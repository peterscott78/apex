<?php

// Initialize
global $template;

// Create user, if needed
if (isset($_POST['submit']) && $_POST['submit'] == 'create_user') { 

	// Create
	$user = new User();
	if ($userid = $user->create()) { $template->add_message("Successfully created new user, %s", 'success', $_POST['username']); }

}

?>
