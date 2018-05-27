<?php
declare(strict_types = 1);

class template {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct(string $template_path = '') {

	// Get template path
	if ($template_path != '') { $this->template_path = $template_path; }
	elseif (isset($_GET['route']) && $_GET['route'] != '') { $this->template_path = trim($_GET['route'], '/'); }
	else { $this->template_path = 'index'; }

	// Set variables
	$this->theme_dir = SITE_PATH . '/themes/' . $GLOBALS['_theme'];
	$this->theme_uri = SITE_URI . '/themes/' . $GLOBALS['_theme'];
	$this->html_tags = new html_tags();

		// Load theme class
	if (file_exists($this->theme_dir .'/theme.php')) { 
		require_once($this->theme_dir . '/theme.php');

		// Load class
		$class_name = 'theme_' . $GLOBALS['_theme'];
		$this->theme_client = new $class_name();
	} else { $this->theme_client = new stdClass(); }

	// Set blank variables
	$this->vars = array();
	$this->user_messages = array();
	$this->has_errors = 0;

}


////////////////////////////////////////////////////////////
// Parse
////////////////////////////////////////////////////////////

public function parse():string {

	// Set tpl file
	$this->tpl_file = SITE_PATH . '/data/tpl/' . $GLOBALS['_panel'] . '/';	
	if (file_exists($this->tpl_file . $this->template_path . '.tpl')) {
		$this->tpl_file .= $this->template_path .'.tpl';
	} else { echo "We're sorry, no TPL file or 404 template exists for the location <b>/" . $GLOBALS['_panel'] . "/" . $this->template_path . "</b>.  Please try again later, or contact the site administrator."; exit(0); }

	// Get TPL code
	$this->tpl_code = file_get_contents($this->tpl_file);

	// Load base variables
	$this->load_base_variables();

	// Add layout
	$this->add_layout();

	// Process theme components
	$this->process_theme_components();

	// Parse PHP code, if needed
		$php_file = SITE_PATH .'/data/php/' . $GLOBALS['_panel'] . '/' . $this->template_path .'.php';
	if (file_exists($php_file)) { require($php_file); }

	// Merge variables
	$this->merge_vars();

	// Parse HTML
	$html = $this->parse_html($this->tpl_code);
file_put_contents(SITE_PATH . '/t.txt', $html);
	return $html;


}

////////////////////////////////////////////////////////////
// Parse HTML
////////////////////////////////////////////////////////////

public function parse_html(string $html):string { 

	// User message
	$html = str_ireplace("<e:user_message>", $this->html_tags->user_message($this->user_messages), $html);

	// Process IF tags
	$html = $this->process_if_tags($html);

	// Process sections
	$html = $this->process_sections($html);

	// Process HTML functions
	$html = $this->process_function_tags($html);

	// Process page title
	$html = $this->process_page_title($html);

	// Process nav menus
	$html = $this->process_nav_menu($html);

	// Process e: tags
	preg_match_all("/<e:(.+?)>/si", $html, $tag_match, PREG_SET_ORDER);
	foreach ($tag_match as $match) {
		$tag = $match[1];

		// Parse attributes
		$attr = array();
		if (preg_match("/(.+?)\s(.+)$/", $tag, $attr_match)) { 
			$tag = $attr_match[1];
			$attr = $this->parse_attr($attr_match[2]);
		}

		// Check for closing tag
		if (preg_match("/$match[0](.*?)<\/e:$tag>/si", $html, $html_match)) { 


			$text = $html_match[1];
			$match[0] = $html_match[0];
		} else { $text = ''; }

		// Replace HTML tag
		$html = str_replace($match[0], $this->get_html_tag($tag, $attr, $text), $html);
	}

	// Replace special characters
	$html = str_replace(array('~op~','~cp~'), array('(', ')'), $html);

	// Return
	return $html;

	}


////////////////////////////////////////////////////////////
// Add page layout
////////////////////////////////////////////////////////////

protected function add_layout() { 

	// Get layout
	if ($row = DB::get_row("SELECT * FROM cms_pages WHERE area = %s AND filename = %s", $GLOBALS['_panel'], $_GET['route'])) {
		$layout = $row['layout'];
	} else { $layout = 'default'; }

	// Check if layout exists
	$layout_file = $this->theme_dir . '/layouts/' . $layout . '.tpl';
	if (!file_exists($layout_file)) { 
		if ($layout == 'default') { return; }

		$layout_file = $this->theme_dir . '/layouts/default.tpl';
		if (!file_exists($layout_file)) { return; }
	}

	// Get layout file
	$layout_html = file_get_contents($layout_file);
	$layout_html = str_replace("<e:page_contents>", $this->tpl_code, $layout_html);
	$this->tpl_code = $layout_html;

}

////////////////////////////////////////////////////////////
// Get page title
////////////////////////////////////////////////////////////

protected function get_page_title() {

	// Initialize
	global $config;

	//Get page title
	$title = '';
	if ($row = DB::get_row("SELECT * FROM cms_pages WHERE area = %s AND filename = %s", $GLOBALS['_panel'], $_GET['route'])) { 
		$title = $row['title'];
	} elseif (preg_match("/<h1>(.+?)<\/h1>/i", $this->tpl_code, $match)) { 
		$title = $match[1];
		$this->tpl_code = str_replace($match[0], "", $this->tpl_code);
	} elseif ($GLOBALS['_panel'] != 'admin') { 
		$title = $config['site_name'];
	}
	


/// Return
	return $title;


}

////////////////////////////////////////////////////////////
// Process page title
////////////////////////////////////////////////////////////

protected function process_page_title(string $html):string {

	// Go through e:page_title tags
	preg_match_all("/<e:page_title(.*?)>/si", $html, $tag_match, PREG_SET_ORDER);
	foreach ($tag_match as $match) { 

		$attr = $this->parse_attr($match[1]);
		$html = str_replace($match[0], $this->get_html_tag('page_title', $attr, $this->page_title), $html);
	}

	// Return
	return $html;


}


////////////////////////////////////////////////////////////
// Parse nav menu
////////////////////////////////////////////////////////////

protected function process_nav_menu(string $html):string { 

	// Initial checks
	if (!preg_match("/<e:nav_menu(.*?)>/i", $html, $match)) { return $html; }

	// Go through menus
		$result = DB::query("SELECT * FROM cms_menus WHERE area = %s AND is_active = 1 AND parent = '' ORDER BY order_num", $GLOBALS['_panel']);
	$menus = $this->process_menu_result($result);

	// Get child menus
	$x=0;
	foreach ($menus as $vars) { 
		$result = DB::query("SELECT * FROM cms_menus WHERE area = %s AND is_active = 1 AND parent = %s ORDER BY order_num", $GLOBALS['_panel'], $vars['alias']);
		$menus[$x]['children'] = $this->process_menu_result($result);
	$x++; }

	// Process nav menus
	if (method_exists($this->theme_client, 'nav_menu')) { 
		$menu_html = $this->theme_client->nav_menu($menus);
	} else { 
		$menu_html = $this->html_tags->nav_menu($menus);
	}

	// Replace HTML
	$html = str_replace("<e:nav_menu>", $menu_html, $html);

	// Return
	return $html;


}

////////////////////////////////////////////////////////////
// Process menu result
////////////////////////////////////////////////////////////

protected function process_menu_result($result):array { 

	// Gather menus
	$menus = array();
	while ($row = DB::fetch_assoc($result)) {

		// Get URL
		if ($row['link_type'] == 'parent') { $url = '#'; }
		elseif ($row['link_type'] == 'external') { $url = $row['url']; }
		elseif ($row['link_type'] == 'internal') {
			$url = SITE_URI;
			if ($GLOBALS['_panel'] != 'public') { $url .= '/' . $GLOBALS['_panel']; }
			if ($row['parent'] != '') { $url .= '/' . $row['parent']; }
			$url .= '/' . $row['alias'];
		} else { $url = ''; } 

		// Set vars
		$vars = array(
			'url' => $url, 
		'icon' => $row['icon'], 
			'alias' => $row['alias'], 
			'name' => $row['display_name']
		);
		array_push($menus, $vars);
	}

	// Return
	return $menus;

}


////////////////////////////////////////////////////////////
// Process HTML function tags
////////////////////////////////////////////////////////////

protected function process_function_tags(string $html):string { 

	// Go through function tags
	preg_match_all("/<e:function (.*?)>/si", $html, $tag_match, PREG_SET_ORDER);
	foreach ($tag_match as $match) {

		// Parse attributes
		$attr = $this->parse_attr($match[1]);
		if (!isset($attr['alias'])) { return "<b>ERROR:</b. No 'alias' attribute exists within the 'function' tag, which is required."; }

		// Set variables
	$package = isset($attr['package']) ? $attr['package'] : '';
		if (!component_exists('htmlfunc', $attr['alias'], $package)) { 
			return "<b>ERROR:</b> The HTML function '$attr[name]' does not exist.";
		}

		// Load component
		$client = load_component('htmlfunc', $attr['alias'], $package, '', $attr);
		if (!is_object($client)) { 
			$html = str_replace($match[0], $client, $html);
			continue;
		}

		// Get temp HTML
		if ($package == '') { $package = DB::get_field("SELECT package FROM internal_components WHERE type = 'htmlfunc' AND alias = %s", $attr['alias']); }
	$func_tpl_file = SITE_PATH . '/data/htmlfunc/' . $package . '/' . $attr['alias'] . '.tpl';
	$temp_html = file_exists($func_tpl_file) ? file_get_contents($func_tpl_file) : '';

		// Replace HTML
		$html = str_replace($match[0], $client->process($temp_html, $attr), $html);
	}

	// Return
	return $html;

 
}

////////////////////////////////////////////////////////////
// Process if tags
////////////////////////////////////////////////////////////

protected function process_if_tags(string $html):string {

	// Go through all IF tags
	preg_match_all("/<e:if (.*?)>(.*?)<\/e:if>/si", $html, $tag_match, PREG_SET_ORDER);
	foreach ($tag_match as $match) {

		// Check for <eLelse> tag
		if (preg_match("/^(.*?)<e:else>(.*)$/si", $match[2], $else_match)) { 
			$if_html = $else_match[1];
			$else_html = $else_match[2];
		} else { 
			$if_html = $match[2];
			$else_match = '';
		}

		// Check condition
		$replace_html = eval( "return " . $match[1] . ";" ) === true ? $if_html : $else_html;
		$html = str_replace($match[0], $replace_html, $html);
	}

	// Return
	return $html;

}


////////////////////////////////////////////////////////////
// Process sections
////////////////////////////////////////////////////////////

protected function process_sections(string $html):string {

	// Go through sections
	preg_match_all("/<e:section(.*?)>(.*?)<\/e:section>/si", $html, $tag_match, PREG_SET_ORDER);
	foreach ($tag_match as $match) { 

		// Parse attributes
		$attr = $this->parse_attr($match[1]);

		// Check if variable exists
		if (!is_array($this->vars[$attr['name']])) { 
			$html = str_replace($match[0], "", $html);
			continue;
		}

		// Get replacement HTML
		$replace_html = '';
		foreach ($this->vars[$attr['name']] as $vars) { 
			$temp_html = $match[2];

			// Replace
			foreach ($vars as $key => $value) { 
				$key = $attr['name'] . '.' . $key;
				$temp_html = str_ireplace("~$key~", $value, $temp_html);
			}
			$replace_html .= $temp_html;
		}

		// Replace in HTML
		$html = str_replace($match[0], $replace_html, $html);
	}


	// Return
return $html;

}


////////////////////////////////////////////////////////////
// Parse theme components
////////////////////////////////////////////////////////////

protected function process_theme_components() { 

	// Go through theme components
	while (preg_match("/<e:theme(.*?)>/si", $this->tpl_code)) {

		preg_match_all("/<e:theme(.*?)>/si", $this->tpl_code, $theme_match, PREG_SET_ORDER);
		foreach ($theme_match as $match) {

			// Parse attributes
			$attr = $this->parse_attr($match[1]);

			// Section file
			if (isset($attr['section']) && $attr['section'] != '') { 
				$temp_html = file_exists($this->theme_dir . '/sections/' . $attr['section']) ? file_get_contents($this->theme_dir . '/sections/' . $attr['section']) : "<b>ERROR: Theme section file does not exist, $attr[section].</v>";

			} else {
				$temp_html = "<b>ERROR: Invalid theme tag.  No valid attributes found.</b>";

			}
			$this->tpl_code = str_replace($match[0], $temp_html, $this->tpl_code);

		}

	}



}

////////////////////////////////////////////////////////////
// Load base variables
////////////////////////////////////////////////////////////

protected function load_base_variables() { 

// Set base variables
	$this->assign('theme_uri', $this->theme_uri); 
	$this->assign('theme_dir', $this->theme_uri);
	$this->assign('site_uri', SITE_URI);
	$this->assign('userid', $GLOBALS['userid']);

	// Get page title
	$this->page_title = $this->get_page_title();




	

	
}

////////////////////////////////////////////////////////////
// Merge variables
////////////////////////////////////////////////////////////

protected function merge_vars() { 

	foreach ($this->vars as $key => $value) {
		$this->tpl_code = str_ireplace("~$key~", $value, $this->tpl_code);
	}


}

////////////////////////////////////////////////////////////
// Get HTML tag code
////////////////////////////////////////////////////////////

public function get_html_tag(string $tag, array $attr, string $text = ''):string {

	// Check for theme specific tag
	if (method_exists($this->theme_client, $tag)) { 
		return $this->theme_client->$tag($attr, $text);
	}

	

	// Check if tag exists
	if (!method_exists($this->html_tags, $tag)) { 
		return "<b>ERROR:</b> The special HTML tag '$tag' is invalid.";
	}


	$html = $this->html_tags->$tag($attr, $text);

	// Return
	return $html;

}





////////////////////////////////////////////////////////////
// Parse attributes
////////////////////////////////////////////////////////////

public function parse_attr(string $string):array { 
	
	// Parse string
	$attributes = array();
	preg_match_all("/(\w+?)\=\"(.*?)\"/", $string, $attribute_match, PREG_SET_ORDER);
	foreach ($attribute_match as $match) { 
		$value = str_replace("\"", "", $match[2]);
		$attributes[$match[1]] = $value;
	}
	
	// Return
	return $attributes;

}
////////////////////////////////////////////////////////////
// Assign variable
////////////////////////////////////////////////////////////

public function assign(string $name, $value, string $description = ''):bool {
	$this->vars[$name] = $value;
	return true;

}

////////////////////////////////////////////////////////////
// Add message
////////////////////////////////////////////////////////////

public function add_message():bool {
 
	// Get args
	$args = func_get_args();
	$message = array_shift($args);
	$type = array_shift($args);
	if ($message == '') { return false; }

	// Go through args
	foreach ($args as $value) { 
		$message = preg_replace("/\%s/", $value, $message, 1);
	}

	// Add message to needed array
	if (!isset($this->user_messages[$type])) { $this->user_messages[$type] = array(); }
	array_push($this->user_messages[$type], $message);
	if ($type == 'error') { $this->has_errors = 1; }

	// Return
	return true;
}





}
?>
