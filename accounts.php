<?php
session_start();

/*
AUTHOR:	Benjamin R. Olson
DATE:	March 8, 2015
COURSE: CS 290 - Web Development, Oregon State University
*/



//check if user is already logged in
if(isset($_SESSION['user'])){
	echo "Note: You are already logged in.<br>";
	echo "<button onclick='window.location.href = \"main.php\"'>User Page</button>";
	die();
}

if(isset($_POST["username"]) && 
	isset($_POST["password"]) &&
	isset($_POST["login_attempt"])){
	
	$username = $_POST["username"];
	$password = $_POST["password"];
	$login_attempt = $_POST["login_attempt"];
	
	//check that all fields have been filled in:
	if(!check_fields($username, $password)) die();
	
	//check username and password against the database
	require("db.php");	//connect to the database
	if ($login_attempt == 'true'){
		//database function call
		db_login($username, $password, $mysqli);
	} else if (isset($_POST["json"])){
		//database function call
		create_user($username, $password, $mysqli, $_POST["json"]);
	}
	$mysqli->close();
	
} else {
	echo "<span style='color:red;'>
		Sorry...Unknown error from php!
		</span>";
}


//returns true if username and password pass the checks, else returns false
function check_fields($user, $pass){
	$checks_passed = true;

	if ($user == ""){
		echo "Error: Username is required.<br>";
		$checks_passed = false;
	}
	if ($pass == ""){
		echo "Error: Password is required.<br>";
		$checks_passed = false;
	}
	if (preg_match('/\s/',$user)){
		echo "Error: Username cannot contain any spaces.<br>";
		$checks_passed = false;
	}
	if (preg_match('/\s/',$pass)){
		echo "Error: Password cannot contain any spaces.<br>";
		$checks_passed = false;
	}
	
	return $checks_passed;
}

//if the username exists and the password is correct,
//  this will allow user to access main.php
function db_login($user, $pass, $mysqli){
	// Prepared statement, stage 1: prepare
	if (!($stmt = $mysqli->prepare("SELECT password FROM users WHERE username = ?"))) {
		//actual message to user
		echo "Error: Failed to check the database for this user.<br>";
		return;
	}

	// Prepared statement, stage 2: bind and execute
	if (!$stmt->bind_param("s", $user)) {
		//message to user
		echo "Failed to check the database for this user.<br>";
		return;
	}

	if (!$stmt->execute()) {
		//message to user
		echo "Failed to check the database for this user.<br>";
		return;
	}
	
	//get result of query, the password from the database
	$db_pass = NULL;
	if (!$stmt->bind_result($db_pass)) {
		//message to user
		echo "Failed to check the database for this user's password.<br>";
		return;
	}
	
	if(!$stmt->fetch()){
		echo "This username does not exist.<br>";
		return;
	}
	
	//check password and create session variable if password is correct
	if ($pass == $db_pass){
		echo "success";
		//create session variable to indicate successful login
		if(session_status() == PHP_SESSION_ACTIVE){
			$_SESSION["user"] = $user;
		} else {
			echo "Error: unknown!<br>";
		}
	} else {
		echo "Wrong password!<br>";
		return;
	}

}

//if the username already exists, this should fail,
//  else a new username and password will be entered as a row in the db table,
//  and then the new user will have access to main.php through a session variable
function create_user($user, $pass, $mysqli, $json){
	
	$json = json_encode($json, JSON_FORCE_OBJECT);
	
	// Prepared statement, stage 1: prepare
	if (!($stmt = $mysqli->prepare("INSERT INTO users VALUES (?, ?, ?)"))) {
		//message to user
		echo "Error: Failed to check the database for this user.<br>";	
		return;
	}

	// Prepared statement, stage 2: bind and execute
	if (!$stmt->bind_param("sss", $user, $pass, $json)) {
		//message to user
		echo "Failed to add this user to the database.<br>";	
		return;
	}

	if (!$stmt->execute()) {
		//message to user
		echo "Failed to add this user to the database.  The user may already exist.<br>";
		return;
	}
	
	echo "success";
	//create session variable to indicate successful login
	if(session_status() == PHP_SESSION_ACTIVE){
		$_SESSION["user"] = $user;
	} else {
		echo "Error: unknown!<br>";
	}
	
}

?>


