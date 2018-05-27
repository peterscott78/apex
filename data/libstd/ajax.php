<?php
declare(strict_types = 1);

class Ajax {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() { 

	// Initialize
	$this->results = array();

}

////////////////////////////////////////////////////////////
// Add to results
////////////////////////////////////////////////////////////

protected function add(string $action, array $vars):bool { 
	$vars['action'] = $action;
	array_push($this->results, $vars);
	return true;
}

////////////////////////////////////////////////////////////
// Alert
////////////////////////////////////////////////////////////

public function alert(string $message) { 
	$this->add('alert', array('message' => $message));
}

////////////////////////////////////////////////////////////
// Clear table rows
////////////////////////////////////////////////////////////

public function clear_table(string $divid) { 
	$this->add('clear_table', array('divid' => $divid));
}

////////////////////////////////////////////////////////////
// Remove checked table rows
////////////////////////////////////////////////////////////

public function remove_checked_rows(string $divid) { 
	$this->add('remove_checked_rows', array('divid' => $divid));
}

////////////////////////////////////////////////////////////
// Add table data row
////////////////////////////////////////////////////////////

public function add_data_rows(string $divid, string $table_alias, string $package, array $rows, array $data = array()) { 

	// Load table
	$table = load_component('table', $table_alias, $package, '', $data);

	if (isset($table->form_field) && $table->form_field == 'checkbox' && !preg_match("/\[\]$/", $table->form_name)) { 
		$table->form_name .= '[]';
	}

	// Go through rows
	foreach ($rows as $row) { 

		// Add radio / checkbox, if needed
		$frow = array();
		if (isset($table->form_field) && ($table->form_field == 'radio' || $table->form_field == 'checkbox')) { 
			$frow[] = "<center><input type=\"$table->form_field\" name=\"$table->form_name\" value=\"" . $row[$table->form_value] . "\"></center>";
		}

		// Go through table columns
		foreach ($table->columns as $alias => $name) { 
			$value = $row[$alias] ?? '';
			$frow[] = $value;
		}

		// AJAX
		$this->add('add_data_row', array('divid' => $divid, 'cells' => $frow));
	}

}

////////////////////////////////////////////////////////////
// Set pagination
////////////////////////////////////////////////////////////

public function set_pagination(string $divid, array $details) { 

	// Get nav function 
	$vars = $_POST;
	unset($vars['page']);
	$nav_func = "<a href=\"javascript:ajax_send('core/navigate_table', '" . http_build_query($vars) . "&page=~page~', 'none');\">";

	// Set AJAX
	$this->add('set_pagination', array(
		'divid' => $divid, 
		'start' => $details['start'], 
		'total' => $details['total'], 
		'page' => $details['page'], 
		'start_page' => $details['start_page'], 
		'end_page' => $details['end_page'], 
		'rows_per_page' => $details['rows_per_page'], 
		'total_pages' => $details['total_pages'], 
		'nav_func' => $nav_func)
	);

}

}

?>
