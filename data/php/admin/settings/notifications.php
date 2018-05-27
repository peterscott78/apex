<?php

// Initialize
global $template;

// Create notification
if (isset($_POST['submit']) && $_POST['submit'] == 'create_notification') { 

	// Create
	$client = new Notification();
	$client->create($_POST);

	// Add message
	$template->add_message("Successfully added new e-mail notification, %s", 'success', $_POST['subject']);

}


// Go through controllers
$controllers = array();
$controller_options = '<option value="">--------------------</option>';
$aliases = DB::get_column("SELECT alias FROM internal_components WHERE type = 'controller' AND package = 'core' AND parent = 'notifications' ORDER BY alias");
foreach ($aliases as $alias) { 
	$client = load_component('controller', $alias, 'core', 'notifications');

	// Add to options
	$name = isset($client->display_name) ? $client->display_name : $alias;
	$controller_options .= "<option value=\"$alias\">$name</option>";

	// Add to array
	$controllers[] = array('alias' => $alias);
}

// Template variables
$template->assign('controllers', $controllers);
$template->assign('controller_options', $controller_options);

?>

?>
