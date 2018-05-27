<?php

class pkg_users {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() {

	// Config variables
	$this->config = array(
		'username_column' => 'username', 
		'require_2fa_user' => 2,
		'users_default_group' => 1,  
		'users_enable_public_profiles' => 0, 
		'users_enable_avatar' => 0, 
		'users_enable_about_me' => 0
	);

	// Define admin menus
	$admin_menus = array(
		'hdr_accounts' => array('name' => 'Accounts', 'link_type' => 'header'),
		'users' => array('name' => 'Users', 'icon' => 'fa fa-fw fa-users', 'link_type' => 'parent', 'submenu' => array(  
			'create' => 'Create New User', 
			'manage' => 'Manage User',
			'delete' => 'Delete User'
		))
	);

	// Add menus
	$this->menus = array(
		array('admin', 'after hdr_setup', $admin_menus)
	); 






}


}

?>
