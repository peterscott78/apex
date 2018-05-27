<?php
declare(strict_types = 1);

class ajax_core_delete_rows extends Ajax { 

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process() { 

	// Set variables
	$package = $_POST['package'] ?? '';
	$dbtable = $_POST['dbtable'] ?? $_POST['table'];
	$dbcolumn = $_POST['dbcolumn'] ?? 'id';

	// Load table
	$table = load_component('table', $_POST['table'], $package, '', $_POST);

	// Get IDs
	$form_name = preg_replace("/\[\]$/", "", $table->form_name);
	$ids = get_chk($form_name);

	// Delete
	foreach ($ids as $id) { 
		if ($id == '') { continue; }
		DB::query("DELETE FROM $dbtable WHERE $dbcolumn = %s", $id);
	}

	// AJAX
	$this->remove_checked_rows($_POST['id']);

}

}

?>
