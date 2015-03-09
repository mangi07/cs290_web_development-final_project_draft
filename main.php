<?php
session_start();

/*
AUTHOR:	Benjamin R. Olson
DATE:	March 8, 2015
COURSE: CS 290 - Web Development, Oregon State University
*/


//check for login
if (!isset($_SESSION['user'])){
	echo "You must be logged in to view this page.<br>
		<button onclick='window.location.href = \"index.php\"'>Log In</button>
	";
	die();
}

?>


<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title>CS 290 Final Project - Ben R. Olson</title>
	
	<link rel="stylesheet" type="text/css" href="style.css" />
	
	<!--leaflet links and scripts-->
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html.js">
	<![endif]-->
	<link rel="stylesheet" href="leaflet/leaflet.css" />
	<!--[if IE]>
		<link rel="stylesheet" href="leaflet/dist/leaflet.ie.css" />
	<![endif]-->
	<script src="leaflet/leaflet.js"></script>
	
	
	<!--jQuery link needed BEFORE trying to load calendar plugin based on jQuery!!-->
	<script type="text/javascript" src="jquery-1.8.3.min.js"></script>
	
	<!--datepicker links and scripts: -->
	<link rel="stylesheet" media="screen" type="text/css" href="datepicker/css/datepicker.css" />
	<script type="text/javascript" src="datepicker/js/datepicker.js"></script>
	
	<!--link to main JavaScript-->
	<script type="text/javascript" src="main.js"></script>
</head>
<body class="centered">


<?php
	echo "<div class='box'>";
	echo "<h1>Personal Map</h1>";
	echo "<h3>Created By Ben R. Olson</h3>";
	echo "<h2>Logged In As \"$_SESSION[user]\"</h2></div>";
	echo "</div>";
?>


	<!-- Logout functionality provided in main.js (160-166) is attached to this button: -->
	<div onclick="window.location.href = 'logout.php'" class="button">Log Out</div>
	

	<div id="mapContainer" class="horizontal">
		<div id="map"></div>
		<h3>Instructions</h3>
		<p class="box">If you've already entered your location...<br>
			Click on the map pin to see information about your location.<br>
			If you don't see the pin, try zooming out or dragging the map.
		</p>
	</div>
	
	<!-- To enter all data for a new location to be placed on the user's map -->
	<div id="newEntry" class="horizontal">
		<h2 title="Add a location if none exist.">Add Or Update Your Location</h2>
		
		<h3 class="box">Location Name: <input id="loc_name" type="text"></h3>
		
		<!-- To choose the range of days the user was at a location: -->
		<div id="dateContainer" class="box">
			<h3 title="When were you there?">Timeframe</h3>
			<div id="date">
				<div class="horizontal">
					<h3>Start Date</h3>
					<div id="startDate"></div>
				</div>
				
				<div class="horizontal">
					<h3>End Date</h3>
					<div id="endDate"></div>
				</div>
			</div>
		</div>
		
		<div class="box">
			<h3 title="Click on the map to get your coordinates.">Location</h3>
			<p id="lat">Latitude: not yet selected.</p>
			<p id="lng">Longitude: not yet selected.</p>
		</div>
		
		<!-- using $.post() -->
		<button class="ajax button">Submit</button>
		<div id="newEntryErrors"></div>
	</div>
	
	
</body>

</html>
