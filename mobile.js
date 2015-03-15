function submitRegister() {
	var username = document.getElementById("u").value;
	var password = document.getElementById("p").value;
	var repass   = document.getElementById("p2").value;
	var email = document.getElementById("e").value;
	var remail = document.getElementById("e2").value;
	var name = document.getElementById("n").value;
	var g = document.getElementById("g");
	var gender = g.options[g.selectedIndex].value;
	var birthday = document.getElementById("b").value;
	var t = document.getElementById("t");
	var timezone = t.options[t.selectedIndex].value;
	var a = document.getElementById("a");
	var adminnotify = a.options[a.selectedIndex].value;
	var s = document.getElementById("s");
	var pmnotify = s.options[s.selectedIndex].value;
	var vh = document.getElementById("vh");
	var ishuman = vh.options[vh.selectedIndex].value;
	var verify = document.getElementById("q").value;

	if(username == "") { sendError("User name must not be empty!"); }
	else if(username.length < 6) { sendError("User name must be greater than 5 characters!"); }
	else if(password == "") { sendError("Password must not be empty!"); }
	else if(password.length < 6) { sendError("Password must greater than 5 characters!"); }
	else if(repass !== password) { sendError("Passwords do not match!"); }
	else if(email == "") { sendError("Email must not be empty!"); }
	else if(!checkemail()) { sendError("Email is invalid!"); }
	else if(email != remail) { sendError("Emails do not match!"); }
	else if(birthday != "" && !checkbirthday()) { sendError("Birthday format invalid!"); }
	else if(ishuman != 2) { sendError("Please verify that you are human!"); }
	else {
		var xmlhttp;
		if (window.XMLHttpRequest) xmlhttp=new XMLHttpRequest();
		else xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		xmlhttp.open("GET","/m/register?"+
				    "u="+username+
				   "&p="+password+
				   "&e="+email+
				   "&f="+name+
				   "&g="+gender+
				   "&b="+birthday+
				   "&t="+timezone+
				   "&a="+adminnotify+
				   "&n="+pmnotify+
				   "&h="+ishuman+
				   "&v="+verify,false);
		xmlhttp.send();

		if(xmlhttp.responseText == "ok") {
			location.href="/m/register?success=true";
		} else {
			sendError(xmlhttp.responseText);
		}
	}
}

function sendError(message) {
	var mesbox = document.getElementById("message");
	var mesfield = document.getElementById("mes");
	mesbox.style.display="block";
	mes.innerHTML=message;
	window.location.hash="top";
}

function checkemail() {
	var email = document.getElementById("e").value;
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function checkbirthday() {
	var birthday = document.getElementById("b").value;
	var re = /^[0-1][0-9]\/[0-3][0-9]\/[1-2][0-9][0-9][0-9]$/;
	return re.test(birthday);
}