
function ajax_send(func_alias, data, field_names, form_id) { 
	if (!data) { data = ''; }
	if (!form_id) { form_id = 'frm_main'; }
	field_names = !field_names ? Array() : field_names.split(/,/);

	// Check for form
	var form = document.getElementById(form_id);
	if (!form) {  
		alert("No HTML form exists within this web page.  Aborting.");
		return false;
	}

	// Go through form elements
	for (x=0; x < form.elements.length; x++) { 
		if (!form.elements[x]) { continue; }
		if (!form.elements[x].name) { continue; }
		var e = form.elements[x];

		// Check field names
		if (field_names.length > 0 && field_names.indexOf(e.name) == -1) { continue; }
		if ((e.type == 'checkbox' || e.type == 'radio') && e.checked === false) { continue; }

		// Add to data
		if (data != '') { data += '&'; }
		data += e.name + '=' + encodeURIComponent(e.value);;
	}

	// Send AJAX request
	var url = SITE_URI + '/ajax/' + func_alias;
	$.post(url, data, function(response) { ajax_response(response); });






}


function ajax_confirm(message, func_alias, data, field_names, form_id) { 
	var response = confirm(message);
	if (response === true) { ajax_send(func_alias, data, field_names, form_id); }
}



function ajax_response(r) { 

	// Parse JSON
	try { 
		var res = JSON.parse(r);
	} catch (e) { 
		alert("Unable to parse JSON response from server.  Aborting.");
		return;
	}

	// Go through response
	for (ax=0; ax < res.actions.length; ax++) { 
		if (!res.actions[ax]) { continue; }
		var e = res.actions[ax];
		if (!e.action) { continue; }

		// Alert
		if (e.action == 'alert') { 
			alert(e.message);

		// Clear table
		} else if (e.action == 'clear_table') { 
			$('#' + e.divid + ' > tbody').children('tr').remove();

		// Remove checked table rows
		} else if (e.action == 'remove_checked_rows') {
			$('#' + e.divid + ' tbody > tr').has('input:checked').remove();

		// Add data row
		} else if (e.action == 'add_data_row') { 

			// Get table
			var table = document.getElementById(e.divid + '_tbody');
			var row = table.insertRow(table.rows.length);

			// Go through cells
			for (x=0; x < e.cells.length; x++) { 
				var c = row.insertCell(x);
				c.innerHTML = (!e.cells[x]) ? "&nbsp;" : e.cells[x];
			}


		// Set pagination
		} else if (e.action == 'set_pagination') { 

			// Set variables
			var pgn_id = 'pgn_' + e.divid;
			var html = '';

			// Set row results label
			var end_row = e.total > (e.page * e.rows_per_page) ? (e.page * e.rows_per_page) : e.total;
			document.getElementById('reslbl_' + e.divid).innerHTML = '<b>' + (++e.start) + '</b> - <b>' + end_row + '</b> of </b>' + e.total + '</b>';



			// First and previous pages
			if (e.start_page > 1) { html += "<li>" + e.nav_func.replace("~page~", '1') + "&laquo;</a></li>"; }		
			if (e.page > 1) { html += "<li>" + e.nav_func.replace('~page~', (e.page - 1)) + "&lt;</a></li>"; }

			// Go through pages
			for (x = e.start_page; x <= e.end_page; x++) { 
				if (e.page == x) { 
					html += '<li class="active"><a>' + x + ',/a></li>';
				} else { 
					html += "<li>" + e.nav_func.replace('~page~', x) + x + '</a></li>';
				}
			}

			// Next and last pages
			if (e.end_page > e.page) { html += "<li>" + e.nav_func.replace('~page~', ++e.page) + "&gt;</a></li>"; }
			if (e.total_pages > e.end_page) { html += "<li>" + e.nav_func.replace('~page~', e.total_pages) + "&laquo;</a></li>"; }

			// Replace HTML
			document.getElementById(pgn_id).innerHTML = html;

		} else { alert("Unknown action -- " + e.action); }

	}

}


function tbl_check_all(chk, table_id) { 
	var is_checked = chk.checked === true ? false : true;
	$('#' + table_id + ' > tbody td input:checkbox').prop('checked', is_checked);
}


