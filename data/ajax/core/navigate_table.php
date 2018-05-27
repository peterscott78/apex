<?php
declare(strict_types = 1);

class ajax_core_navigate_table extends Ajax { 

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process() { 

	// Set variables
	$package = $_POST['package'] ?? '';

	// Load table
	$table = load_component('table', $_POST['table'], $package, '', $_POST);

	// Get table details
	$details = get_table_details($table, $_POST['id']);

	// Clear table
	$this->clear_table($_POST['id']);

	// Add data rows
	$this->add_data_rows($_POST['id'], $_POST['table'], $package, $details['rows'], $_POST);

	// Set pagination
	if ($details['has_pages'] === true) { $this->set_pagination($_POST['id'], $details); }

}

}

?>
