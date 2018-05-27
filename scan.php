<?php

require("load.php");

$package = $argv[1] ?? 'users';

$client = new package($package);
$client->install_configuration();

echo "Package All Installed ---- $package\n";

?>
