<?php
declare(strict_types = 1);

// Load
require_once('./load.php');

// Parse URI, determine page to display
$route = isset($_GET['route']) ? strtolower(trim($_GET['route'], '/')) : 'index';
$parts = explode('/', strtolower($route));

// Load controller class
$GLOBALS['_request_type'] = component_exists('controller', $parts[0], 'core', 'http_requests') === true ? array_shift($parts) : 'default';
$controller = load_component('controller', $GLOBALS['_request_type'], 'core', 'http_requests');
if ($controller != 'default') { $_GET['route'] = implode('/', $parts); }

// Process request
$controller->process($parts);
exit(0);

?>
