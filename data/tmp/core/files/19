<?php
declare(strict_types = 1);

class Network { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct() { 


}


////////////////////////////////////////////////////////////
// Compile package
////////////////////////////////////////////////////////////

public function compile_package(string $pkg_alias) { 

	// Initialize
	$this->toc = array();
	$this->file_num = 1;
	$this->pkg_alias = $pkg_alias;

	// Load package
	$client = new Package($pkg_alias);
	$pkg = $client->load();

	// Create tmp directory
	$tmp_dir = SITE_PATH . '/data/tmp/' . $pkg_alias;
	remove_dir($tmp_dir);
	create_dir($tmp_dir);
	create_dir("$tmp_dir/files");

	// Copy over basic files
		$pkg_dir = SITE_PATH . '/data/pkg/' . $pkg_alias;
	$files = array('package.php', 'install.sql', 'install_after.sql', 'reset.sql', 'remove.sql');
	foreach ($files as $file) {
		if (!file_exists("$pkg_dir/$file")) { continue; }
		@copy("$pkg_dir/$file", "$tmp_dir/$file");
	}

	// Controllers
	foreach ($pkg->controllers as $file) { 
		if (!file_exists(SITE_PATH . '/data/controller/' . $file . '.php')) { continue; }
		$this->add_file('data/controller/' . $file . '.php');
	}

	// Templates
	foreach ($pkg->templates as $file) { 
		if (!file_exists(SITE_PATH . '/data/tpl/' . $file . '.tpl')) { continue; }
		$this->add_file('data/tpl/' . $file . '.tpl');

		// Check .php file
		if (file_exists(SITE_PATH . '/data/php/' . $file . '.php')) { 
			$this->add_file('data/php/' . $file . '.php');
		}
	}

	// External files
	foreach ($pkg->ext_files as $file) { 

		// Check for * mark
		if (preg_match("/^(.+?)\*$/", $file, $match)) { 
			$files = parse_dir(SITE_PATH . '/' . $match[1]);
			foreach ($files as $tmp_file) { $this->add_file($match[1] . $tmp_file); }
		} else { 
			$this->add_file($file);
		}
	}

	// Save JSON file
	file_put_contents("$tmp_dir/toc.json", json_encode($this->toc));

	// Create archive
	$archive_file = SITE_PATH . '/data/tmp/' . $pkg_alias . '_' . $pkg->version . '.tar';
	$archive_file = SITE_PATH . '/data/tmp/core.tar';
//	try { 
		$archive = new phar($archive_file);
		$archive->buildfromdirectory($temp_dir);
//	} catch (Exception $e) { 
//		trigger_error("Unable to create .tar archive at $archive_file.  Server Message: " . $e->getMessage(), E_USER_ERROR);
//	}

	// Return
	return $archive_file;

}

////////////////////////////////////////////////////////////
// Add file to archive
////////////////////////////////////////////////////////////

protected function add_file(string $filename) { 
	copy(SITE_PATH . '/' . $filename, SITE_PATH .'/data/tmp/' . $this->pkg_alias . '/files/' . $this->file_num);
	$this->toc[$type . ':' . $filename] = $this->file_num;
	$this->file_num++;

}


}

?>
