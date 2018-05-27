<?php

class controller_core_http_requests_default {


////////////////////////////////////////////////////////////
// Process
////////////////////////////////////////////////////////////

public function process() {

	// Parse template
	global $template;
	$template = new template();
	echo $template->parse(); exit(0);

}


}
?>
