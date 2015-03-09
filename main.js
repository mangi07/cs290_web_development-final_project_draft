/*
AUTHOR:	Benjamin R. Olson
DATE:	March 8, 2015
COURSE: CS 290 - Web Development, Oregon State University
*/



/*
Some of the following code modified from tutorial:
Leaflet Basemap Initialization - GISC Video Tutorial
by Sam Matthews
https://www.youtube.com/watch?v=7Tll2k57ork
*/

/*
Date api used to select dates:
  http://www.eyecon.ro/datepicker/#implement
*/

/*
JSON structure used: 
	
	{ "users":
		{
		"entries":
			{ "coords": {"lat":number, "lng":number},
			"loc_name": string,
			"timeframe": {"day":number, "month":number, "year":number} }
		}
	}
*/

//VARIABLES
var map;
var marker;
var userData;
var latitude, longitude;
var html_loc;
var popup;

window.onload = function (){

	//data RETURNS JSON OBJECT STRING
	$.post( "userData.php", function( data ) {
		userData = JSON.parse(data);
		if (userData.entries.coords != undefined) {
			html_loc = locationString(userData);
			initMap(latitude, longitude);
			showLocation(html_loc);
		} else {
			initMap(5, 5);
		}
	});
	
	
	//sets location variables and
	//  returns an html string of location info from userData
	function locationString (userData) {
		name = userData.entries.loc_name;
		
		start = userData.entries.timeframe.start;
		end = userData.entries.timeframe.end;
		
		latitude = parseFloat(userData.entries.coords.lat);
		longitude = parseFloat(userData.entries.coords.lng);
		
		html_string = 
			"<strong>Name: </strong>" + name + "<br>" +
			"<strong>Start Date: </strong>" + start + "<br>" +
			"<strong>End Date: </strong>" + end + "<br>" +
			"<strong>Latitude: </strong>" + latitude + "<br>" +
			"<strong>Longitude: </strong>" + longitude + "<br>"
		;
		
		return html_string;
	
	}
	
	//showLocation must be called to set location prior to this:
	function initMap (lat, lng) {
		map = L.map('map', {
			center: [lat, lng],
			zoom: 2,
		});
		
		L.tileLayer('http://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwMTMiLCJhIjoieHFnSkh1RSJ9.pbbOa2J6sV8g_qLAr0E45Q', {
			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/bysa/2.0/">CCBYSA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
			maxZoom: 18
		}).addTo(map)
		
		map.on('click', onMapClick);
	}
	
	//places a pin on the map
	function showLocation (html_string) {
		
		marker = L.marker([latitude, longitude]);
		marker.bindPopup(html_string);
		map.addLayer(marker);
	}
			
	
	//GET COORDINATES FOR A NEW LOCATION TO BE ADDED TO USER'S MAP
	popup = L.popup();
	function onMapClick(e) {
		popup
		.setLatLng(e.latlng)
		.setContent("Location of " + e.latlng.toString() + " to be saved.")
		.openOn(map);
		$('#lat').html(e.latlng.lat);
		$('#lng').html(e.latlng.lng);
	}
	

	var dateDivs = ['#startDate', '#endDate'];
	for (var x = 0; x < 2; x++){
		$(dateDivs[x]).DatePicker({
			flat: true,
			format:'m/d/Y',
			date: '03-04-2015',
			current: '03-04-2015',
			starts: 0,
			onBeforeShow: function(){
				$(dateDivs[x]).DatePickerSetDate($(dateDivs[x]).val(), true);
			}
		});
	}
	
	
	
	
	//FUNCTION TO ADD A JSON LOCATON OBJECT TO THE LOCATION ARRAY
	//  AND THEN SAVE THE JSON LOCATION OBJECT FOR THE CURRENT USER
	$('button.ajax').on('click', function() {
		
		//SET TO TRUE IF THERE ARE ANY USER INPUT ERRORS
		var errors = false;
		var errors_string = "";
		
		//CLEAR ANY POSSIBLE ERRORS FROM PREVIOUS CLICKS
		$('#newEntryErrors').html("");
		
		//GET AND FILTER DATE RANGE INPUT
		var startDate = $('#startDate').DatePickerGetDate(true);
		var endDate = $('#endDate').DatePickerGetDate(true);
		//adapted from: http://stackoverflow.com/questions/5619202/converting-string-to-date-in-js
		var parts = startDate.split('/');
		var start = new Date(parts[2],parts[0]-1,parts[1]);
		parts = endDate.split('/');
		var end = new Date(parts[2],parts[0]-1,parts[1]);
		
		//check input before sending
		if (start > end){
			errors_string += "Error: Start date is later than end date!<br><br>";
			errors = true;
		}
		
		//GET AND FILTER LOCATION NAME INPUT
		latitude = $('#lat').html();
		longitude = $('#lng').html();
		if (latitude == 'Latitude: not yet selected.' && 
			longitude == 'Longitude: not yet selected.') {
			errors_string += "Error: No latitude or longitude specified.<br>Click on the map to get coordinates.<br><br>";
			errors = true;
		}

		//GET AND FILTER LOCATION INPUT
		loc_name = $('#loc_name').val();
		loc_name.trim();
		if (loc_name == ''){
			errors_string += "Error: No location name provided.<br><br>";
			errors = true;
		}
		
		//STOP HERE IF THERE ARE ANY USER INPUT ERRORS,
		//  AND DISPLAY THESE ERRORS TO THE USER
		if (errors) {
			$('#newEntryErrors').html(errors_string);
			return;
		} else {
		//CREATE THE JSON OBJECT STRING FROM THE PRECEDING VARIABLES
			loc = { "entries":
					{ "coords": {"lat":latitude, "lng":longitude},
					"loc_name": loc_name,
					"timeframe": {"start":startDate, "end":endDate} 
					}
				};
			JSON.stringify(loc);
		}
		
		
		//POST THE JSON TO THE USER'S ACCOUNT ON THE SERVER
		//get response from this post as json to load locations on map (refresh map)
		$.post( "update.php", {loc:loc} )
			.done(function( data ) {
				//REMOVE THE OLD MARKER FROM THE MAP
				if (marker != null) {
					map.removeLayer(marker);
				}
				
				//REMOVE THE POPUP FROM THE MAP
				if (popup != null) {
					popup._close();
				}
				
				//ADD THE NEW LOCATION TO THE MAP
				userData = loc;
				var html_loc = locationString(userData);
				showLocation(html_loc);
			})
			.fail(function( data ) {//HAVE DATA RETURN ERROR MESSAGES
				$('#newEntryErrors').html(data);
			});
		
	
	});
	
	
	//LOGOUT
	$('#logout').on('click', function() {
		$.post( "logout.php" );
	});
	
	
	
	
	
}


