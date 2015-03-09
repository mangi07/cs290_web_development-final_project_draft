<?php
session_start();

/*
AUTHOR:	Benjamin R. Olson
DATE:	March 8, 2015
COURSE: CS 290 - Web Development, Oregon State University
*/


?>

<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>CS 290 Final Project - Ben R. Olson</title>
	
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript" src="jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="index.js"></script>
</head>

<body>
	<div class="centered box">
		<h1>Personal Map</h1>
		<h3>Created By Ben R. Olson</h3>

		<p>Login or Create an Account</p>
		<p>Username: <input id="userfield" type="text"></p>
		<p>Password: <input id="passfield" type="password"></p>
		<button id="login" class="button">Login</button>
		<button id="create_user" class="button">Create User</button>
		<p id="errors" style="color:red;"></p>
	</div>
</body>


