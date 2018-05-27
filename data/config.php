<?php
define('DB_DRIVER', 'mysql');
define('DBNAME', 'apex');
define('DBUSER', 'boxer');
define('DBPASS', 'white4882');
define('DBHOST', 'localhost');
define('DBPORT', 3306);


//
// DEFAULT TIMEZONE
//        The default timezone used by the software for all users who are not logged in.
//
define('DEFAULT_TIMEZONE', 'America/New_York');


//
// LOGGING LEVEL
//       0 = No logging
//       1 = Error only
//       2 = Error and warnings
//
define('LOG_LEVEL', 2);


//
// USE STRICT ERROR REPORTING?
//        A boolean (0/1) that defines whether or display am error for 
//         PHP warnings / notices (1) or not display an error (0).
//
define('USE_STRICT', 0);


//
// DEBUG LEVEL
//        Integer of 0 - 3, defining the deb logging level.  0 for no debug logging, 
//        and 3 for the most extensive debug logging.
//
define('DEBUG_LEVEL', 3);


//
// COOKIE PREFIX / NAME
//        Rabdin string generated during installation, and used as a prefix for 
//        cookie variables within authentication sessions for better security.
//
define('COOKIE_NAME', 'dkg944kakrnoq');


//
// ENCRYPTION PASSWORD
//        Random string generatrf during installation that is used for two-way AES256 encryption.
//
define('ENCRYPT_PASS', 'Ksn4oa9PagNg49aIq3gk9');

?>
