<?php

class table_core_notifications { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct($data = array()) { 

	// Set variables
	$this->has_search = 1;
	$this->rows_per_page = 5;
	$this->delete_button = tr('Delete Checked Notifications');

	// Set form field
	$this->form_field = 'checkbox';
	$this->form_name = 'notification_id';
	$this->form_value = 'id_raw';
	
	// Set columns
	$this->columns = array(
	'id' => 'ID', 
	'controller' => 'Type', 
	'recipient' => 'Recipient', 
	'subject' => 'Subject', 
	'manage' => 'Manage'
	);

	// Sortable columns
	$this->sortable = array('id', 'controller','recipient','subject');

}

////////////////////////////////////////////////////////////
// Get total
////////////////////////////////////////////////////////////

public function get_total(string $search_text = '') { 

	// Get total
	if ($search_text != '') { 
		$total = DB::get_field("SELECT count(*) FROM notifications WHERE subject LIKE %ls", $search_text);

	} else { 
		$total = DB::get_field("SELECT count(*) FROM notifications");
	}


	// Return
	return $total;

}

////////////////////////////////////////////////////////////
// Get rows
////////////////////////////////////////////////////////////

public function get_rows(int $start = 0, string $search_text = '',string $order_by = 'id') { 

	// Get SQL
	if ($search_text != '') { 
		$result = DB::query("SELECT id,controller,recipient,subject FROM notifications WHERE subject LIKE %ls ORDER BY $order_by LIMIT $start,$this->rows_per_page", $search_text);
	} else { 
		$result = DB::query("SELECT id,controller,recipient,subject FROM notifications ORDER BY $order_by LIMIT $start,$this->rows_per_page");
	}

	// Get rows
$results = array();
	while ($row = DB::fetch_assoc($result)) { 
		array_push($results, $this->format_row($row));
	}

	// Return
	return $results;

}

////////////////////////////////////////////////////////////
// Format row
////////////////////////////////////////////////////////////

protected function format_row($row) { 

	// Load controller
	$controller = load_component('controller', $row['controller'], 'core', 'notifications'); 

	// Format row
	$row['id_raw'] = $row['id'];
	$row['controller'] = $controller->display_name ?? 'Unknown';
	$row['recipient'] = method_exists($controller, 'get_recipient_name') === true ? $controller->get_recipient_name($row['recipient']) : $row['recipient'];
	$row['manage'] = "<center><a href=\"" . SITE_URI . "/admin/settings/notifications_edit?notification_id=$row[id]\" class=\"btn btn-primary btn-md\">Manage</a></center>";
	$row['id'] = '<center>' . $row['id'] . '</center>';
	return $row;
}

}

?>
