<?php

class Notification {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() { 


}

////////////////////////////////////////////////////////////
// Create notification
////////////////////////////////////////////////////////////

public function create($data = array()) { 

	// Perform checks
	if (!isset($data['controller'])) { trigger_error("No 'controller' variable defined when creating e-mail notification.", E_USER_ERROR); } 
	elseif (!isset($data['sender_' . $data['controller']])) { trigger_error("No 'sender' variable defined while trying to create e-mail notification.", E_USER_ERROR); }
	elseif (!isset($data['recipient_' . $data['controller']])) { trigger_error("No 'recipient' variable defined while creating e-mail notification.", E_USER_ERROR); }

	// Load controller
	if (!$client = load_component('controller', $data['controller'], 'core', 'notifications')) {
		trigger_error("Notification controller '$data[controller]' does not exist.", E_USER_ERROR);
	}

	// Set variables
	$sender = $data['sender_' . $data['controller']];
	$recipient = $data['recipient_' . $data['controller']];
	$content_type = isset($data['content_type']) ? $data['content_type'] : 'text/plain';

	// Get condition
	$condition = array();
	foreach ($client ->fields as $field_name => $vars) { 
		$condition[$field_name] = $_POST['cond_' . $data['controller'] . '_' . $field_name];
	}

	// Add to DB
	DB::insert('notifications', array(
		'controller' => $data['controller'], 
		'sender' => $sender, 
		'recipient' => $recipient, 
		'content_type' => $content_type, 
		'subject' => $data['subject'], 
		'contents' => base64_encode($data['contents']), 
		'condition_vars' => base64_encode($condition))
	);
	$notification_id = DB::insert_id();

	// Add attachments as needed
	$x=1;
	while (1) { 
		if (!list($filename, $mime_type, $contents) = get_uploaded_file('attachment' . $x)) { break; }

		// Add to DB
		DB::insert('notifications_attachments', array(
			'notification_id' => $notification_id, 
		'mime_type' => $mime_type, 
			'filename' => $filename, 
			'contents' => base64_encode($contents))
		);

	$x++; }

	// Return
	return $notification_id;

}




////////////////////////////////////////////////////////////
// Update notification
////////////////////////////////////////////////////////////

public function update($notification_id, $data = array()) { 

	// Set updates array
	$updates = array();
	if (isset($data['content_type'])) { $updates['content_type'] = $data['content_type']; }
	if (isset($data['sender'])) { $updates['sender'] = $data['sender']; }
	if (isset($data['recipient'])) { $updates['recipient'] = $data['recipient']; }
	if (isset($data['subject'])) { $updates['subject'] = $data['subject']; }
	if (isset($data['contents'])) { $updates['contents'] = base64_encode($data['contents']); }

	// Update DB
	DFB::update('notifications', $updates, "id = %i", $notification_id);

	// Upload attachments

	// Add attachments as needed
	$x=1;
	while (1) { 
		if (!list($filename, $mime_type, $contents) = get_uploaded_file('attachment' . $x)) { break; }

		// Add to DB
		DB::insert('notifications_attachments', array(
			'notification_id' => $notification_id, 
		'mime_type' => $mime_type, 
			'filename' => $filename, 
			'contents' => base64_encode($contents))
		);

	$x++; }

	// Return
	return true;

}

////////////////////////////////////////////////////////////
// Delete
////////////////////////////////////////////////////////////

public function delete($notification_id) { 

	DB::query("DELETE FROM notifications WHERE id = %i", $notification_id);
	return true;

}


}


?>



}

?>
