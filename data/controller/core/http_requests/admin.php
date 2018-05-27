<?php

class controller_core_http_requests_admin {


////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process() {

	// Initializw
	global $config;
	$GLOBALS['_panel'] = 'admin';
	$GLOBALS['_theme'] = $config['theme_admin'];

	//  Check if admin exists
	$count = DB::get_field("SELECT count(*) FROM admin");
	if (isset($_POST['submit']) && $_POST['submit'] == 'create_first_admin' && $count == 0) {
		$client = new admin();
		$GLOBALS['userid'] = $client->create(); 

		$auth = new auth('admin');
		$auth->login($true);
		exit(0);

	} elseif ($count == 0) { 
		$template = new template('create_first_admin');
		echo $template->parse(); exit(0);
	}

	// Check auth
	$auth = new auth('admin');
	$auth->check_login(true);

	// Parse template
	global $template;
	$template = new template();
	echo $template->parse(); exit(0);

}

}

?>
