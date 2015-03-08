<?php
session_start();

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
		//echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!(
		$stmt->bind_param("ss", $json, $user) &&
		$stmt->execute()
	)) {
		echo "fail to save location";
		$stmt->close();
		die();
	}
	
	
	$stmt->close();

	echo "success";
	
} else {
	echo "fail to save location";
}




?>