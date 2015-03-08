<?php
session_start();

//code here to check for login
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
	
	
	<!--jquery link needed BEFORE trying to load calendar plugin based on jQuery!!-->
	<script type="text/javascript" src="jquery-1.8.3.min.js"></script>
	
	<!--datepicker links and scripts: -->
	<link rel="stylesheet" media="screen" type="text/css" href="datepicker/css/datepicker.css" />
	<script type="text/javascript" src="datepicker/js/datepicker.js"></script>
	
	<!--link to main JavaScript-->
	<script type="text/javascript" src="main.js"></script>
	<!--
	<script>
		window.onload = function (){
			//variables
			var markers = new Array();//use JSON.stringify() and JSON.parse()
			//for going to and from database
			//JSON structure of array: 
			//  {"location":["loc_name": "London", "coords":[54,-33], "timeframe":[Date(), Date()]]}
			//use date api to select dates:
			//  http://www.eyecon.ro/datepicker/#implement
			var userData = new Array();
			//each user is a row in the table?
			//HOW TO STORE JSON IN DB:
			//  varchar
			//GUI: 1) list users to display
			//  2) click (or doubleclick?) on map to add location
			//    coordinates will be displayed to user and 
			//    included in location object
			
			
			
			//everything for our map goes here
			var map = L.map('map', {
				center: [30.0, 0.0],
				zoom: 2,
			});
			//var map = L.map('map').setView([51.505, -0.09], 13);
			L.tileLayer('http://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwMTMiLCJhIjoieHFnSkh1RSJ9.pbbOa2J6sV8g_qLAr0E45Q', {
				attribution: 'GISC Video Tutorial',
				maxZoom: 18
			}).addTo(map)
			
			var marker = L.marker([51.5, -0.09]).addTo(map);
			marker.bindPopup("<b>Hello world!</b><br>I am a popup.");
			//how to remove a marker??
			
			var popup = L.popup();
			function onMapClick(e) {
				//alert("You clicked the map at " + e.latlng);
				//have this say: "You clicked this location to be saved."
				popup
				.setLatLng(e.latlng)
				.setContent("You clicked the map at " + e.latlng.toString())
				.openOn(map);
			}
			map.on('click', onMapClick);
			
			//datepicker code:
			/*
			$('#date').DatePicker({
				flat: true,
				date: '2008-07-31',
				current: '2008-07-31',
				calendars: 1,
				starts: 0
			});
			*/
			
			$('#date').DatePicker({
				flat: true,
				format:'m/d/Y',
				date: '2015-03-04',
				current: '2015-03-04',
				starts: 0,
				//position: 'r',
				onBeforeShow: function(){
					$('#inputDate').DatePickerSetDate($('#inputDate').val(), true);
				},
				onChange: function(formatted, dates){
					//$('#inputDate').val(formated);
					$('#inputDate').text(formatted);
				}
			});
			
			
			//or use $.post() ??
			$('button.ajax').on('click', function() {
				var location = $('#inputDate').text();
				$.post( "newLoc.php", { loc: location })
					.done(function( data ) {
						alert( "Data Loaded: " + data );
					});
			});
			//jquery ajax form submission
			/*
			$('form.ajax').on('submit', function() {
				//get data
				//send data
				alert("got here");
				$.ajax({
					type: "POST",
					dataType: "text",
					url: "newLoc.php",
					data: { loc: "2" },//fill this in correctly!!
					success: function(response){
						console.log(response);//debug
						//do stuff with response
					}
				});
			});
			*/
			
			/*
			//http://api.jquery.com/jQuery.ajax/
			var menuId = $( "ul.nav" ).first().attr( "id" );
			var request = $.ajax({
			  url: "script.php",
			  type: "POST",
			  data: { id : menuId },
			  dataType: "html"
			});
			 
			request.done(function( msg ) {
			  $( "#log" ).html( msg );
			});
			 
			request.fail(function( jqXHR, textStatus ) {
			  alert( "Request failed: " + textStatus );
			});
			*/
			
		}
		
	</script>
	-->
</head>
<body>
	<div id="map"></div>
	<button id="show_loc">Show Your Location</button>
	
	<!--personal attempt
	<div class="map">
		<iframe width='100%' height='500px' frameBorder='0' src='https://a.tiles.mapbox.com/v4/map13.lbo541k8/attribution,zoompan,zoomwheel,geocoder,share.html?access_token=pk.eyJ1IjoibWFwMTMiLCJhIjoieHFnSkh1RSJ9.pbbOa2J6sV8g_qLAr0E45Q'></iframe>
	</div>
	-->
	
	<!-- To enter all data for a new location to be placed on the user's map -->
	<div id="newEntry">
		<div>Location Name: <input id="loc_name" type="text"></div>
		
		<!-- To choose the range of days the user was at a location: -->
		<div id="date">
			<div id="startDate"></div>
			<div id="endDate"></div>
		</div>
		
		<div>
			<h3>Location</h3>
			<p id="lat">Latitude: not yet selected</p>
			<p id="lng">Longitude: not yet selected</p>
		</div>
		
		<!-- using $.post() -->
		<button class="ajax">Submit</button>
		<div id="newEntryErrors"></div>
	</div>
	
	<!--ONLY FOR LEARNING PURPOSES
	<form action="newLoc.php" method="POST" class="ajax">
		<input type="submit" value="Submit">
	</form>
	-->
	
	<!-- Logout functionality provided in main.js (160-166) is attached to this button: -->
	<button onclick="window.location.href = 'logout.php'">Log Out</button>
	
	
	
</body>

</html>
<!--
mapbox account:
uersname: map13
Ben Olson
benrolson@gmail.com
Iwtvage.

api key:
pk.eyJ1IjoibWFwMTMiLCJhIjoieHFnSkh1RSJ9.pbbOa2J6sV8g_qLAr0E45Q

default secret token:
sk.eyJ1IjoibWFwMTMiLCJhIjoiM1FfNW90byJ9.B6F4jvXF8HqNGOPbhw2csg

default public token:
pk.eyJ1IjoibWFwMTMiLCJhIjoieHFnSkh1RSJ9.pbbOa2J6sV8g_qLAr0E45Q

map id:
map13.lbo541k8

-->