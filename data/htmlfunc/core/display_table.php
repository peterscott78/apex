<?php
declare(strict_types = 1);

class htmlfunc_core_display_table {

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process(string $html, array $data = array()):string { 

	// Perform checks
	if (!isset($data['table'])) { return "<b>ERROR:</b> No 'table' attribute exists within the e:function tag to display a data table."; }

	// Set variables
	$package = $data['package'] ?? '';
	$page = $_REQUEST['page'] ?? 1;
	$id = $data['id'] ?? 'tbl_' . $data['table'];

	// Load component
	if (!$table = load_component('table', $data['table'], $package, '', $data)) { 
		return "<B>ERROR:</b> The table component '$data[table] does not exist.";
	}

	// Get AJAX data
	$ajaxdata_vars = $data;
	$ajaxdata_vars['id'] = $id;
	unset($ajaxdata_vars['alias']);
	$ajaxdata = http_build_query($ajaxdata_vars);

	// Get table details
	$details = get_table_details($table, $id);

	// Get total columns
	$total_columns = count($table->columns);
	if (isset($table->form_field) && ($table->form_field == 'radio' || $table->form_field == 'checkbox')) { $total_columns++; }

	// Start data table
	$tpl_code = "<e:data_table id=\"$id\"><thead>\n";

	// Add search bar to TPL code, if needed
	if (isset($table->has_search) && $table->has_search == 1) {
		$tpl_code .= "<tr>\n\t<td colspan=\"$total_columns\" align=\"right\">\n"; 
		$tpl_code .= "\t\t<e:table_search_bar table=\"$data[table]\" id=\"$id\" ajaxdata=\"$ajaxdata\">\n"; 
		$tpl_code .= "\t</td>\n</tr>";
	}

	$tpl_code .= "<tr>\n";
	if (isset($table->form_field) && $table->form_field == 'checkbox') { 
		$tpl_code .= "\t<th><input type=\"checkbox\" name=\"check_all\" value=\"1\" onclick=\"tbl_check_all(this, '$id');\"></th>\n";
		if (!preg_match("/\[\]$/", $table->form_name)) { $table->form_name .= '[]'; }
	} elseif (isset($table->form_field) && $table->form_field == 'radio') { 
		$tpl_code .= "\t<th>&nbsp;</th>\n";
	}
	
	// Add theader columns
	foreach ($table->columns as $alias => $name) { 
		if (is_array($table->sortable) && in_array($alias, $table->sortable)) { 
			$sort_asc = "<a href=\"javascript:ajax_send('core/sort_table', '" . $ajaxdata . "&sort_col=" . $alias . "&sort_dir=asc', 'none');\" border=\"0\"><i class=\"fa fa-sort-asc\"></i></a> ";
			$sort_desc = " <a href=\"javascript:ajax_send('core/sort_table', '" . $ajaxdata . "&sort_col=" . $alias . "&sort_dir=desc', 'none');\" border=\"0\"><i class=\"fa fa-sort-desc\"></i></a>";
	} else { list($sort_asc, $sort_desc) = array('', ''); }

		$tpl_code .= "\t<th>" . $sort_asc . $name . $sort_desc . "</th>\n";
	}
	$tpl_code .= "</tr></thead><tbody id=\"" . $id . "_tbody\">\n\n";

	// Go through table rows
	foreach ($details['rows'] as $row) { 
		$tpl_code .= "<tr> `\n";

		// Add form field, if needed
		if (isset($table->form_field) && ($table->form_field == 'radio' || $table->form_field == 'checkbox')) { 
			$tpl_code .= "\t<td align=\"center\"><input type=\"$table->form_field\" name=\"$table->form_name\" value=\"" . $row[$table->form_value] . "\"></td>";
		}


		// Go through columns
		foreach ($table->columns as $alias => $name) { 
			$value = isset($row[$alias]) ? $row[$alias] : '';
			$tpl_code .= "\t<td>$value</td>\n";
		}
		$tpl_code .= "</tr>";
	}

	// Finish table
	$tpl_code .= "</tbody><tfoot><tr><td colspan=\"$total_columns\" align=\"right\">\n";

	// Delete button
	if (isset($table->delete_button) && $table->delete_button != '') { 

		// Set variables
		$dbtable = $table->delete_dbtable ?? $data['table'];
		$dbcolumn = $table->delete_dbcolumn ?? 'id';
		$delete_data = $ajaxdata . '&dbtable=' . $dbtable . '&dbcolumn=' . $dbcolumn;
		$form_name = preg_match("/\[\]$/", $table->form_name) ? $table->form_name : $table_form_name . '[]';

		// Add HTML
		$tpl_code .= "\t<a href=\"javascript:ajax_confirm('Are you sure you want to delete the checked records?', 'core/delete_rows', '$delete_data', '$form_name');\" class=\"btn btn-primary btn-md\" style=\"float: left;\">$table->delete_button</a>\n\n";
	}

	// Add pagination links
	if ($details['has_pages'] === true) { 
		$tpl_code .= "\t<e:pagination start=\"$details[start]\" page=\"$details[page]\" start_page=\"$details[start_page]\" end_page=\"$details[end_page]\" total=\"$details[total]\" rows_per_page=\"$details[rows_per_page]\" total_pages=\"$details[total_pages]\" id=\"$id\" ajaxdata=\"$ajaxdata\">\n\n";
	}
	$tpl_code .= "</tr></tfoot></e:data_table>\n\n";

	// Return
	return $tpl_code;

}

}

?>
