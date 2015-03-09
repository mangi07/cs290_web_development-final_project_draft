<?php
session_start();

/*
AUTHOR:	Benjamin R. Olson
DATE:	March 8, 2015
COURSE: CS 290 - Web Development, Oregon State University
*/



//connect to the database
include ("db.php");

$user;
$json = "test";
$stmt;

if (isset($_SESSION["user"]) &&
	isset($_POST["loc"])){
	
	$user = $_SESSION["user"];
	$json = $_POST["loc"];
	$json = json_encode($json, JSON_FORCE_OBJECT);
	
	if(!($stmt = $mysqli->prepare(
		"UPDATE users SET locations = ? WHERE username = ?"
	))){
		echo "failed to save location";
	}
	
	if (!(
		$stmt->bind_param("ss", $json, $user) &&
		$stmt->execute()
	)) {
		echo "failed to save location";
		$stmt->close();
		$mysqli->close();
		die();
	}
	
	
	$stmt->close();
	$mysqli->close();
	
	echo "success";
	
} else {
	echo "failed to save location";
}


?>


