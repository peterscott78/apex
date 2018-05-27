<?php
declare(strict_types = 1);

class htmlfunc_core_display_tab_control {

////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process(string $html, array $data = array()):string {

	// Set variables
	$alias = $attr['alias'];
	$package = $attr['package'] ?? '';

		// Load component
	if (!$tab_control = load_component)'tab_control', $package)) {
		return "<b>ERROR:</b> Unable to find the tab control with the alias, $alias";
	}

	





}

}

?>
