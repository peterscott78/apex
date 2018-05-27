<?php

class form_core_admin { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct($data = array()) { 

	// Initialize
	global $config;

	// Set form fields
	$this->form_fields = array( 
		'sep1' => array('field' => 'seperator', 'label' => 'Login Credentials'), 
		'username' => array('field' => 'textbox', 'label' => 'Username', required => 1, datatype => 'alphanum'), 
		'password' => array('field' => 'textbox', 'type' => 'password', 'label' => 'Desired Password', 'required' => 1), 
		'confirm-password' => array('field' => 'textbox', 'type' => 'password', 'label' => 'Confirm Password', 'placeholder' => 'Confirm Password', 'required' => 1, 'equalto' => 'password'), 
		'full_name' => array('field' => 'textbox', 'label' => 'Full Name', 'placeholder' => 'Full Name', 'required' => 1), 
		'email' => array('field' => 'textbox', 'label' => 'E-Mail Address', 'required' => 1, 'datatype' => 'email'),  
		'require_2fa' => array('field' => 'boolean', 'label' => 'Require 2FA Authentication?', 'value' => 0)
	);

	// Security questions
	if ($config['num_security_questions_admin'] > 0) {
		$this->form_fields['sep2'] = array('field' => 'seperator', 'label' => 'Secondary Security Questions');
 
		for ($x=1; $x <= $config['num_security_questions_admin']; $x++) { 
			$this->form_fields['question' . $x] = array('field' => 'select', 'data_source' => 'hash:secondary_security_questions', 'label' => 'Question ' . $x, 'required' => 1);
			$this->form_fields['answer' . $x] = array('field' => 'textbox', 'label' => 'Answer ' . $x, 'required' => 1);
		}
	}


	// Add submit button
	$this->form_fields['submit'] = array('field' => 'submit', 'value' => 'create_first_admin', 'label' => 'Create First Administor');
		

}
}


?>
