window.onload = function (){

	$('#login').on('click', function(){login(true);});
	$('#create_user').on('click', function(){login(false);});
			
}


json = {
		"entries":
			[
				{ "coords": {"lat":null, "lng":null},
				"loc_name": null,
				"timeframe": {"start":null, "end":null} }
			]
};

	
function login(login_attempt){
	
	username = $("#userfield").val();
	password = $("#passfield").val();
	
	//alert(username);
	
	$.post( "accounts.php", { login_attempt:login_attempt, username:username, password:password, json:json })
		.done(function( data ) {
			if ( data.trim() == "success" ){
				//attempt to direct to main.php
				window.location.replace("main.php");
			} else {
				$('#errors').html(data);
				//clear login fields
				$("#userfield").val("");
				$("#passfield").val("");
			}
			
		})
		.fail(function() {
			$('#errors').html("Failed to communicate with the server.");
		});
			
}