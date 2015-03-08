<?php
session_start();

error_reporting(E_ALL);

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
	
	//DEFINE AND PROVIDE THE STRUCTURE OF THE JSON OBJECT EACH USER'S DATA WILL HAVE
	/*
	$json = json_encode(array('username' => array($user => array('entries' =>
		array(
			'loc_name' => null,
			'coords' => array('lat' => null, 'lng' => null),
			'timeframe' => array('day' => null, 'month' => null, 'year' => null)
	)))));
	*/
	/*
	$json = "{'users':['username':'$user','entries':[{'coords': {'lat':null, 'lng':null},'loc_name': null,'timeframe': {'day':null, 'month':null, 'year':null}}]]}
	";
	*/
	//$json = json_encode($json, JSON_FORCE_OBJECT);
	/*
	{'users':
		[
		'username':$user,
		'entries':
			[
				{ 'coords': {'lat':null, 'lng':null},
				'loc_name': null,
				'timeframe': {'day':number, 'month':number, 'year':number} }
			]
		]
	}
	*/
	$json = json_encode($json, JSON_FORCE_OBJECT);
	//$json = json_encode(array('user' => $user, $json)), JSON_FORCE_OBJECT);
	
	// Prepared statement, stage 1: prepare
	if (!($stmt = $mysqli->prepare("INSERT INTO users VALUES (?, ?, ?)"))) {
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
	if (!$stmt->bind_param("sss", $user, $pass, $json)) {
		//debug
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		
		//actual message to user
		echo "Failed to add this user to the database.<br>";
		
		//die();
		return;
	}

	if (!$stmt->execute()) {
		//debug
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		
		//actual message to user
		echo "Failed to add this user to the database.  The user may already exist.<br>";
		
		//die();
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