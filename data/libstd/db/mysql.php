<?php

class DB {

	public static $dbname = '';
	public static $dbuser = '';
	public static $dbpass = '';
	public static $dbhost = 'localhost';
	public static $dbport = 3306;

	public static $is_connected = false;
	public static $conn;
	public static $tables = array();
	public static $columns = array();
	
	
	





////////////////////////////////////////////////////////////
// Connect
////////////////////////////////////////////////////////////

public static function dbconnect() {
	

	// Check if connected
	if (self::$is_connected === true) { return $this->conn; }

		// Connect
	if (!self::$conn = mysqli_connect(self::$dbhost, self::$dbuser, self::$dbpass, self::$dbname, self::$dbport)) { 
		echo "Unable to connect to the mySQL database using the supplied information.  Please ensure the mySQL server is running, and the right connection information is in the /data/config.php file.", E_USER_ERROR; exit(0);
		exit(0);
	}
	self::$is_connected = true;

	

	
}

////////////////////////////////////////////////////////////
// Show tables
////////////////////////////////////////////////////////////

public static function show_tables() {

	// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// Check IiF TABLES ALREADY RETRIEVED
	IF (COUNT(self::$tables) > 0) { RETURN self::$tables; }

	// Get tables
	$result = self::query("SHOW TABLES");
	while ($row = self::fetch_array($result)) { 
		self::$tables[] = $row[0];
	}

	// Return
	return self::$tables;
	

}

////////////////////////////////////////////////////////////
// Show column names
////////////////////////////////////////////////////////////

public static function show_columns($table_name) {

	// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// cHECK IF COLUMNS ALREADY GOTTEN
if (isset(self::$columns[$table_name]) && is_array(self::$columns[$table_name]) && count(self::$columns[$table_name])) { return self::$columns[$table_name]; }
	

	// Get column names
	self::$columns[$table_name] = array();
	$result = self::query("DESCRIBE $table_name");
	while ($row = self::fetch_array($result)) { 
		self::$columns[$table_name][] = $row[0];
	}

	// Return
	return self::$columns[$table_name];
	
}


////////////////////////////////////////////////////////////
// Insert
////////////////////////////////////////////////////////////

public static function insert() {

	// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// Get args
$args = func_get_args();	
	$table_name = array_shift($args);

	// Check if table exists
	if (!self::check_table($table_name)) {
		self::dberror('insert', 'no_table', '', $table_name);
	}

	// Set variables
	$values = array();
	$placeholders = array();
	$columns = self::show_columns($table_name);

	// Generate SQL
	$sql = "INSERT INTO $table_name (" . implode(',', array_keys($args[0])) . ") VALUES (";
	foreach ($args[0] as $column => $value) {
		
		// Check if column exists
		if (!in_array($column, $columns)) { self::dberror('insert', 'no_column', '', $table_name, $column); }

		// Add variables to sql
		$placeholders[] = '%s';
		$values[] = $value;
	}
	$sql .= implode(", ", $placeholders) . ')';

	// Format SQL
	array_unshift($values, $sql);
	$sql = self::format_sql($values);



	// Execute SQL
	self::query($sql);

}


////////////////////////////////////////////////////////////
// Update
////////////////////////////////////////////////////////////

public static function update() {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// Get arts
	$args = func_get_args();
	$table_name = array_shift($args);
	$updates = array_shift($args);

	// Check if table exists
	if (!self::check_table($table_name)) { self::dberror('update', 'no_table', '', $table_name); }

	// Set variables
	$values = array();
	$placeholders = array();
	$columns = self::show_columns($table_name);

	// Generate SQL
	$sql = "UPDATE $table_name SET ";
	foreach ($updates as $column => $value) { 
		if (!in_array($column, $columns)) { self::dberror('update', 'no_column', '', $table_name, $column); }
		$placeholders[] = "$column = %s";
		$values[] = $value;
	}


	// Finish SQL
	$sql .= implode(", ", $placeholders);
	array_unshift($values, $sql);
	$sql = self::format_sql($values);

	// Add where to SQL, if needed
	if (isset($args[0]) && isset($args[1])) {
		$sql .= " WHERE " . self::format_sql($args);
	}

	// Execute  SQL
self::query($sql);



}

////////////////////////////////////////////////////////////
// Delete
////////////////////////////////////////////////////////////

public static function delete() {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// Get arts
	$args = func_get_args();
	$table_name = array_shift($args);

	// Check if table exists
	if (!self::check_table($table_name)) { self::dberror('delete', 'no_table', '', $table_name); }
	// Format SQL
	$sql = "DELETE FROM $table_name";
	if (isset($args[0]) && $args[0] != '') { $sql .= ' WHERE ' . self::format_sql($args); }

	// Execute SQL
self::query($sql);


}


////////////////////////////////////////////////////////////
// Get row
////////////////////////////////////////////////////////////

public static function get_row() {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

// Format SQL
	$args = func_get_args();
	$sql = self::format_sql($args);

	// Get first row
	$result = self::query($sql);
	if (!$row = self::fetch_assoc($result)) { return false; }

	// Return
	return $row;


}


////////////////////////////////////////////////////////////
// Get row by OD
////////////////////////////////////////////////////////////

public static function get_idrow($table_name, $id_number) {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	//Check table
	if (!self::check_table($table_name)) { self::dberror('get_idrow', 'no_table', '', $table_name); }

		// Get first row
	if (!$row = DB::get_row("SELECT * FROM $table_name WHERE id = %s ORDER BY id LIMIT 0,1", $id_number)) { 
		return false;
	}

	// Return
	return $row;

}



////////////////////////////////////////////////////////////
// Get column
////////////////////////////////////////////////////////////

public static function get_column() {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// Format SQL
	$args = func_get_args();
	$sql = self::format_sql($args);

	// Get column
	$cvalues = array();
	$result = self::query($sql);
	while ($row = self::fetch_array($result)) { 
		$cvalues[] = $row[0];
	}

// Return
	return $cvalues;


}

////////////////////////////////////////////////////////////
// Get field
////////////////////////////////////////////////////////////

public static function get_field() {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }

