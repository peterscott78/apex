<?php
declare(strict_types = 1);

// Define component types
define('COMPONENT_TYPES', array(
	'ajax', 
	'cron', 
	'form', 
	'htmlfunc', 
	'lib', 
	'table')
);

// Get site path
$site_path = realpath(dirname(__FILE__));
define ('SITE_PATH', $site_path);

// Get site URI
$site_uri = preg_replace("/\/(.+?)\.php/", '', $_SERVER['PHP_SELF']);
define('SITE_URI', $site_uri);

// Load files
require_once(SITE_PATH . '/data/config.php');
require_once(SITE_PATH . '/data/libstd/functions.php');
require_once(SITE_PATH . '/data/libstd/db/' . DB_DRIVER .'.php');


// Register autoload function
spl_autoload_register('autoload_class');

// Set error reporting
error_reporting(E_ALL);
set_error_handler('error');

// Start session
session_start();

// Set time zone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Set INI variables
ini_set('pcre.backtrack_limit', '4M');
ini_set('zlib.output_compression_level', '2');

// Set db variables
DB::$dbname = DBNAME;
DB::$dbuser = DBUSER;
DB::$dbpass = DBPASS;
DB::$dbhost = DBHOST;
DB::$dbport = DBPORT;

// Check if database installed
if (!DB::check_table('internal_components')) { 
	echo "We're sorry, but there is no database installed on this system.  Please ensure the database is correctly installed, and try again."; 
	exit(0);
}

// Load config vars and libraries
$config = array(); global $config;
$libs = array(); global $libs;
$result = DB::query("SELECT type,package,alias,value FROM internal_components WHERE type IN ('config', 'lib')");
while ($row = DB::fetch_assoc($result)) { 
	if ($row['type'] == 'lib') { $libs[$row['alias']] = $row['package']; }
	else { $config[$row['alias']] = $row['value']; }
}

// Set global variables
$GLOBALS['userid'] = 0;
$GLOBALS['_panel'] = 'public';
$GLOBALS['_theme'] = $config['theme_public'];
$GLOBALS['_request_type'] = 'default';

// Set session ID for logging
if (isset($_SESSION['_log_id']) && preg_match("/^(\d+)-(\d+)$/", "$_SESSION[_log_id]", $match)) { 
	$_SESSION['_log_id'] = $match[1] . '-' . ($match[2] + 1);
} else { $_SESSION['_log_id'] = rand(0, 99999); }


////////////////////////////////////////////////////////////
// Autoload classes
////////////////////////////////////////////////////////////

function autoload_class(string $class_name):bool {

	// Initialize
	global $libs;
	$class_file = strtolower($class_name);

	// Check for /lib/ file
	if (isset($libs[$class_file]) && file_exists(SITE_PATH . '/data/lib/' . $libs[$class_file] . '/' . $class_file . '.php')) { 
		require_once(SITE_PATH . '/data/lib/' . $libs[$class_file] . '/' . $class_file . '.php');
		return true;
	}

	// Check if /libstd/ exists
	$filename = SITE_PATH . '/data/libstd/' . $class_file . '.php';
	if (file_exists($filename)) {
		require_once($filename);
		return true;
	}
	

	// Check if /libstdthird_party// exists
	$filename = SITE_PATH . '/data/libstd/third_party/' . $class_file . '.php';
	if (file_exists($filename)) {
		require_once($filename);
		return true;
	}

	// Return false
	return false;


}




?>
