<?php
declare(strict_types = 1);

class htmlfunc_core_display_form {

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process(string $html, array $data = array()):string {

	// Set variables
	$package = $data['package'] ?? '';
	$width = $data['width'] ?? '';
	$allow_post_values = $data['allow_post_values'] ?? 1;

	// Load component
	$form = load_component('form', $data['form'], $package, '', $data);

	// Start TPL code
	$tpl_code = "<e:form_table";
	if ($width != '') { $tpl_code .= ' style="width: ' . $width . ';"'; }
	$tpl_code .= ">\n";

	// Go through form fields
	foreach ($form->form_fields as $name => $vars) { 

		// Get value
		if (isset($vars['value'])) { $value = $vars['value']; }
		elseif (is_array($form->values) && isset($form->values[$name]) && $form->values[$name] != '') { $value = $form->values[$name]; }
		elseif (isset($_POST[$name]) && $_POST[$name] != '' && $allow_post_values == 1) { $value = $_POST[$name]; }
		else { $value = ''; }

		// Get TPL code
		$field_tpl = "<e:ft_" . $vars['field'] . ' name="' . $name . '"';
		foreach ($vars as $fkey => $fvalue) { 
			if ($fkey == 'field') { continue; }
			$field_tpl .= ' ' . $fkey . '="' . $fvalue . '"';
		}
		$field_tpl .= " />";

		// Add to TPL code
		$tpl_code .= "\t$field_tpl\n";
	}

	// Retirm
	$tpl_code .= "</e:form_table>\n\n";
	return $tpl_code;


}


}

?>
