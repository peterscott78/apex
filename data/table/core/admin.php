<?php
declare(strict_types = 1);

class table_core_admin {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct(array $data = array()) { 

	// Set variables
	$this->delete_button = tr('Delete Checked Administrators');
	$this->rows_per_page = 25;

	// Form field
	$this->form_field = 'checkbox';
	$this->form_name = 'admin_id';
	$this->form_value = 'id';

	// Set columns
	$this->columns = array(
		'id_html' => 'ID', 
		'username' => 'Username', 
		'full_name' => 'Full Name', 
		'last_seen' => 'Last Seen', 
		'manage' => 'Manage'
	);

	// Sortable columns
	$this->sortable = array('username', 'full_name', 'last_seen');

}


////////////////////////////////////////////////////////////
// Get total
////////////////////////////////////////////////////////////

public function get_total(string $search_text = '') { 
	$total = DB::get_field("SELECT count(*) FROM admin");
	return $total;
}

////////////////////////////////////////////////////////////
// Get rows
////////////////////////////////////////////////////////////

public function get_rows($start = 0, $search_text = '', $order_by = 'id asc') { 

	// Get rows
	$results = array();
	$result = DB::query("SELECT * FROM admin ORDER BY $order_by LIMIT $start,$this->rows_per_page");
	while ($row = DB::fetch_assoc($result)) { 
		array_push($results, $this->format_row($row));
	}

	// Return
	return $results;

}


////////////////////////////////////////////////////////////
// Format row
////////////////////////////////////////////////////////////

public function format_row(array $row) { 

	// Set variables
	$row['id_html'] = '<center>' . $row['id'] . '</center>';
	$row['full_name'] .= ' (' . $row['email'] . ')';
	$row['manage'] = '<center><a href="' . SITE_URI . '/admin/settings/admin_manage?admin_id=' . $row['id'] . '" class="btn btn-primary btn-md">Manage</a></center>';

	// Return
	return $row;

}

}

?>
