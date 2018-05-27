<?php

class pkg_devkit extends Package {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() {

	// Set package variables
	$this->pkg_version = '1.0.0';
	$this->pkg_name = 'Development Toolkit';
	$this->pkg_description = 'The development toolkit, which allows developers to to create and manage new packages, plus start and maintain a repository.';
	$this->author_name = 'Envrin Group';
	$this->author_email = 'support@envrin.com';
	$this->author_url = 'https://envrin.com/';


	// Config variables
	$this->config = array(
		'repo_enabled' => 0, 
		'repo_host' => '', 
		'repo_name' => '', 
		'repo_tagline' => '', 
		'repo_email' => '', 
		'repo_url' => ''
	);

	// Define admin menus
	$admin_menus = array(
		'hdr_devkit' => array('name' => 'Development', 'link_type' => 'header'), 
		'devkit' => array('name' => 'Devel Kit', 'icon' => 'fa fa-fw fa-hash', 'link_type' => 'parent', 'submenu' => array( 
			'packages' => 'Packages', 
			'themes' => 'Themes', 
			'repository' => 'Repository'
		))
	);

// Add menus
	$this->menus = array(
		array('admin', 'bottom', $admin_menus)
	);

}

}

?>
