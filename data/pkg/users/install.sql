
CREATE TABLE users_groups (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	name VARCHAR(100) NOT NULL
) engine=InnoDB;

INSERT INTO users_groups (name) VALUES ('Member');

CREATE TABLE users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	username VARCHAR(100) NOT NULL UNIQUE, 
	password VARCHAR(160) NOT NULL DEFAULT '', 
	group_id SMALLINT NOT NULL, 
	status ENUM('active','inactive','pending') NOT NULL DEFAULT 'active', 
	sponsor INT NOT NULL DEFAULT 0, 
	verify_level TINYINT(1) NOT NULL DEFAULT 0, 
	require_2fa TINYINT(1) NOT NULL DEFAULT 0, 
	email_verified TINYINT(1) NOT NULL DEFAULT 0, 
	failed_logins SMALLINT NOT NULL DEFAULT 0,
	rating INT NOT NULL DEFAULT 0, 
	rating_total INT NOT NULL DEFAULT 0,  
	country VARCHAR(5) NOT NULL DEFAULT '',
	phone_country VARCHAR(5) NOT NULL DEFAULT '', 
	phone_number VARCHAR(20) NOT NULL DEFAULT '', 
	reg_ip VARCHAR(60) NOT NULL DEFAULT '', 
	date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) engine=InnoDB;

CREATE TABLE users_profile (
	id INT NOT NULL PRIMARY KEY, 
	full_name VARCHAR(255) NOT NULL DEFAULT '', 
	email VARCHAR(255) NOT NULL DEFAULT '', 
	about_me TEXT NOT NULL, 
	FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
) engine=InnoDB;


CREATE TABLE users_profile_fields (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	is_required TINYINT(1) NOT NULL DEFAULT 0, 
	allow_duplicates TINYINT(1) NOT NULL DEFAULT 1, 
	order_num SMALLINT NOT NULL, 
	form_field ENUM('textbox', 'textarea', 'select', 'radio', 'checkbox') NOT NULL DEFAULT 'textbox', 
	alias VARCHAR(50) NOT NULL UNIQUE, 
	display_name VARCHAR(100) NOT NULL, 
	options TEXT NOT NULL
) engine=InnoDB;

INSERT INTO users_profile_fields (is_required, order_num, alias, display_name, options) VALUES (1, 1, 'full_name', 'Full Name', '');
INSERT INTO users_profile_fields (is_required, order_num, alias, display_name, options) VALUES (1, 2, 'email', 'E-Mail Address', '');


