
DROP TABLE IF EXISTS internal_components;
DROP TABLE IF EXISTS internal_packages;
DROP TABLE IF EXISTS internal_repos;
DROP TABLE IF EXISTS cms_pages;
DROP TABLE IF EXISTS cms_menus;
DROP TABLE IF EXISTS admin_auth_sessions;
DROP TABLE IF EXISTS admin_security_questions;
DROP TABLE IF EXISTS admin;



--------------------------------------------------
-- Internal tables
--------------------------------------------------

 
CREATE TABLE internal_repos (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	is_active TINYINT(1) NOT NULL DEFAULT 1, 
	date_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
	url VARCHAR(255) NOT NULL, 
	display_name VARCHAR(255) NOT NULL, 
	description TEXT NOT NULL
) engine=InnoDB;	

INSERT INTO internal_repos (url,display_name,description) VALUES ('http://repo.envrin.com', 'Envrin Main Repository', 'The main, public repository for the Apex Framework.');


CREATE TABLE internal_packages (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	in_development TINYINT(1) NOT NULL DEFAULT 0,
	repo_id INT NOT NULL, 
version VARCHAR(15) NOT NULL DEFAULT '0.0.0', 
	prev_version VARCHAR(15) NOT NULL DEFAULT '0.0.0',
	date_installed TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
	last_modified DATETIME,  
	alias VARCHAR(100) NOT NULL UNIQUE, 
	display_name VARCHAR(255) NOT NULL, 
	FOREIGN KEY (repo_id) REFERENCES internal_repos (id) ON DELETE CASCADE
) engine=InnoDB;
INSERT INTO internal_packages (repo_id, version, alias, display_name) VALUES (1, '1.0.0.0', 'core', 'Core Framework');


CREATE TABLE internal_components (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	order_num SMALLINT NOT NULL DEFAULT 0, 
	type VARCHAR(15) NOT NULL, 
	package VARCHAR(100) NOT NULL, 
	parent VARCHAR(255) NOT NULL DEFAULT '', 
	alias VARCHAR(255) NOT NULL, 
	value TEXT NOT NULL, 
	FOREIGN KEY (package) REFERENCES internal_packages (alias) ON DELETE CASCADE
) engine=InnoDB;

--------------------------------------------------
-- CMS 
--------------------------------------------------

CREATE TABLE cms_pages (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	area VARCHAR(100) NOT NULL DEFAULT 'public', 
	layout VARCHAR(255) NOT NULL DEFAULT 'default', 
	title VARCHAR(255) NOT NULL DEFAULT '', 
	filename VARCHAR(255) NOT NULL
) engine=InnoDB;

CREATE TABLE cms_menus ( 
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	package VARCHAR(100) NOT NULL, 
	area VARCHAR(50) NOT NULL DEFAULT 'members', 
	is_active TINYINT(1) NOT NULL DEFAULT 1, 
	is_system TINYINT(1) NOT NULL DEFAULT 1, 
	require_login TINYINT(1) NOT NULL DEFAULT 0, 
	order_num SMALLINT NOT NULL DEFAULT 0, 
	link_type ENUM('internal','external','parent','header') NOT NULL DEFAULT 'internal', 
	icon VARCHAR(100) NOT NULL DEFAULT '', 
	parent VARCHAR(100) NOT NULL DEFAULT '', 
	alias VARCHAR (100) NOT NULL, 
	display_name VARCHAR(100) NOT NULL, 
	url VARCHAR(255) NOT NULL DEFAULT ''
) engine=InnoDB;


--------------------------------------------------
-- Admin tables
--------------------------------------------------

CREATE TABLE admin (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	status ENUM('active','inactive') NOT NULL DEFAULT 'active', 
	require_2fa TINYINT(1) NOT NULL DEFAULT 0, 
	invalid_logins INT NOT NULL DEFAULT 0, 
	last_seen INT NOT NULL DEFAULT 0, 
	sec_hash VARCHAR(130) NOT NULL DEFAULT '', 
	date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,      
	username VARCHAR(255) NOT NULL UNIQUE, 
	password VARCHAR(130) NOT NULL DEFAULT '', 
	full_name VARCHAR(255) NOT NULL DEFAULT '', 
	email VARCHAR(255) NOT NULL DEFAULT ''
) engine=InnoDB;

CREATE TABLE admin_auth_sessions                                                                                                                                                                                                                                    ( 
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	userid INT NOT NULL, 
	last_active INT NOT NULL DEFAULT 0, 
	login_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
	logout_date DATETIME, 
	auth_hash VARCHAR(130) NOT NULL UNIQUE, 
	2fa_status TINYINT(1) NOT NULL DEFAULT 0, 
	2fa_hash VARCHAR(130) NOT NULL DEFAULT '', 
	FOREIGN KEY (userid) REFERENCES admin (id) ON DELETE CASCADE
) engine=InnoDB;

CREATE TABLE admin_security_questions ( 
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	userid INT NOT NULL, 
	question VARCHAR(5) NOT NULL, 
	answer VARCHAR(255) NOT NULL, 
	FOREIGN KEY (userid) REFERENCES admin (id) ON DELETE CASCADE
) Engine=InnoDB;

CREATE TABLE admin_allowips ( 
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	userid INT NOT NULL, 
	ip_address VARCHAR(40) NOT NULL, 
	FOREIGN KEY (userid) REFERENCES admin (id) ON DELETE CASCADE
) engine=InnoDB;



--------------------------------------------------
-- Notifications
--------------------------------------------------

CREATE TABLE notifications (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	is_active TINYINT(1) NOT NULL DEFAULT 1, 
	controller VARCHAR(100) NOT NULL, 
	sender VARCHAR(30) NOT NULL, 
	recipient VARCHAR(30) NOT NULL, 
	content_type ENUM('text/plain', 'text/html') NOT NULL DEFAULT 'text/plain', 
	subject VARCHAR(255) NOT NULL,
	contents LONGTEXT NOT NULL, 
	condition_vars TEXT NOT NULL
) Engine=InnoDB;

CREATE TABLE notifications_attachments ( 
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	notification_id INT NOT NULL, 
	mime_type VARCHAR(100) NOT NULL, 
	filename VARCHAR(255) NOT NULL, 
	contents LONGTEXT NOT NULL, 
	FOREIGN KEY (notification_id) REFERENCES notifications (id) ON DELETE CASCADE
) engine=InnoDB;


