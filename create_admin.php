<?php

require("load.php");

DB::query("DELETE FROM admin");

DB::insert('admin', array(
	'username' => 'envrin', 
	'password' => 'test')
);
//$admin_id = DB::insert_id();//
$admin_id = mysqli_insert_id(DB::$conn);

echo "ADMIN ID IS: $admin_id\n";

$enc = new encrypt();
$pass = $enc->get_password_hash('white4882', $admin_id, 'admin');

DB::query("UPDATE admin SET password = %s WHERE id = %i", $pass, $admin_id);

echo "HASH: $pass\n\n";

?>