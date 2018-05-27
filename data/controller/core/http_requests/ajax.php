<?php

class controller_core_http_requests_ajax {


////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process(array $parts = array()) {

	//  Initialize
	if (!isset($parts[0])) { trigger_error("Invalid request", E_USER_ERROR); }
	if (isset($parts[1]) && $parts[1] != '') { 
		$package = $parts[0];
		$alias = $parts[1];
	} else { 
		$package = '';
		$alias = $parts[0];
	}

	// Check if component exists
	if (!component_exists('ajax', $alias, $package)) { 
		trigger_error("AJAX function does not exist '$alias' within the package ''", E_USER_ERROR);
	}

	// Process AJAX
	$client = load_component('ajax', $alias, $package);
	$client->process();

		// Display response
	$response = array('status' => 'ok', 'actions' => $client->results);
	echo json_encode($response);
	exit(0);

}

}

?>
