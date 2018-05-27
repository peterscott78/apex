<?php

class pkg_core extends Package {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() {

	// Set package variables
	$this->pkg_version = '1.0.0';
	$this->pkg_name = 'Core Framework';
	$this->pkg_description = 'The core package of the framework, and is required for all installations of the software.';
	$this->author_name = 'Envrin Group';
	$this->author_email = 'support@envrin.com';
	$this->author_url = 'https://envrin.com/';

	// Config variables
	$this->config = array(
		'theme_admin' => 'admin_default', 
		'theme_public' => 'public_default', 
		'site_name' => 'My Company Name', 
		'session_expire_mins_admin' => 60,  
		'session_expire_mins_user' => 60,  
		'session_retain_logs_admin' => '', 
		'session_retain_logs_user' => '', 
		'password_retries_allowed_admin' => 5, 
		'require_2fa_admin' => 0, 
		'num_security_questions_admin' => 3
	);

	// Hash - Secondary secure questions
	$this->hash = array();
	$this->hash['secondary_security_questions'] = ARRAy(
		'q1' => "What was your childhood nickname?", 
		'q2' => "In what city did you meet your spouse/significant other?", 
		'q3' => "What is the name of your favorite childhood friend?", 
		'q4' => "What street did you live on in third grade?", 
		'q5' => "What is your oldest sibling?s birthday month and year? (e.g., January 1900)", 
		'q6' => "What is the middle name of your oldest child?", 
		'q7' => "What is your oldest siblings middle name?", 
		'q8' => "What school did you attend for sixth grade?", 
		'q9' => "What was your childhood phone number including area code? (e.g., 000-000-0000)", 
		'q10' => "What is your oldest cousins first and last name?", 
		'q11' => "What was the name of your first stuffed animal?", 
		'q12' => "In what city or town did your mother and father meet?", 
		'q13' => "Where were you when you had your first kiss?", 
		'q14' => "What is the first name of the boy or girl that you first kissed?", 
		'q15' => "What was the last name of your third grade teacher?", 
		'q16' => "In what city does your nearest sibling live?", 
		'q17' => "What is your oldest brothers birthday month and year? (e.g., January 1900)", 
		'q18' => "What is your maternal grandmothers maiden name?", 
		'q19' => "In what city or town was your first job?", 
		'q20' => "What is the name of the place your wedding reception was held?", 
		'q21' => "What is the name of a college you applied to but didnt attend?" 
	);

	// Hash - system notification actions
	$this->hash['notify_system_actions'] = array(
		'2fa_admin' => '2FA - Administrator', 
		'2fa_user' => '2FA - Member'
	);

	// Hash - notification content type
	$this->hash['notification_content_type'] = array(
		'text/plain' => 'Plain Text', 
		'text/html' => 'HTML'
	);

	// Controllers
	$this->controllers = array(
		'core/http_requests/admin.php', 
		'core/http_requests/default.php', 
		'core/notifications/system.php'
	);

// Define public site menus
	$public_menus = array(
		'index'=> 'Home', 
		'about' => 'About Us', 
		'services' => 'Services', 
		'register' => 'Sign Up', 
		'login' => 'Login'
	);

	// Define admin menus
	$admin_menus = array(
		'hdr)setup' => array('name' => 'Setup', 'type' => 'header'), 
		'settings' => array('type' => 'parent', 'icon' => 'fa fa-fw fa-cog', 'submenu' => array(
			'general' => 'General', 
			'admin' => 'Administrators', 
			'notifications' => 'Notifications'
		)), 
		'maintenance' => array('type' => 'parent', 'icon' => 'fa fa-fw fa-wrench', 'submenu' => array( 
			'package_manager' => 'Package Manager', 
			'theme_manager' => 'Theme Manager', 
			'backup_manager' => 'Backup Manager', 
			'cron_manager' => 'Cron Manager', 
			'log_manager' => 'Log Manager', 
			'system_check' => 'System Check'
		))
	);

	// Add menus
	$this->menus = array(
		array('public', 'top', $public_menus), 
		array('admin', 'top', $admin_menus)
	);



	// Templates
	$this->templates = array(
		'admin/404', 
		'admin/500', 
		'admin/create_first_admin', 
		'admin/index', 
		'admin/login', 
		'admin/logout', 
		'admin/security_question',
		'admin/settings/notifications', 
		'admin/settings/notifications_create', 
		'public/404', 
		'public/500', 
		'public/index'
	);

	// External files
	$this->ext_files = array(
		'data/libstd/*'
	);


}

////////////////////////////////////////////////////////////
// Install Before
////////////////////////////////////////////////////////////

public function install() {  }

////////////////////////////////////////////////////////////
// Install After
////////////////////////////////////////////////////////////

public function install_after() { } 


////////////////////////////////////////////////////////////
// Reset
////////////////////////////////////////////////////////////

public function reset() { } 


////////////////////////////////////////////////////////////
// Remove
////////////////////////////////////////////////////////////

public function remove() { }



}

?>
