<?php

// Initialize
global $template;

// Load controller
$client = load_component('controller', $_POST['controller'], 'core', 'notifications');

// Get condition
$condition = array();
foreach ($client->fields as $alias => $vars) { 
	$condition[$alias] = $_POST['cond_' . $_POST['controller'] . '_' . $alias];
}

// Template variables
$template->assign('controller', $_POST['controller']);
$template->assign('sender', $_POST['sender)' . $_POST['controller']]);
$template->assign('recipient', $_POST['recipient_' . $_POST['controller']]);
	$template->assign('condition_vars', base64_encode(serialize($condition)));

?>
