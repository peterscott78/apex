<?php
declare(strict_types = 1);

class auth {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct(string $auth_type = 'user') {

 	$this->auth_type = $auth_type;
	$this->table_name = $auth_type == 'admin' ? 'admin_auth_sessions' : 'users_auth_sessions';
	


}
////////////////////////////////////////////////////////////
// Check login
////////////////////////////////////////////////////////////

public function check_login(bool $require_login = false) { 

	// Initialize
	global $config;
	$expire_time = ($config['session_expire_mins_' . $this->auth_type] * 60);

	// Check for session
	$cookie = COOKIE_NAME . '_' . $this->auth_type . '_auth_hash';
	if (isset($_COOKIE[$cookie]) && $row = DB::get_row("SELECT * FROM $this->table_name WHERE auth_hash = %s", hash('sha512', $_COOKIE[$cookie]))) {

		// Check for 2FA
		if ($row['2fa_status'] == 0 && $GLOBALS['_panel'] != 'public') { 
			$template = new template('2fa');
			echo $template->parse(); exit(0);
		}

		// Check for inactive session
		if (time() >= ($row['last_active'] + $expire_time)) { 
			$this->invalid_login('expired');
		}

		// Update session
		DB::query("UPDATE $this->table_name SET last_active = %i WHERE id = %i", time(), $row['id']);

		// Return
		$GLOBALS['userid'] = $row['userid'];
		return $row['userid'];

	// Login
	} elseif ((isset($_POST['submit']) && $_POST['submit'] == 'login') || ($_SERVER['REQUEST_METHOD'] == 'POST' && preg_match("/login$/", $_GET['route']))) { 
		$this->login();
	
	// Require login, if needed
	} elseif ($require_login === true) { 
	$template = new template('login');
	echo $template->parse(); exit(0);
	}

}

////////////////////////////////////////////////////////////
// Login
////////////////////////////////////////////////////////////

public function login(bool $auto_login = false) {

	// Initialize
	global $config;
	$login_retries_allowed = $config['password_retries_allowed_' . $this->auth_type];
	$chk_sec_hash = '';

	// Get							 admin / user info
	if ($auto_login === true) { 
		$user_row = array('id' => $GLOBALS['userid']);

	} elseif ($this->auth_type == 'admin') { 
		if (!$user_row = DB::get_row("SELECT * FROM admin WHERE username = %s", $_POST['username'])) { $this->invalid_login('invalid'); }
			$chk_password = $user_row['password'];
			$chk_sec_hash = $user_row['sec_hash'];
			$user_type = 'admin';
	} else {
 
		if (!$user_row = DB::get_row("SELECT * FROM users WHERE $config[username_column] = %s", $_POST['username'])) { $this->invalid_login('invalid'); }
		$chk_password = $user_row['password'];
		$user_type = 'users';
	}

	// Check password
	$enc = new encrypt();
	if ($auto_login === false) { 
		if ($chk_password != $enc->get_password_hash($_POST['password'], $user_row['id'], $user_type)) { 

			// Check # of retries
			if ($login_retries_allowed > 0 && $user_row['invalid_logins'] >= $login_retries_allowed) { 
				DB::query("UPDATE $user_type SET status = 'inactive' WHERE id = $i", $user_row['id']);
			}
			DB::query("UPDATE $user_type SET invalid_logins = invalid_logins + 1 WHERE id = %i", $user_row['id']);

			// Invalid login
			$this->invalid_login('invalid');
		}	

		// Check user status
		if ($user_row['status'] == 'inactive') { $this->invalid_login('inactive'); }

		// Check if 2FA required
		if ($config['require_2fa_' . $this->auth_type] == 2) { $require_2fa = $user_row['require_2fa']; }
		else { $require_2fa = $config['require_2fa_' . $this->auth_type]; }
		$status_2fa = $require_2fa == 1 ? 0 : 1;

		// Get 2FA hash
		if ($require_2fa == 1) { 
			$hash_2fa = generate_random_string(36);
			$hash_2fa_enc = hash('sha512', $hash_2fa);
		} else { $hash_2fa_enc = ''; }

		// Check security question
		$this->check_security_question($user_type, $user_row['id'], $chk_sec_hash);

		// Check IP address
		$this->check_ip_restrictions($user_row['id']);

} else { 
		$status_2fa = 1;
		$hash_2fa = '';
		$hash_2fa_enc = '';
		$require_2fa = 0;
	}

	// Generate session ID
	do {
		$session_id = generate_random_string(60);
		$exists = DB::get_field("SELECT count(*) FROM $this->table_name WHERE auth_hash = %s", hash('sha512', $session_id)) ? 1 : 0;
	} while ($exists > 0);

	// Add session to DB
	DB::insert($this->table_name, array(
		'userid' => $user_row['id'], 
		'auth_hash' => hash('sha512', $session_id), 
		'2fa_status' => $status_2fa, 
		'2fa_hash' => $hash_2fa_enc, 
		'last_active' => time())
	);

	// Set cookie
$cookie_name = COOKIE_NAME . '_' . $this->auth_type . '_auth_hash';
	if (!setcookie($cookie_name, $session_id, 0, '/')) { trigger_error("Unable to set login cookie.  Please contact customer suppport for further assistance.", E_USER_ERROR); }

	// Initiate 2FA, if needed
	if ($require_2fa == 1) { $this->initiate_2fa(); }

		// Parse template
	$GLOBALS['userid'] = $user_row['id'];
	$template = new template('index');
	echo $template->parse(); exit(0);
	

}

////////////////////////////////////////////////////////////
// Check secondary question hash
////////////////////////////////////////////////////////////

protected function check_security_question(string $user_type, $userid, string $chk_sec_hash):bool { 

	// Initialize
	$question_table = $user_type . '_security_questions';
	$cookie = COOKIE_NAME . '_' . $this->auth_type . '_auth_sechash';

	// Check for cookie
	if (isset($_COOKIE[$cookie]) && hash('sha512', $_COOKIE[$cookie]) == $chk_sec_hash) { return true; }

	// Check if user has questions
	$count = DB::get_field("SELECT count(*) FROM $question_table WHERE userid = %i", $userid);
	if ($count == 0) { return true; }

	// Check answer, if needed
	$ask_question = true; $invalid_answer = false;
	if (isset($_POST['answer']) && isset($_POST['question_id'])) { 

		// Get requestion
		if ($row = DB::get_row("SELECT * FROM $question_table WHERE userid = %i AND question = %s", $userid, $_POST['question_id'])) { 

			$enc = new encrypt();
			if ($row['answer'] == $enc->get_password_hash($_POST['answer'], $userid, $user_type)) { $ask_question = false; }
			else { $invalid_answer = true; }
		}
	}

	// Ask question, if needed 
	if ($ask_question === true) { 

		// Get random question
	$question_id = DB::get_field("SELECT question FROM $question_table WHERE userid = %i ORDER BY RAND()", $userid);

		// Start template
		$template = new template('security_question');
		if ($invalid_answer === true) { $template->add_message("We're sorry, but your answer to the security question was incorrect.  Please try again.", 'error'); }
		$template->assign('username', $_POST['username']);
		$template->assign('password', $_POST['password']);
		$template->assign('question_id', $question_id);
		$template->assign('question', get_hash_variable('secondary_security_questions', $question_id));
		echo $template->parse();
		exit(0);
	}

	// Set cookie
	$sec_hash = generate_random_string(50);
	setcookie($cookie, $sec_hash, (time() + 2592000));
	DB::update($user_type, array('sec_hash' => hash('sha512', $sec_hash)), "id = %i", $userid);

	// Return
	return true;


}

////////////////////////////////////////////////////////////
// Check IP restrictions
////////////////////////////////////////////////////////////

protected function check_ip_restrictions($userid):bool { 

	// Initialize
	$ip_table = $this->auth_type . '_allowips';

	// Check if IP records exist
	$count = DB::get_field("SELECT count(*) FROM $ip_table WHERE userid = %i", $userid);
	if ($count == '' || $count == 0) { return true; }

	// Check if IP allowed
	if (!$row = DB::get_row("SELECT * FROM $ip_table WHERE userid = $i AND ip_address = %s", $userid, $_SERVER['REMOTE_ADDR'])) { 
		$this->invalid_login();
	}

	// Return
	return true;

}


////////////////////////////////////////////////////////////
// Invalid login
////////////////////////////////////////////////////////////

public function invalid_login(string $type = 'none') { 

	// Logout
	$this->logout();

	// Start template
	$template = new template('login');

	// Add template message
	if ($type == 'invalid') { $template->add_message("Invalid username or password.  Please double check your login credentials and try again.", 'error'); }
	elseif ($type == 'expired') { $template->add_message("Your session has expired due to inactivity.  Please login again.", 'error'); }
	elseif ($type == 'inactive') { $template->add_message("Your account is currentylu inactive, and not not login.  Please contact customer support for further information.", 'error'); }

	// Parse template
	echo $template->parse(); 
	exit(0);

}

////////////////////////////////////////////////////////////
// Logout
////////////////////////////////////////////////////////////

public function logout():bool { 

	// Set variables
	$cookie = COOKIE_NAME . '_' . $this->auth_type . '_auth_hash';
	if (!isset($_COOKIE[$cookie])) { return true; }

	// Delete secondary cookie
	$sec_cookie = COOKIE_NAME . '_' . $this->auth_type . '_auth_sechash';
	if (isset($_COOKIE[$sec_cookie])) { unset($_COOKIE[$sec_cookie]); }

	// Delete session
	DB::query("DELETE FROM $this->table_name WHERE auth_hash = %s", hash('sha512', $_COOKIE[$cookie]));
	DB::query("UPDATE $this->auth_type SET sec_hash = '' WHERE id = $GLOBALS[userid]");
	unset($_COOKIE[$cookie]);
	$GLOBALS['userid'] = 0;

	// Return
	return true;

}

}


	

?>
