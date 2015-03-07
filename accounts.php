<?php
session_start();

error_reporting(E_ALL);

if(isset($_POST["username"]) && 
	isset($_POST["password"]) &&
	isset($_POST["login_attempt"])){
	
	$username = $_POST["username"];
	$password = $_POST["password"];
	$login_attempt = $_POST["login_attempt"];
	
	//check that all fields have been filled in:
	/*
	if ($username == "")
		echo "<span style='color:red;'>
			Error: Username is required.
		</span><br>";
	if ($password == "")
		echo "<span style='color:red;'>
			Error: Password is required.
		</span><br>";
	if (preg_match('/\s/',$username))
		echo "<span style='color:red;'>
			Error: Username cannot contain any spaces.
		</span><br>";
	if (preg_match('/\s/',$password))
		echo "<span style='color:red;'>
			Error: Password cannot contain any spaces.
		</span><br>";
	*/
	if(!check_fields($username, $password)) die();
	
	//debug
	//echo "Debug from accounts.php line 16: " . $login_attempt;
	
	//check username and password against the database
	require("db.php");	//connect to the database
	if ($login_attempt){
		//database function call
		db_login($username, $password, $mysqli);
	} else {
		//database function call
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

//FINISHed!!
function db_login($user, $pass, $mysqli){
	// Prepared statement, stage 1: prepare
	if (!($stmt = $mysqli->prepare("SELECT password FROM users WHERE username = ?"))) {
		//debug
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		
		//actual message to user
		echo "Error: Failed to check the database for this user.<br>";
		
		//die();
		return;
	}

	// Prepared statement, stage 2: bind and execute
	// See http://php.net/manual/en/mysqli-stmt.bind-param.php
	//  for bind_param("i"... description
	if (!$stmt->bind_param("s", $user)) {
		//debug
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		
		//actual message to user
		echo "Failed to check the database for this user.<br>";
		
		//die();
		return;
	}

	if (!$stmt->execute()) {
		//debug
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		
		//actual message to user
		echo "Failed to check the database for this user.<br>";
		
		//die();
		return;
	}
	
	//get result of query, the password from the database
	$db_pass = NULL;
	if (!$stmt->bind_result($db_pass)) {
		//debug
		echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		
		//actual message to user
		echo "Failed to check the database for this user's password.<br>";
		
		//die();
		return;
	}
	if(!$stmt->fetch()){
		echo "This username does not exist.<br>";
		return;
	}
	//debug
	//echo "Debug: $db_pass<br>";//for now, this will show in the error section of login
	
	//(make sure the users table has been created)
	//check password
	if ($pass == $db_pass){
		echo "success";
		//create session variable to indicate successful login
		if(session_status() == PHP_SESSION_ACTIVE){
			$_SESSION["logged_in"] = 1;
		} else {
			echo "Error: unknown!<br>";
		}
	} else {
		echo "Wrong password!<br>";
		return;
	}
	
	
	
	//redirect to main interface, main.php
	/*
	$filePath = explode('/', $_SERVER['PHP_SELF'], -1);
		$filePath = implode('/', $filePath);
		$redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
		header("Location: {$redirect}/main.php", true);
		die();
	*/
}

//START WORKING ON FUNCTION TO CREATE USER, HERE...


?>