/*
Some of the following code modified from tutorial:
Leaflet Basemap Initialization - GISC Video Tutorial
by Sam Matthews
https://www.youtube.com/watch?v=7Tll2k57ork
*/


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
		attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/bysa/2.0/">CCBYSA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
		maxZoom: 18
	}).addTo(map)
	
	//put this in a function that accepts json array of objects, each representing a location
	//create layergroup for markers
	var marker = L.marker([51.5, -0.09]).addTo(map);
	//change the following to say location's name, timeframe, and coordinates 
	marker.bindPopup("<b>Hello world!</b><br>I am a popup.");
	
	var popup = L.popup();
	function onMapClick(e) {
		popup
		.setLatLng(e.latlng)
		.setContent("Location of " + e.latlng.toString() + " to be saved.")
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
	
	var dateDivs = ['#startDate', '#endDate'];
	for (var x = 0; x < 2; x++){
		$(dateDivs[x]).DatePicker({
			flat: true,
			format:'m/d/Y',
			date: '2015-03-04',
			current: '2015-03-04',
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
	
	//set button for sending ALL new entry data to server
	$('button.ajax').on('click', function() {
		var startDate = $('#startDate').DatePickerGetDate(true);
		var endDate = $('#endDate').DatePickerGetDate(true);
		
		//adapted from: http://stackoverflow.com/questions/5619202/converting-string-to-date-in-js
		var parts = startDate.split('/');
		startDate = new Date(parts[2],parts[0]-1,parts[1]);
		parts = endDate.split('/');
		endDate = new Date(parts[2],parts[0]-1,parts[1]);
		//alert(startDate);
		//alert(endDate);
		/*
		//use the following to convert the dates:
		//adapted from: http://stackoverflow.com/questions/5619202/converting-string-to-date-in-js
		var parts ='04/03/2014'.split('/');
		//please put attention to the month (parts[0]), Javascript counts months from 0:
		// January - 0, February - 1, etc
		var mydate = new Date(parts[2],parts[0]-1,parts[1]);
		alert(mydate);		
		*/
		
		//check input before sending
		if (startDate > endDate){
			alert("Error: Start date is later than end date!");
			return;
		}
		//more checks needed here
		
		//get response from this post as json to load locations on map (refresh map)
		$.post( "newLoc.php", { startDate:startDate, endDate:endDate })
			.done(function( data ) {
				alert( "Data Loaded: " + data );
			});
			//check docs for failure code
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