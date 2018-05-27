<?php

class package {

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct(string $pkg_alias = '') { 

	// Initialize
	$this->pkg_alias = $pkg_alias;
	$this->pkg_dir = SITE_PATH . '/data/pkg/' . $pkg_alias;



}

////////////////////////////////////////////////////////////
// Load package configuration
////////////////////////////////////////////////////////////

public function load() { 

	// Check package file
	if (!file_exists($this->pkg_dir . '/package.php')) { trigger_error("Package cinstallation file does not exist at, /data/pkg/$this->pkg_alias/package.php", E_USER_ERROR); }

	// Load package file
	require_once($this->pkg_dir . '/package.php');
	$class_name = 'pkg_' . $this->pkg_alias;
	$pkg = new $class_name();

	// Set blank variables
	if (!isset($pkg->config)) { $pkg->config = array(); }
	if (!isset($pkg->hash)) { $pkg->hash = array(); }
	if (!isset($pkg->controllers)) { $pkg->controllers = array(); }
	if (!isset($pkg->menus)) { $pkg->menus = array(); }
	if (!isset($pkg->templates)) { $pkg->templates = array(); }
	if (!isset($pkg->ext_files)) { $pkg->ext_files = array(); }


	// Return
	return $pkg;

}

////////////////////////////////////////////////////////////
// Installn package configuration
////////////////////////////////////////////////////////////

public function install_configuration() { 

	// Load package
	$pkg = $this->load();

	// Execute install code
	if (method_exists('install', $pkg)) { $pkg->install(); }

	// Config vars
	$this->install_config_vars($pkg);

	// Hashes
	$this->install_hashes($pkg);

	// Install controllers
	$this->install_controllers($pkg);

	// Install menus
	foreach ($pkg->menus as $vars) { 
		$this->install_menu($vars[0], $vars[1], $vars[2]);
	}

	// Go through component types
	foreach (COMPONENT_TYPES as $type) { 
		$this->scan_component_dir($type);
	}

	// Execute PHP code, if needed
	if (method_exists('install_after', $pkg)) { $pkg->install_after(); }



}

////////////////////////////////////////////////////////////
// Install config vars
////////////////////////////////////////////////////////////

protected function install_config_vars($pkg) { 
	if (!isset($pkg->config)) { return; }
	if (!is_array($pkg->config)) { return; }

	// Add config vars
	foreach ($pkg->config as $alias => $value) { 
		$this->add_component('config', $alias, $value);
	}

	// Check for deletions
	$chk_config = DB::get_column("SELECT alias FROM internal_components WHERE package = %s AND type = 'config'", $this->pkg_alias);
	foreach ($chk_configg as $chk) { 
		if (in_array($chk, array_keys($pkg->config))) { continue; }
		$this->delete_component($type, $chk);
	}

}

////////////////////////////////////////////////////////////
// Install hashes
////////////////////////////////////////////////////////////

protected function install_hashes($pkg) { 
	if (!isset($pkg->hash)) { return; }
	if (!is_array($pkg->hash)) { return; }

	// Add needed hashes
	foreach ($pkg->hash as $hash_alias => $vars) { 
		if (!is_array($vars)) { continue; }

		// Add hash
		$this->add_component('hash', $hash_alias);

		// Check for deletions
		$chk_vars = DB::get_column("SELECT alias FROM internal_components WHERE type = 'hash_var' AND package = %s AND parent = %s", $this->pkg_alias, $hash_alias);
		foreach ($chk_vars as $var) { 
			if (in_array($chk, array_keys($vars))) { continue; }
			$this->delete_component('hash_var', $chk, $hash_alias);
		}

		// Go through variables
		$order_num = 1;
		foreach ($vars as $key => $value) { 
			$this->add_component('hash_var', $key, $value, $hash_alias, $order_num);
		$order_num++; }
	}

	// Check for deletions
	$chk_hash = DB::get_column("SELECT alias FROM internal_components WHERE type = 'hash' AND package = %s", $this->pkg_alias);
	foreach ($chk_hash as $chk) { 
		if (in_array($chk, array_keys($pkg->hash))) { continue; }
		$this->delete_component('hash', $chk);
	}


}

////////////////////////////////////////////////////////////
// Install controllers
////////////////////////////////////////////////////////////

protected function install_controllers($pkg) { 
	if (!isset($pkg->controllers)) { return; }
	if (!is_array($pkg->controllers)) { return; }

	// Go through controllers
	foreach ($pkg->controllers as $file) { 
		if (!file_exists(SITE_PATH . '/data/controller/' . $file . '.php')) { continue; }

		// Add component
		list($package, $parent, $alias) = explode("/", preg_replace("/\.php$/", "", $file));

		// Add controller, if needed
		$exists = DB::get_field("SELECT count(*) FROM internal_components WHERE type = 'controller' AND package = %s AND parent = %s AND alias = %s", $package, $parent, $alias);
		if ($exists > 0) { continue; }

		// Add component
		$this->add_component('controller', $alias, '', $parent, 0, $package);
	}

}

////////////////////////////////////////////////////////////
// Scan component directory
////////////////////////////////////////////////////////////

protected function scan_component_dir(string $comp_type, string $parent = '') { 

	// Get component dir
	$comp_dir = SITE_PATH . '/data/' . $comp_type . '/' . $this->pkg_alias;
	if ($parent != '') { $comp_dir .= '/' . $parent; }
	if (!is_dir($comp_dir)) { return true; }

	// Go through all files
	$components = array();
	$files = parse_dir($comp_dir, true);
	foreach ($files as $file) { 

		// Process directory
		if (is_dir("$comp_dir/$file") && $parent == '') { 
			$this->scan_component_dir($comp_type, $file);
			continue;
		}

		// Ensure .php file
		if (!preg_match("/^(.+?)\.php$/", $file, $match)) { ccontinue; }
		$alias = $match[1];

		// Check if component exists
		$exists = DB::get_field("SELECT count(*) FROM internal_components WHERE type = %s AND package = %s AND parent = %s AND alias = %s", $comp_type, $this->pkg_alias, $parent, $alias);
		if ($exists ==0) {
			$this->add_component($comp_type, $alias, '', $parent);
		}
		$components[] = $alias;
	}

	// Delete needed components
$chk_components = DB::get_column("SELECT alias FROM internal_components WHERE type = %s AND package = %s AND parent = %s", $comp_type, $this->pkg_alias, $parent);
	foreach ($chk_components as $chk_alias) { 
		if (in_array($chk_alias, $components)) { continue; }
		$this->delete_component($comp_type, $chk_alias, $parent);

		// Delete file, if needed
		$filename = $comp_dir . '/' . $chk_alias . '.php';
		if (file_exists($filename)) { unlink($filename); }
	}

}


////////////////////////////////////////////////////////////
// Add component
////////////////////////////////////////////////////////////

protected function add_component($type, $alias, $value = '', $parent = '', $order_num = 0, $comp_package = '') {
	if ($comp_package == '') { $comp_package = $this->pkg_alias; }

	// Check if component exists
	$component_id = DB::get_field("SELECT id FROM internal_components WHERE package = %s AND type = %s AND alias = %s", $comp_package, $type, $alias);
	if ($component_id > 0) { 

		// Awr updates
		$updates = array(
			'order_num' => $order_num, 
			'parent' => $parent
		);
		if ($type == 'hash_var') { $updates['value'] = $value; }

		// Update db
		DB::update('internal_components', $updates, "id = %i", $component_id);

	// New component
	} else { 


		// Add to DB
		DB::insert('internal_components', array(
			'order_num' => $order_num, 
			'type' => $type, 
			'package' => $comp_package, 
			'parent' => $parent, 	
		'alias' => $alias, 
			'value' => $value)
	);	
	$component_id = DB::insert_id();

	}

// Return
	return $component_id; 

}


////////////////////////////////////////////////////////////
// Delete component
////////////////////////////////////////////////////////////

protected function delete_component($type, $alias, $parent = '') { 
	DB::query("DELETE FROM internal_components WHERE package = %s AND type = %s AND parent = %s AND alias = %s", $this->pkg_alias, $type, $parent, $alias);
	return true;
}


////////////////////////////////////////////////////////////
// Install menu
////////////////////////////////////////////////////////////

public function install_menu(string $area, string $top_position = 'bottom', array $menus = array()):bool { 

	// Go through menus
	foreach ($menus as $alias => $vars) {

		// Set variables
		$position = $vars['position'] ?? $top_position;

		// Add menu
		$this->add_single_menu($area, $alias, $position, $vars);
		$top_position = 'after ' . $alias;

		// Check for sub-menus
		if (isset($vars['submenu']) && is_array($vars['submenu'])) { 
			$vars['parent'] = $alias;
			$vars['link_type'] = 'internal';
			$position = 'bottom';

			foreach ($vars['submenu'] as $sub_alias => $sub_name) { 
				$vars['name'] = $sub_name;
				$this->add_single_menu($area, $sub_alias, $position, $vars);
			}
		}
	}

	// Return
	return true;



}

////////////////////////////////////////////////////////////
// Add single menu
////////////////////////////////////////////////////////////

protected function add_single_menu(string $area,string $alias, string $position = 'bottom', $vars = array()):int { 

	// Set variables
	$name = $vars['name'] ?? ucwords(str_replace("_", " ", $alias));
	$parent = $vars['parent'] ?? '';
	$icon = $vars['icon'] ?? '';
	$link_type = $vars['link_type'] ?? 'internal';
	$url = $vars['url'] ?? '';
	$require_login = $vars['require_login'] ?? 0;

	// Check if menu exists
	if ($menu_id = DB::get_field("SELECT id FROM cms_menus WHERE package = %s AND area = %s AND parent = %s AND alias = %s", $this->pkg_alias, $area, $parent, $alias)) { 



	// Add new menu {
	} else {

		// Get menu position
		$order_num = $vars['order_num'] ?? $this->get_menu_position($area, $position);

		// Add to DB
		DB::insert('cms_menus', array(
			'area' => $area, 
			'package' => $this->pkg_alias, 
			'require_login' => $require_login, 
			'order_num' => $order_num, 
			'link_type' => $link_type,  
			'icon' => $icon, 
			'parent' => $parent, 
			'alias' => $alias, 
			'display_name' => $name, 
			'url' =>$url)
		);
		$menu_id = DB::insert_id();
	}


	// Return
	return $menu_id;

}


////////////////////////////////////////////////////////////
// Get menu position
////////////////////////////////////////////////////////////

protected function get_menu_position(string $area, string $position):int { 

	// Initialize
	$words = explode(" ", strtolower($position));
	if (isset($words[1]) && preg_match("/^(.+?):(.+)$/", $words[1], $match)) { 
		$parent = $match[1];
		$alias = $match[2];
	} else { 
		$parent = '';
		$alias = $words[1];
	}

	// Top
if ($words[0] == 'top') { 
		DB::query("UPDATE cms_menus SET order_num = order_num + 1 WHERE area = %s AND parent = %s", $area, $parent);
		return 1;

	// Bottom
	} elseif ($words[0] == 'bottom') { 
		$order_num = DB::get_field("SELECT max(order_num) FROM cms_menus WHERE area = %s AND parent = %s", $area, $parent);
		return ($order_num + 1);
	}

	// Get current menu, default to bottom if not exists
	if (!$row = DB::get_row("SELECT * FROM cms_menus WHERE area = %s AND parent = %s AND alias = %s", $area, $parent, $alias)) {
		$order_num = DB::get_field("SELECT max(order_num) FROM cms_menus WHERE area = %s AND parent = %s", $area, $parent);
		return ($order_num + 1);
	}
	$order_num = $row['order_num'];


	// Get new order num
	if ($words[0] == 'before') { 
		DB::query("UPDATE cms_menus SET order_num = order_num + 1 WHERE area = %s AND parent = %s AND order_num >= %i", $area, $parent, $order_num);

	} elseif ($row['link_type'] == 'header' && $words[0] == 'after') { 

		if ($order_num = DB::get_field("SELECT order_num FROM cms_menus WHERE area = %s AND parent = %s AND link_type = 'header' AND order_num > %i", $area, $parent, $row['order_num'])) { 
			DB::query("UPDATE cms_menus SET order_num = order_num + 1 WHERE area = %s AND parent = %s AND order_num >= %i", $area, $parent, $order_num);
		} else {
			$order_num = DB::get_field("SELECT max(order_num) FROM cms_menus WHERE area = %s AND parent = %s", $area, $parent);
			$order_num++;
		}
		//if ($order_num == '') { $order_num = 0; }

	}elseif ($words[0] == 'after') { 
		DB::query("UPDATE cms_menus SET order_num = order_num + 1 WHERE area = %s AND parent = %s AND order_num > %i", $area, $parent, $row['order_num']);
		$order_num = ($row['order_num'] + 1);
	}
	// Return
return $order_num;

}

}

?>
