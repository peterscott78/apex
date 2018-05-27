<?php

class User {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct($userid = 0) { 
	$this->userid = $userid;
}

////////////////////////////////////////////////////////////
// Create
////////////////////////////////////////////////////////////

public function create() {

	// Initialize
	global $config, $template;

	// Validate profile
	$this->validate();

	// Get group ID
	if (!isset($_POST['group_id'])) { $_POST['group_id'] = $config['users_default_group']; }
	if ($_POST['group_id'] == 0) { 
		$count = DB::get_field("SELECT count(*) FROM users_groups");
		if ($count == 1) { $_POST['group_id'] = DB::get_field("SELECT id FROM users_groups"); }
	}
	if ($_POST['group_id'] == 0) { $template->add_message("No user group defined, and either no or more than one user groups exist.", 'error'); } 		

	// Check for errors
	if ($template->has_errors == 1) { return false; }

	// Add to database
	DB::insert('users', array(
		'username' => $POST['username'], 
		'group_id' => $_POST['group_id'])
	);
	$this->userid = DB::insert_id();

	// Update password
	$enc = new encrypt();
	$password = $enc->get_password_hash($_POST['password'], $this->userid);
	DB::update('users', array('password' => $password), "id = %i", $this->userid);

	// Gather profile
	$profile = array('id' => $this->userid);
	$result = DB::query("SELECT id,is_required,allow_duplicates,alias,display_name FROM users_profile_fields ORDER BY order_num");
	while ($row = DB::fetch_assoc($result)) { 
		 $profile[$row['alias']] = $_POST[$row['alias']] ?? '';
	}
	$profile['about_me'] = $_POST['about_me'] ?? '';

	// Insert additional profile
	DB::insert('users_profile', $profile);

	// Add avatar, if needed
	if (isset($_FILES['avatar']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) { 
		$image = new Image();
		$image->upload('avatar', 'users_avatar', $this->userid);
	}

	// Return
	return $this->userid;

}


////////////////////////////////////////////////////////////
// Validate profile
////////////////////////////////////////////////////////////

public function validate() { 

	// Initialize
	global $config, $template;

	// Check username
	if (isset($_POST['username'])) {
		$_POST['username'] = strtolower($_POST['username']); 
		if ($_POST['username'] == '') { $template->add_message("You did not specify a username.", 'error'); }

		if ($config['username_column'] == 'email') { 
			if (!filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)) { $template->add_message("You must submit a valid e-mail address."); }
			$_POST['email'] = $_POST['username'];

		} elseif (preg_match("/[\s\W]/", $_POST['username'])) { $template->add_message("Username can not contian spaces or special characters.", 'error'); }

		// Check for duplicate
		if ($new_user === true) { 
			$exists = DB::get_field("SELECT count(*) FROM users WHERE username = %s", $_POST['username']);
			if ($exists > 0) { $template->add_message("Username already exists, $_POST[username].  Please try a new username.", 'error'); }
		}


	}

	// Go through profile fields
	$result = DB::query("SELECT * FROM users_profile_fields ORDER BY order_num");
	while ($row = DB::fetch_assoc($result)) { 
		$value = $_POST[$row['alias']] ?? '';
		if ($row['is_required'] == 1 && $value == '') { $template->add_message("The %s field is required.", 'error', $row['display_name']); }

		// Check for duplicates
		if ($row['allow_duplicates'] == 1 && $value != '') { 
			$count = DB::get_field("SELECT count(*) FROM users_profile WHERE $row[alias] = %s", $value);
			if ($count > 0 && $new_user === true) { $template->add_message("Another member already has the same value for the %s field, and this field does not allow duplicates", 'error', $row['display_name']); }
		}
	}



}

}

?>
