<?php
session_start();

//connect to the database
include ("db.php");

$user;
$json = "test";
$stmt;
//handle input errors
//echo "Successful response!<br>";
if (isset($_SESSION["user"])){
	//echo "$_SESSION[user] from userData.php<br>";
	$user = $_SESSION["user"];
	
	//when user wants to see another user's location...
	if (isset($_POST["user"])) {
		$user = $_POST["user"];
	}
	
	if(!($stmt = $mysqli->prepare(
		"SELECT locations FROM users WHERE username = ?"
	))){
		echo "fail";
		//echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!(
		$stmt->bind_param("s", $user) &&
		$stmt->execute() &&
		$stmt->bind_result($json) &&
		$stmt->fetch()
	)) {
		echo "fail";
		$stmt->close();
		die();
	}
	
	
	$stmt->close();

	echo $json;
	
}




?>