	// Format SQL
	$args = func_get_args();
	$sql = self::format_sql($args);

	// Execute SQL query
	if (!$result = mysqli_query(self::$conn, $sql)) {
self::dberror('query', 'general', $sql);
	}

	// Return result
	if (!$row = self::fetch_array($result)) { return false; }
	return $row[0];

}


////////////////////////////////////////////////////////////
// Query
////////////////////////////////////////////////////////////

public static function query() {

// Connect
	if (self::$is_connected === false) { self::dbconnect(); }


	//Format SQL
	$args = func_get_args();
	$sql = self::format_sql($args);


	// Execute SQL query
	if (!$result = mysqli_query(self::$conn, $sql)) {
self::dberror('query', 'general', $sql);
	}

	// Return
	return $result;

}
////////////////////////////////////////////////////////////
// Fetch array
////////////////////////////////////////////////////////////

public static function fetch_array($result) {

	// Get row
	if (!$row = mysqli_fetch_array($result)) {
		return false;
	}

	// Return
	return $row;

}

////////////////////////////////////////////////////////////
// Fetch assoc
////////////////////////////////////////////////////////////

public static function fetch_assoc($result) {

	// Get row
	if (!$row = mysqli_fetch_assoc($result)) {
		return false;
	}

	// Return
	return $row;

}
////////////////////////////////////////////////////////////
// Num rows
////////////////////////////////////////////////////////////

public static function num_rows($result) {

	// Get num rows
	if (!$num = mysqli_num_rows($result)) { self::dberror('num_rows', 'general', ''); }
	if ($num == '') { $num = 0; }

	// Return
	return $num;

}


////////////////////////////////////////////////////////////
// Get insert Id
////////////////////////////////////////////////////////////

public static function insert_id() {

	// Get insert ID
	return mysqli_insert_id(self::$conn);

}


////////////////////////////////////////////////////////////
// Format SQL
////////////////////////////////////////////////////////////

