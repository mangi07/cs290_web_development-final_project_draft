<?php

include 'storedInfo.php';

$mysqli = new mysqli("localhost", "root", $myPassword, "final_project_db");
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	//echo "Connection successful!<br>";
}

//close this connection with $mysqli->close(); in files in which it is used.

/*USE THE FOLLOWING FOR TABLE CREATION and queries:

CREATE TABLE users(
		username VARCHAR(255) PRIMARY KEY, 
		password VARCHAR(255) DEFAULT NULL CHECK ( password != '' AND password != NULL),
		locations LONGTEXT
		);
		
INSERT INTO users (?) VALUES (?) WHERE username = ?;

//put this in a for loop:
SELECT locations FROM `users` WHERE username = ?;

*/

?>

