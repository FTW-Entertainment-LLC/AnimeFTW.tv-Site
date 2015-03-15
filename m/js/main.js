$(function() {
	$("#login").attr("disabled",true);
	var temp = Math.round(Math.random() * 1000000);
	var hashObj = new jsSHA("Something" + temp, "ASCII");
	var password = hashObj.getHash("SHA-512", "HEX");
	//Handshake between client and server
	//The folowing file names should point to your PHP file
	$.jCryption.authenticate(password,
	"../jcryption.php?generateKeypair=true",
	"../jcryption.php?handshake=true",
	function(AESKey) {
		$("#login").attr("disabled",false);
	}, function() {
		// Authentication failed
	});
});
//Login Button Click --------------------------------------------
$("#login").click(function() {
	var User = $("#user").val();
	var encryptedString = $.jCryption.encrypt($("#password").val(), password);
	$.ajax({
		url: "jcryption.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: encryptedString,
			User: User
		},
		success: function(response) {
			var decryptedString = $.jCryption.decrypt(response.data, password);
			//This is where you check the Decrypted Data
			//Decrypted Data is the response from the PHP file
		}
	});
});