<?php
declare(strict_types = 1);

class ajax_core_search_table Extends Ajax { 

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process() { 

	// Set variables
	$package = $_POST['package'] ?? '';
	$search_text = $_POST['search_' . $_POST['id']] ?? '';
	if ($search_text == '') { 
		$this->alert(tr('You did not specify any text to search for.'));
		return;
	}

	// Load table
	$table = load_component('table', $_POST['table'], $package, '', $_POST);
	// Get table details
	$details = get_table_details($table, $_POST['id']);

	// Clear table rows
	$this->clear_table($_POST['id']);

	// Add new rows
	$this->add_data_rows($_POST['id'], $_POST['table'], $package, $details['rows'], $_POST);

	// Set pagination
	$this->set_pagination($_POST['id'], $details);

}

}

?>
