<?php

class form_core_notification {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct($data = array()) { 

	// Get controller
	if (isset($data['notification_id']) && $data['notification'] > 00) { 
		$controller = DB::get_field("SELECT controller FROM notifications WHERE id = $i", $data['notification_id']);
	} else { $controller = $data['controller']; }

	// Load controller
	if (!$form = load_component('controller', $controller, 'core', 'notifications', $data)) {
		trigger_error("The controller '$controller' does not exist within the notifications", E_USER_ERROR);
	}

	// Get merge fields
	$fields = $form->get_merge_fields();
	$field_options = '';
	foreach ($fields as $type => $vars) { 
		$field_options .= "<option value=\"\">$type</option>";

		foreach ($vars as $key => $value) { 
			$field_options .= "<option value=\"$key\">        $value</option>";
		}
	}

	// Define form fields
	$this->form_fields = array(
		'content_type' => array('field' => 'select', 'data_source' => 'hash:notification_content_type'), 
		'subject' => array('field' => 'textbox', 'label' => 'Subject'),
		'attachment1' => array('field' => 'textbox', 'type' => 'file', 'label' => 'Attachment'), 
		'merge_vars' => array('field' => 'custom', 'label' => 'Merge Variables', 'contents' => '<select name="merge_vars" id="merge_vars">' . $field_options . '</select> <a href="javascript:addMergeVar();" class="btn btn-primary btn-md">Add</a>'), 
		'contents' => array('field' => 'textarea', 'label' => 'Message Contents', 'size' => '600px,300px', 'placeholder', 'Enter your message contents'), 
		'submit' => array('field' => 'submit', 'value' => 'create_notification', 'label' => 'Create E-Mail Notification')
	);

}

}

?>
