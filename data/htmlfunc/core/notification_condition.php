<?php
declare(strict_types = 1);

class htmlfunc_core_notification_condition {

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process(string $html, array $data = array()):string { 	

	// Initialize
	$html_tags = new html_tags();

	// Get values
	if (isset($data['notification_id']) && $row = DB::get_idrow('notifications', $data['notification_id'])) { 
		$condition = unserialize(base64_decode($row['condition_vars']));
		$sender = $row['sender'];
		$recipient = $_row['recipient'];
		$data['controller'] = $row['controller'];
	} elseif (isset($_POST['sender']) && isset($_POST['recipieint']) && isset($_POST['condition_vars'])) { 
		$condition = unserialize(base64_decode($_POST['condition_vars']));
		$sender = $_POST['sender'];
		$recipient = $_POST['recipient'];
	} else { 
		list($sender, $recipient, $condition) = array('', '', array());
	}


	// Load component
	if (!$client = load_component('controller', $data['controller'], 'core', 'notifications')) { return "<b>ERROR:</b> The notification controller '$data[controller]' does not exist."; }

	// Create admin options
	$admin = new admin();
	$admin_options = $admin->create_select_options(0, true);
	
	// Sender option
	$sender_options = '';
	foreach ($client->senders as $key => $sender_name) { 
		if ($key == 'admin' && preg_match("/admin:(\d+)/", $sender, $match)) { $html .= $admin->create_select_options($match[1], true); }
		elseif ($key == 'admin') { $sender_options .= $admin_options; }
		else { 
			$chk = $key == $sender ? 'selected="selected"' : '';
			$sender_options .= "<option value=\"$key\" $chk>$sender_name</option>";
		}
	}

// Recipient options
	$recipient_options = '';
	foreach ($client->recipients as $key => $recipientr_name) { 
		if ($key == 'admin' && preg_match("/admin:(\d+)/", $recipient, $match)) { $html .= $admin->create_select_options($match[1], true); }
		elseif ($key == 'admin') { $recipient_options .= $admin_options; }
		else { 
			$chk = $key == $recipieint ? 'selected="selected"' : '';
			$recipient_options .= "<option value=\"$key\" $chk>$sender_name</option>";
		}
	}

	// Start HTML
	$html = $html_tags->ft_select(array('name' => 'sender_' . $data['controller'], 'label' => 'Sender'), $sender_options);
	$html .= $html_tags->ft_select(array('name' => 'recipient_' . $data['controller'], 'label' => 'Recipient'), $recipient_options);
	$html .= $html_tags->ft_seperator(array('label' => 'Condition Information'));

	// Get conditional HTML
	foreach ($client->fields as $field_name => $vars) {
		$vars['name'] = 'cond_' . $data['controller'] . '_' . $field_name;
		$func_name = 'ft_' . $vars['field'];
		$vars['selected'] = isset($condition[$field_name]) ? $condition[$field_name] : '';
		$html .= $html_tags->$func_name($vars);
	}

	// Add submit button
	$display = 'visible';
	if (isset($data['submit']) && $data['submit'] == 'edit') { 
		$html .= $html_tags->ft_submit(array('value' => 'edit_notification', 'label' => 'Edit E-Mail Notification'));
	} elseif (isset($data['submit']) && $data['submit'] == 'create') { 
		$display = 'hidden'; 
		$html .= $html_tags->ft_submit(array('value' => 'create_notification', 'label' => 'Create E-Mail Notification'));
	}

	// Return
	$html = $html_tags->form_table(array('display' => $display), $html);
	return $html;

}
}

?>
