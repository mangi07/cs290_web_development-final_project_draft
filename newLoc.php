<?php

//connect to the database
include ("db.php");
//handle input errors
	echo "Successful response!<br>";
	if (isset($_POST["startDate"])){
		echo $_POST["startDate"] . "<br>";
	}

?>