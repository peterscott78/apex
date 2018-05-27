<?php

class form_users_users_register { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct($data = array()) {

	// Initialize
	global $config;
	$username_field = $config['username_column'] == 'email' ? 'E-Mail Address' : 'Username';
	$username_datatype = $config['username_column'] == 'email' ? 'email' : 'alphanum';

	// Set form fields
	$this->form_fields = array(
		'sep1' => array('field' => 'seperator', 'label' => 'Login Details'), 
		'username' => array('field' => 'textbox', 'label' => $username_field, 'placeholder' => 'Desired Username', 'required' => 1, 'datatype' => $username_datatype), 
		'password' => array('field' => 'textbox', 'type' => 'password', 'label' => 'Desired Password', 'required' => 1), 
		'password2' => array('field' => 'textbox', 'type' => 'password', 'label' => 'Confirm Password', 'required' => 1, 'equalto' => '#input_password')
	);

	// Add profile fields
	$first = true;
	$result = DB::query("SELECT * FROM users_profile_fields ORDER BY order_num");
	while ($row = DB::fetch_assoc($result)) { 
		if ($config['username_column'] == 'email' && $row['alias'] == 'email') { continue; }

		// Add seperator, if needed
		if ($first === true) { 
			$this->form_fields['sep2'] = array('field' => 'seperator', 'label' => 'Profile');
			$first = false;
		}

		// Add form field
		$this->form_fields[$row['alias']]= array('field' => $row['form_field'], 'label' => $row['display_name'], 'required' => $row['is_required']);
	}

	// Add submit button
	$this->form_fields['submit'] = array('field' => 'submit', 'value' => 'create_user', 'label' => 'Create New User', 'has_reset' => 1);








}

}

?>
