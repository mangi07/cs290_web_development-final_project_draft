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
		[
		"username":string,
		"entries":
			[
				{ "coords": {"lat":number, "lng":number},
				"loc_name": string,
				"timeframe": {"day":number, "month":number, "year":number} }
			]
		]
	}
*/

//VARIABLES
var marker;
var userData;
//use JSON.stringify() and JSON.parse()

window.onload = function (){

	//data RETURNS JSON OBJECT STRING
	$.post( "userData.php", function( data ) {
		//data.trim();
		userData = JSON.parse(data);
	});
	
	//GUI: 1) list users to display
	
	//INITIALIZE THE MAP, WITHOUT ANY USER DATA YET
	var map = L.map('map', {
		center: [30.0, 0.0],
		zoom: 2,
	});
	//var map = L.map('map').setView([51.505, -0.09], 13);
	L.tileLayer('http://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwMTMiLCJhIjoieHFnSkh1RSJ9.pbbOa2J6sV8g_qLAr0E45Q', {
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/bysa/2.0/">CCBYSA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
		maxZoom: 18
	}).addTo(map)
	
	//SHOW USER DATA: why is userData undefined???
	$('#show_loc').on('click', function() {
		latitude = parseFloat(userData.entries.coords.lat);
		longitude = parseFloat(userData.entries.coords.lng);
		marker = L.marker([latitude, longitude]);
		marker.bindPopup("<b>Hello world!</b><br>I am a popup.");
		map.addLayer(marker);
	});
	
	//REMOVE A CERTAIN USER'S LOCATIONS FROM THE MAP
	//map.removeLayer(circle);//where circle is a layer
	
			
	
	//GET COORDINATES FOR A NEW LOCATION TO BE ADDED TO USER'S MAP
	var popup = L.popup();
	function onMapClick(e) {
		popup
		.setLatLng(e.latlng)
		.setContent("Location of " + e.latlng.toString() + " to be saved.")
		.openOn(map);
		$('#lat').html(e.latlng.lat);
		$('#lng').html(e.latlng.lng);
	}
	map.on('click', onMapClick);
	
	//DATEPICKER CODE
	/*
	$('#date').DatePicker({
		flat: true,
		date: '2008-07-31',
		current: '2008-07-31',
		calendars: 1,
		starts: 0
	});
	*/
	
	var dateDivs = ['#startDate', '#endDate'];
	for (var x = 0; x < 2; x++){
		$(dateDivs[x]).DatePicker({
			flat: true,
			format:'m/d/Y',
			date: '03-04-2015',
			current: '03-04-2015',
			starts: 0,
			//position: 'r',
			onBeforeShow: function(){
				$(dateDivs[x]).DatePickerSetDate($(dateDivs[x]).val(), true);
			},
			onChange: function(formatted, dates){
				//$('#inputDate').val(formated);
				//$(dateDivs[x]).next().text(formatted);
				//alert(formatted);
				//$('#endDate').text(formatted);
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
		startDate = new Date(parts[2],parts[0]-1,parts[1]);
		parts = endDate.split('/');
		endDate = new Date(parts[2],parts[0]-1,parts[1]);
		//alert(startDate);
		//alert(endDate);
		
		//check input before sending
		if (startDate > endDate){
			//alert("Error: Start date is later than end date!");
			errors_string += "Error: Start date is later than end date!<br><br>";
			errors = true;
		}
		
		//GET AND FILTER LOCATION NAME INPUT
		//var latitude = '';
		//var longitude = '';
		latitude = $('#lat').html();
		longitude = $('#lng').html();
		if (latitude == 'Latitude: not yet selected' || 
			longitude == 'Longitude: not yet selected') {
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
					//map.removeLayer(marker);
				//ADD THE NEW LOCATION TO THE MAP
				latitude = parseFloat(latitude);
				longitude = parseFloat(longitude);
				marker = L.marker([latitude, longitude]);
				//change the following to say location's name, timeframe, and coordinates 
				marker.bindPopup("<b>Hello world!</b><br>I am a popup.");
				map.addLayer(marker);
				//debug
				$('#newEntryErrors').html(data);
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


