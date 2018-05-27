<?php

class theme_admin_default { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() { 

	// Set variables
	$this->theme_name = 'Admin Panel - Default Theme';
	$this->version = '1.0.0';
	$this->author_name = 'Envrin Group';
	$this->author_email = 'support@envrin.com';
	$this->author_url = 'https://envrin.com/';

}

////////////////////////////////////////////////////////////
// Nav menu
////////////////////////////////////////////////////////////

public function nav_menu($menus) { 


	// Get menu HTML
	$htnl = '';
	foreach ($menus as $vars) { 

		if ($vars['icon'] != '') { $vars['name'] = "<i class=\"$vars[icon]\"></i> $vars[name]"; }

		// Check for header
		if ($vars['link_type'] == 'header') { 
			$html .= "<li class=\"header\">$vars[name]</li>\n";
			continue;
		}

		// Start parent menu
		$html .= "\t<li class=\"treeview\"><a href=\"$vars[url]\"><span>$vars[name]</span><i class=\"fa fa-angle-left pull-right\"></i></a>";
		if (count($vars['children']) == 0) { $html .= "</li>\n"; continue; }

		$html .= "\n\t<ul class=\"treeview-menu\">";
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

}



?>
