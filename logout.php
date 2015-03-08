<?php
session_start();

	$_SESSION = array();
	session_destroy();
	$filePath = explode('/', $_SERVER['PHP_SELF'], -1);
	$filePath = implode('/', $filePath);
	$redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
	header("Location: {$redirect}/index.php", true);
	//or use JavaScript:
	//window.location.href = "index.php";
	die();
	
	
?>