protected static function format_sql($args) {

	// Go through args
	$x=1;
	preg_match_all("/\%(\w+)/", $args[0], $args_match, PREG_SET_ORDER);
	foreach ($args_match as $match) {
		$value = isset($args[$x]) ? $args[$x] : '';

		// Check data type
		$is_valid = true;
		if ($match[1] == 'i' && !filter_var($value, FILTER_VALIDATE_INT)) { $is_valid = false; }
		elseif ($match[1] == 'd' && !filter_var($value, FILTER_VALIDATE_FLOAT)) { $is_valid = false; }
		elseif ($match[1] == 'b' && !filter_var($value, FILTER_VALIDATE_BOOLEAN)) { $is_valid = false; }

	elseif ($match[1] == 'e' && !filter_var($value, FILTER_VALIDATE_EMAIL)) { $is_valid = false; }
		elseif ($match[1] == 'url' && !filter_var($value, FILTER_VALIDATE_URL)) { $is_valid = false; }
		elseif ($match[1] == 'ds') { 
			if (preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d)$/", $value, $dmatch)) { 
				if (!check_date($dmatch[2], $dmatch[3], $dmatch[1])) { $is_valid = false; }
			} else { $is_valid = false; }
		} elseif ($match[1] == 'ts' && !preg_match("/^\d\d:\d\d:\d\d$/", $value)) { $is_valid = false; }
		elseif ($match[1] == 'dt') { 
			if (preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d) \d\d:\d\d:\d\d$/", $value, $dmatch)) { 
				if (!check_date($dmatch[2], $dmatch[3], $dmatch[1])) { $is_valid = false; }
			} else { $is_valid = false; }
		}
		

		// Process invalid argument, if needed
		if ($is_valid === false) {
				self::process_invalid_sql($args[0], $x, $match[1], $value);
		}
		// Format value
		$value = mysqli_real_escape_string(self::$conn, $value);
		$value = $match[1] == 'ls' ? "'%" . $value . "%'" : "'" . $value . "'";

		$args[0] = preg_replace("/$match[0]/", $value, $args[0], 1);
	
		

	$x++; }


	// Return
return $args[0];

}


////////////////////////////////////////////////////////////
// Process invalid SQL
////////////////////////////////////////////////////////////

protected static function process_invalid_sql($sql, $num, $type, $value) {

	// Trigger error
	$errmsg = "Unable to execute SQL statement -- $sql<br />\n<br />\nInvalid formatted argument -- #" . $num . " -- $type -- $value<br /\n";
	trigger_error($errmsg, E_USER_ERROR);
 

}

////////////////////////////////////////////////////////////
// Check table
////////////////////////////////////////////////////////////

public static function check_table($table_name) {

	// Get table names
	$tables = self::show_tables();
	$ok = in_array($table_name, $tables) ? true : false;

	// Return
	return $ok;

}

////////////////////////////////////////////////////////////
// Begin transaction
////////////////////////////////////////////////////////////

public static function begin_transaction() {

	// Begin transaction
	if (!mysqli_begin_transaction(self::$conn)) { 
		self::dberror('begin_transaction', 'begin_transaction');
	}

	//EwReturn
	return true;

} 

////////////////////////////////////////////////////////////
// Commit
////////////////////////////////////////////////////////////

public static function commit() {

	// Commit transaction
	if (!mysqli_commit(self::$conn)) { 
		self::dberror('commit', 'commit');
	}

	//EwReturn
	return true;

} 

////////////////////////////////////////////////////////////
// Rollback
////////////////////////////////////////////////////////////

public static function rollback() {

	// Rollback transaction
	if (!mysqli_rollback(self::$conn)) { 
		self::dberror('rollback', 'rollback');
	}

	//EwReturnr
	return true;
 
} 


////////////////////////////////////////////////////////////
// Database error
////////////////////////////////////////////////////////////

protected static function dberror($action, $errtype, $sql = '', $table = '', $column = '' ) {

	// Get variables
	$action = strtoupper($action);

	// Get error message
if ($errtype == 'no_table') { $errmsg = "Unable to perform $action on table name '$table', as table does not exist within the database."; }
	elseif ($errtype == 'no_column') {$errmsg = "Unable to perform $action on column '$column' within the table '$table', as column does not exist within table."; }
	elseif ($errtype == 'num_rows') { $errmsg = "Unable to retrieve number of affected rows.  " . mysqli_error(self::$conn); }
		elseif ($errtype == 'insert_id') { $errmsg = "Unable to determine latest insert ID.  " . mysqli_error(self::$conn); }
		elseif ($errtype == 'begin_transaction') { $errmsg = "Unable to begin transaction.  " . mysqli_error(self::$conn); }
		elseif ($errtype == 'commit') { $errmsg = "Unable to commit transaction.  " . mysqli_error(self::$conn); }
		elseif ($errtype == 'rollback') { $errmsg = "Unable to rollback transaction.  " . mysqli_error(self::$conn); }
	else { $errmsg = "Unable to execute SQL statement -- $sql.<br><br>\n\n " . mysqli_error(self::$conn); }

	// Trigger errir
	trigger_error("SQL Database Error.  $errmsg", E_USER_ERROR);
 


}

}
?>
