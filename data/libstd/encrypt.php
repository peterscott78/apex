<?php
declare(strict_types = 1);

class encrypt { 

////////////////////////////////////////////////////////////
// Get password hash
////////////////////////////////////////////////////////////

public function get_password_hash($password, $userid, $user_type = 'users'):string {

	// Get user info
	if (!$user_row = DB::get_idrow($user_type, $userid)) { trigger_error("User does not exist, ID# $userid", E_USER_ERROR); }


	// Generate salt
	$date_vars = explode(" ", preg_replace("/[-:]/", " ", $user_row['date_created']));
	$ip_vars = explode(" ", preg_replace("/[\.:]/", " ", $user_row['reg_ip']));
	$salt = array_sum($date_vars) + array_sum($ip_vars);

	// Ecncrypt
	$hash = $salt . $password . $salt;
	for ($x=1; $x < 32; $x++) { $hash = hash('sha512', $hash); }
	
	// Return
	return $hash;


}



}

?>
