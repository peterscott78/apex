<?php

class controller_core_notifications_system { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() { 

	// Set variables
	$this->display_name = 'System Notifications';

	// Set fields
	$this->fields = array(
		'action' => array('field' => 'select', 'data_source' => 'hash:notify_system_actions', 'label' => 'Action')
	);
 

	// Senders
	$this->senders = array(
		'admin' => 'Administrator' 
	);

	// Recipients
	$this->recipients = array(
		'user' => 'User'		
	);


}

////////////////////////////////////////////////////////////
// Get sender name
////////////////////////////////////////////////////////////

public function get_sender_name(string $sender):string { 

	$sender_name = 'Unknown';
	if (preg_match("/^admin:(\d+)/", $sender, $match)) { 

		if ($row = DB::get_idrow('admin', $match[1])) { 
		$sender_name = 'Admin: ' . $row['full_name'] . ' (' . $row['username'] . ')';
		}
	}

	// Return
	return $sender_name;

}

////////////////////////////////////////////////////////////
// Get recipient name
////////////////////////////////////////////////////////////

public function get_recipient_name(string $recipient):string { 
	return 'User';
}

////////////////////////////////////////////////////////////
// Get merge fields
////////////////////////////////////////////////////////////
public function get_merge_fields():array { 

	// Set fields
	$fields = array(
		'Profile' => array(
			'username' => 'Username', 
			'full_name' => 'Full Name', 
			'email' => 'E-Mail'
		), 
		'2FA Variablies' => array(
			'2fa-url' => 'URL', 
			'2fa-ip_address' => 'IP Address', 
			'2fa-user_agent' => 'User Agent'
		)
	);

	// Return
	return $fields;
}

}

?>
