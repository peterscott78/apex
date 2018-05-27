<?php
declare(strict_types = 1);

class html_tags {

////////////////////////////////////////////////////////////
// user_message
////////////////////////////////////////////////////////////

public function user_message(array $messages):string {

	$user_message = '';
	$msg_types = array('success','info','error');
	foreach ($msg_types as $type) { 
		if (!isset($messages[$type])) { continue; }
		$css_type = $type == 'error' ? 'danger' : $type;

		// Get icon
		if ($type == 'info') { $icon = 'info'; }
		elseif ($type == 'error') { $icon = 'ban'; }
		else { $icon = 'check'; }

		// Create HTML
		$user_message .= '<div class="callout callout-' . $css_type . ' text-center"><p><i class="icon fa fa-' . $icon . '"></i> ';
		foreach ($messages[$type] as $msg) { 
			if ($msg == '') { continue; }
			$user_message .= "$msg<br />";
		}
		$user_message .= "</p></div>";
	}

	
	// rETURN
		return $user_message;

}
////////////////////////////////////////////////////////////
// Page Title
////////////////////////////////////////////////////////////

public function page_title(array $attr, string $text):string { 

	// Chwck if textonly
	if (isset($attr['textonly']) && $attr['textonly'] == 1) { return $text; }

	// Format
	return '<h1>' . $text . '</h1>';

}


////////////////////////////////////////////////////////////
// Nav menu
////////////////////////////////////////////////////////////

public function nav_menu(array $menus):string { 


	// Get menu HTML
	$htnl = '<ul id="nav">' . "\n";
	foreach ($menus as $vars) { 

		if ($vars['icon'] != '') { $vars['name'] = "<i class=\"$vars[icon]\"></i> $vars[name]"; }
		$html .= "\t<li><a href=\"$vars[url]\">$vars[name]</a>";
		if (count($vars['children']) == 0) { $html .= "</li>\n"; continue; }

		$html .= "\n\t<ul>";
		foreach ($vars['children'] as $cvars) { 
			if ($cvars['icon'] != '') { $cvars['name'] = "<i class=\"$cvars[icon]\"></i> $cvars[name]"; }
			$html .= "<li><a href=\"$cvars[url]\">$cvars[name]</a></li>\n";
		}
		$html .= "\t</ul></li>\n";
	}


	$html .= "</ul>";

	// Return
	return $html;

}

////////////////////////////////////////////////////////////
// form
////////////////////////////////////////////////////////////

public function form($attr, $text = '') {

		// Get form action
	if (isset($attr['action'])) { $action = $attr['action']; }
	elseif ($GLOBALS['_panel'] == 'public') { $action = $_GET['route']; }
	else { $action = $GLOBALS['_panel'] . '/' . $_GET['route']; }

	// Set variables
	$action = SITE_URI . '/' . trim($action, '/');
	$method = $attr['method'] ?? 'POST';
	$enctype = $attr['enctype'] ?? 'application/x-www-form';
	$class = $attr['class'] ?? 'form-inline';
	$id = $attr['id'] ?? 'frm_main';
	if (isset($attr['file_upload']) && $attr['file_upload'] == 1) { $enctype = 'multipart/form-data'; }

	// Set HTML
	$html = "<form action=\"$action\" method=\"$method\" enctype=\"$enctype\" class=\"$class\" id=\"$id\" data-parsley-validate=\"\">";
	return $html;




}


////////////////////////////////////////////////////////////
// form_table
////////////////////////////////////////////////////////////

public function form_table(array $attr, string $text):string {

	// Get HTML
	$html = "<table class=\"form_table\"";
	if (isset($attr['width'])) {$html .= " style=\"width: " . $attr['width'] . ";\""; }
	if (isset($attr['align'])) { $html .= " align=\"$attr[align]\""; }
	$html .= ">" . $text . "</table>";

	// Return
	return $html;
 

}


////////////////////////////////////////////////////////////
// ft_seperator
////////////////////////////////////////////////////////////

public function ft_seperator(array $attr, string $text = ''):string { 
	$html = "<tr><td colspan=\"2\" style=\"padding: 5px 25px;\"><h5>" . $attr['label'] . "</h5></td></tr>\n";
	return $html; 

}

////////////////////////////////////////////////////////////
// ft_textbox
////////////////////////////////////////////////////////////

public function ft_textbox(array $attr, string $text = ''):string {

// Perform checks
	if (!isset($attr['name'])) { return "<v>ERROR:</b>:  No 'name' attribute within the ft_textbox field."; }

	// Set variables
	$name = $attr['name'];
	$label = $attr['label'] ?? ucwords(str_replace("_", " ", $name));	

	// SGet HTML
	$html = "\n\t<tr><td><label for=\"$name\">" . $label . ":</label></td>\n\t<td>";
	$html .= '<div class="form-group">';

	// Add form field
	$html .= $this->textbox($attr, $text);
	$html .= "</div></td>\n</tr>";	//

	// Return
	return $html;

}


////////////////////////////////////////////////////////////
// ft_textarea
////////////////////////////////////////////////////////////

public function ft_textarea(array $attr, string $text = ''):string {

// Perform checks
	if (!isset($attr['name'])) { return "<v>ERROR:</b>:  No 'name' attribute within the ft_textbox field."; }

	// Set variables
	$name = $attr['name'];
	$label = $attr['label'] ?? ucwords(str_replace("_", " ", $name));	

	// SGet HTML
	$html = "\n\t<tr><td><label for=\"$name\">" . $label . ":</label></td>\n\t<td>";
	$html .= '<div class="form-group">';

	// Add form field
	$html .= $this->textarea($attr, $text);
	$html .= "></div></td>\n</tr>";	//

	// Return
	return $html;

}

////////////////////////////////////////////////////////////
// ft_select
////////////////////////////////////////////////////////////

public function ft_select(array $attr, string $text = ''):string {         

	// Checks
	if (!isset($attr['name'])) { return "<b>ERROR:</b> No 'name' attribute exists within the 'select' tag."; }
	//if (!isset($attr['data_source'])) { return "<b>ERROR:</b> No 'data_source' attribute exists within the 'select_data' tag."; }

	// Set variables
	$label = $attr['label'] ?? ucwords(str_replace("\n", "<br />", $attr['name']));

	// Set HTML
	$html = "<tr><td><label for=\"$attr[name]\">" . $label . ":</label></td><td>";
	$html .= $this->select($attr, $text);
	$html .= "</td></tr>";

	// Return
	return $html;

}

////////////////////////////////////////////////////////////
// ft_boolean
////////////////////////////////////////////////////////////

public function ft_boolean(array $attr, string $text = ''):string {

	// Perform checks
	if (!isset($attr['name'])) { return "The 'ft_boolean' tag does not contain a 'name' attribute."; }

	// Set variables
	$label = $attr['label'] ?? ucwords(str_replace("_", " ", $attr['name']));
	$value = $attr['value'] ?? 0;
	$chk_yes = $value == 1 ? 'checked="checked"' : '';
	$chk_no = $value == 0 ? 'checked="checked"' : '';

	// SGet HTML
	$html = "\n\t<tr><td><label for=\"$attr[name]\">" . $label . ":</label></td>\n\t<td>";
	$html .= '<div class="form-group">';
	// Add form field
	$html .= "<input type=\"radio\" name=\"$attr[name]\" class=\"form-control\" value=\"1\" $chk_yes> Yes ";
	$html .= "<input type=\"radio\" name=\"$attr[name]\" class=\"form-control\" value=\"0\" $chk_no> No ";
	$html .= "></div></td>\n</tr>";	//

	// Return
return $html;

}


////////////////////////////////////////////////////////////
// ft_custom
////////////////////////////////////////////////////////////

public function ft_custom(array $attr, string $text = ''):string { 

	// Perform checks
	if (!isset($attr['name'])) { return "<b>ERROR:</b> The 'ft_custom' tag does not have a 'name' attribute."; }
	if (!isset($attr['contents'])) { return "<b>ERROR:</b> The 'ft_custom' tag does not have a 'contents' attribute."; }

	// Set variables
	$label = $attr['label'] ?? ucwords(str_replace("_", " ", $attr['name']));

	// Set HTML
	$html = "<tr><td><label for=\"$attr[name]\">" . $label . ":</label></td><td>";
	$html .= $attr['contents'];
	$html .= "</td></tr>";

	// Return
	return $html;

}

////////////////////////////////////////////////////////////
// ft_submit
////////////////////////////////////////////////////////////

public function ft_submit(array $attr, string $text = ''):string { 

	// Set variables
	$value = $attr['value'] ?? 'submit';
	$label = $attr['label'] ?? 'Submit Query';
	$size = $attr['size'] ?? 'lg';
	$has_reset = $attr['has_reset'] ?? 0;

// Set HTML
$html = "<tr>\n\t<td colspan=\"2\" align=\"center\">";
	$html .= "<button type=\"submit\" name=\"submit\" value=\"$value\" class=\"btn btn-prinary btn-$size\">$label</button>";
	if ($has_reset == 1) { $html .= " <button type=\"reset\">Reset Form</button>"; }
	$html .= "</td>\n</tr>";


	// Return
	return $html;




}


////////////////////////////////////////////////////////////
// select
////////////////////////////////////////////////////////////

public function select(array $attr, string $text = ''):string { 

	// Checks
	if (!isset($attr['name'])) { return "<b>ERROR:</b> No 'name' attribute exists within the 'select' tag."; }
	//if (!isset($attr['data_source'])) { return "<b>ERROR:</b> No 'data_source' attribute exists within the 'select_data' tag."; }
	
	// Set variables
	$class = $attr['class'] ?? 'form-control';
	$width = $attr['width'] ?? '';
	$selected = $attr['selected'] ?? '';
	$onchange = $attr['onchange'] ?? '';
	$package = $attr['package'] ?? '';


	// Start HTML
	$html= "<select name=\"$attr[name]\" class=\"$class\"";
	if ($width != '') { $html .= " style=\"width: " . $width . ";\""; }
	if ($onchange != '') { $html .= " onchange=\"$onchange\""; }
	$html .= ">";

	// Add select options
	if (isset($attr['data_source'])) { 
		$html .= parse_data_source($attr['data_source'], $selected, 'select', $package);
	} else { $html .= $text; }
	$html .= "</select>";

	// Return
	return $html;

}

////////////////////////////////////////////////////////////
// textbox
////////////////////////////////////////////////////////////

public function textbox(array $attr, string $text = ''):string {


// Perform checks
	if (!isset($attr['name'])) { return "<v>ERROR:</b>:  No 'name' attribute within the ft_textbox field."; }

	// Set variables
	$name = $attr['name'];
	$type = $attr['type'] ?? 'text';	
	$label = $attr['label'] ?? ucwords(str_replace("_", " ", $name));
	$placeholder = $attr['placeholder'] ?? '';
	$class = $attr['class'] ?? 'form-control';
	$value = $attr['value'] ?? '';
	$width = $attr['width'] ?? '';
	$id = $attr['id'] ?? 'input_' . $name;
	$onfocus = $attr['onfocus'] ?? '';
	$onblur = $attr['onblur'] ?? '';
	$onkeyup = $attr['onkeyup'] ?? '';

	// Validation variables
	$required = $attr['required'] ?? 0;
	$datatype = $attr['datatype'] ?? '';
	$minlength = $attr['minlength'] ?? 0;
	$maxlength = $attr['maxlength'] ?? 0;
	$range = $attr['range'] ?? '';
	$equalto = $attr['equalto'] ?? '';
 

	// Get HTML
	$html = "<input type=\"$type\" name=\"$name\" class=\"$class\" id=\"$id\"";
	if ($placeholder != '') { $html .= " placeholder=\"$placeholder\""; }
	if ($width != '') { $html .= " style=\"width: $width; float: left;\""; }
	if ($onfocus != '') { $html .= " onfocus=\"$onfocus\""; }
	if ($onblur != '') { $html .= " onblur=\"$onblur\""; }
	if ($onkeyup != '') { $html .= " onkeyup=\"$onkeyup\""; }

	// Add validation attributes
	if ($required == 1) { $html .= " data-parsley-required=\"true\""; }
	if ($datatype != '') { $html .= " data-parsley-type=\"$datatype\""; }
	if ($minlength > 0) { $html .= " data-parsley-minlength=\"$minlength\""; }
	if ($maxlength > 0) { $html .= " data-parsley-maxlength=\"$maxlength\""; }
	if ($range != '') { $html .= " data-parsley-range=\"$range\""; }
	if ($equalto != '') { $html .= " data-parsley-equalto=\"$equalto\""; }

	// Return
	$html .= ">";
	return $html;

}


////////////////////////////////////////////////////////////
// textarea
////////////////////////////////////////////////////////////

public function textarea(array $attr, string $text = ''):string { 

	// Perform checks
	if (!isset($attr['name'])) { return "<v>ERROR:</b>:  No 'name' attribute within the ft_textarea field."; }

	// Set variables
	$name = $attr['name'];
	$label = $attr['label'] ?? ucwords(str_replace("_", " ", $name));
	$placeholder = $attr['placeholder'] ?? '';
	$class = $attr['class'] ?? 'form-control';
	$value = $attr['value'] ?? '';
	$id = $attr['id'] ?? 'input_' . $name;

	// Get size
	if (isset($attr['size']) && preg_match("/^(.+?),(.+)/", $attr['size'], $match)) { 
		$width = $match[1];
		$height = $match[2];
	} else { $width = ''; $height = ''; }

	// SGet HTML
	$html = "<textarea  name=\"$name\" class=\"$class\" id=\"$id\"";
	if ($placeholder != '') { $html .= " placeholder=\"$placeholder\""; }
	if ($width != '' && $height != '') { $html .= " style=\"width: $width; height: $height;;\""; }
	$html .= ">$value</textarea>";

	// Return
	return $html;

}


////////////////////////////////////////////////////////////
// button
////////////////////////////////////////////////////////////

public function button(array $attr, string $text = ''):string {

	// Set variables
	$href = $attr['href'] ?? '';
	$label = $attr['label'] ?? 'Submit Query';
	$size = $attr['size'] ?? 'lg';

	// Set HTML
	$html = "<a href=\"$href\" class=\"btn btn-prinary btn-$size\">$label</a>";
	return $html;

}

////////////////////////////////////////////////////////////
// box
////////////////////////////////////////////////////////////

public function box(array $attr, string $text = ''):string { 
	$html .= "<div class=\"box\">\n$text\n</div>\n";
	return $html;

}

////////////////////////////////////////////////////////////
// box_header
////////////////////////////////////////////////////////////

public function box_header(array $attr, string $text = ''):string { 

		// Get HTML
	$html = "\t<div class=\"box-header with-border\">\n";
	if (isset($attr['title']) && $attr['title'] != '') { $html .= "\t\t<h3>$attr[title]</h3>\n"; }
	$html .= $text . "\n\t</div>\n";

	// Return
	return $html;

}
////////////////////////////////////////////////////////////
// Data table
////////////////////////////////////////////////////////////

public function data_table(array $attr, string $text = ''):string { 

	// Set variables
	$class = $attr['class'] ?? 'table table-bordered table-striped table-hover';
	$id = $attr['id'] ?? 'data_table';

	// Set HTML
	$html = "<table class=\"$class\" id=\"$id\">\n";
	$html .= $text . "\n";
	$html .= "</table>\n";

	// Return
	return $html;

}

////////////////////////////////////////////////////////////
// Table search bar
////////////////////////////////////////////////////////////

public function table_search_bar(array $attr, string $text = ''):string { 	

	// Set variables
	$search_id = 'search_' . $attr['id'];
$ajaxdata = $attr['ajaxdata'] ?? '';

	// Set HTML
	$html = "<div class=\"tbl_search_bar\">\n";
	$html .= "\t<i class=\"fa fa-search\"></i> \n";
	$html .= "\t<input type=\"text\" name=\"$search_id\" placeholder=\"Search...\" class=\"form-control\" style=\"width: 210px;\"> \n";
	$html .= "\t<a href=\"javascript:ajax_send('core/search_table', '$ajaxdata', '$search_id');\" class=\"btn btn-primary btn-md\">Search</a>\n";
	$html .= "</div>\n\n";

	// Return
	return $html;



}

////////////////////////////////////////////////////////////
// Pargination links
////////////////////////////////////////////////////////////

public function pagination(array $attr, string $text = ''):string { 

	// Set variables
	$id = $attr['id'];

	// Get AJAX function
	$ajaxdata = $attr['ajaxdata'] ?? '';
	if ($ajaxdata != '') { $ajaxdata .= '&'; }
	$ajaxdata .= "page=~page~";
	$nav_func = "<a href=\"javascript:ajax_send('core/navigate_table', '$ajaxdata', 'none');\">";

	// Return, if not enough rows
	if ($attr['rows_per_page'] >= $attr['total']) { return ''; }

	// Start HTML
	$end_row = $attr['total'] > ($attr['page'] * $attr['rows_per_page']) ? ($attr['page'] * $attr['rows_per_page']) : $attr['total'];
	$html = '<span id="reslbl_' . $id . '" style="vertical-align: middle; font-size: 8pt; margin-right: 7px;"><b>' . ($attr['start'] + 1) . '</b> - <b>' . ($attr['page'] * $attr['rows_per_page']) . '</b> of <b>' . $end_row . '</b></span>';
	$html .= "<ul class=\"pagination\" id =\"pgn_" . $id . "\">";

	// First page
	$display = $attr['start_page'] > 1 ? '' : 'none';
	$html .= "<li style=\"display: " . $display . ";\">" . str_replace("~page~", '1', $nav_func) . "&laquo;</a></li>";

	// Previous page
	$display = $attr['page'] > 1 ? '' : 'none';
	$html .= "<li style=\"display: " . $display . ";\">" . str_replace("~page~", ($attr['page'] - 1), $nav_func) . "&lt;</a></li>";

	// Go through pages
	$x=1;
	for ($page_num = $attr['start_page']; $page_num <= $attr['end_page']; $page_num++) {  

		if ($page_num == $attr['page']) { 
			$html .= '<li class="active"><a>' . $page_num . '</a></li>';
		} else {
			$html .= "<li>" . str_replace('~page~', $page_num, $nav_func) . $page_num . "</a></li>"; 
		}
	$x++; }

	// Next page
	$display = $attr['total_pages'] > $attr['page'] ? '' : 'none';
	$html .= "<li style=\"display: " . $display . ";\">" . str_replace("~page~", ($attr['page'] + 1), $nav_func) . "&gt;</a></li>";

	// Last page
	$display = $attr['total_pages'] > $attr['end_page'] ? '' : 'none';
	$html .= "<li style=\"display: " . $display . ";\">" . str_replace("~page~", $attr['end_page'], $nav_func) . "&raquo;</a></li>";

	// Return
	$html .= '</ul>';
	return $html;

}


////////////////////////////////////////////////////////////
// tab control
////////////////////////////////////////////////////////////

public function tab_control($attr, $text = '') { 

	// Set variables
	$active = $attr['active'] ?? '';
	$tab_num = 1;
	$tab_html = '';

	// Start HTML
	$html = "<div class=\"nav-tabs-custom\">\n\t<ul class=\"nav nav-tabs\">\n";

	// Go through tab pages
	preg_match_all("/<e:tab_page(.*?)>(.*?)<\/e:tab_page>/si", $text, $tab_match, PREG_SET_ORDER);
	foreach ($tab_match as $tab) { 

		// Get name
		$name = preg_match("/name=\"(.+?)\"/", $tab[1], $name_match) ? $name_match[1] : 'Unknown Tab';

		// Add tab navigation
		$html .= (($active == $name) || ($tab_num == 1 && $active == '')) ? "\t\t<li class=\"active\">" : "\t\t<li>";
		$html .= "<a href=\"#tab" . $tab_num . "\" data-toggle=\"tab\">$name</a></li>\n";

		// Add tab page contents
		$class = (($active == $name) || ($tab_num == 1 && $active == '')) ? "tab-pane active" : "tab-pane";
		$tab_html .= "\t\t<div class=\"$class\" id=\"tab" . $tab_num . "\">\n";
		$tab_html .= $tab[2] . "\n\t</div>\n\n";

	$tab_num++; }

	// Return
	$html .= "\t</ul>\n\n\t<div class=\"tab-content\">\n\n" . $tab_html . "\t</div>\n</div>\n\n";
	return $html;

}

}
?>
