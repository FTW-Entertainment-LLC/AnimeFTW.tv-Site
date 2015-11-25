function validateAllSteps(){
	var e=true;
	if(validateStep1()==false){
		e=false;
		$("#wizard").smartWizard("setError",{stepnum:1,iserror:true})
	}
	else {
		$("#wizard").smartWizard("setError",{stepnum:1,iserror:false})
	}
	if(validateStep2()==false){
		e=false;
		$("#wizard").smartWizard("setError",{stepnum:2,iserror:true})
	}
	else{
		$("#wizard").smartWizard("setError",{stepnum:2,iserror:false})
	}
	if(validateStep4()==false){
		e=false;
		$("#wizard").smartWizard("setError",{stepnum:4,iserror:true})
	}
	else{
		$("#wizard").smartWizard("setError",{stepnum:4,iserror:false})
	}
	if(!e){
		$("#wizard").smartWizard("showMessage","Please correct the errors the steps and continue")
	}
	return e
}
function validateSteps(e){
	var t=true;
	if(e==1){
		if(validateStep1()==false){
			t=false;
			$("#wizard").smartWizard("showMessage","Please correct the errors in step"+e+" and click next.");
			$("#wizard").smartWizard("setError",{stepnum:e,iserror:true})
		}
		else{
			$("#wizard").smartWizard("setError",{stepnum:e,iserror:false})
		}
	}
	if(e==2){
		if(validateStep2()==false){
			t=false;
			$("#wizard").smartWizard("showMessage","Please correct the errors in step"+e+" and click next.");
			$("#wizard").smartWizard("setError",{stepnum:e,iserror:true})
		}
		else{
			$("#wizard").smartWizard("setError",{stepnum:e,iserror:false})
		}
	}
	if(e==4){
		if(validateStep4()==false){
			t=false;
			$("#wizard").smartWizard("showMessage","Please correct the errors in step"+e+" and click next.");
			$("#wizard").smartWizard("setError",{stepnum:e,iserror:true})
		}
		else{
			$("#wizard").smartWizard("setError",{stepnum:e,iserror:false})
		}
	}
	return t
}
function validateStep1(){
	var e=true;
	var t=$("#username").val();
	if(!t&&t.length<=0){
		e=false;
		$("#msg_username").html("Please fill username").show()
	}
	else{
		$("#msg_username").html("").hide()
	}
	var n=$("#password").val();
	if(!n||n.length<=4){
		if(n.length<5){
			e=false;
			$("#msg_password").html("Please make your password longer than 4 Characters").show()
		}
		else{
			e=false;
			$("#msg_password").html("Please fill password").show()
		}
	}
	else{
		$("#msg_password").html("").hide()
	}
	var r=$("#cpassword").val();
	if(!r||r.length<=4){
		if(r.length<5){
			e=false;
			$("#msg_cpassword").html("Please make your password longer than 4 Characters").show()
		}
		else{
			e=false;
			$("#msg_cpassword").html("Please fill confirm password").show()
		}
	}
	else{
		$("#msg_cpassword").html("").hide()
	}
	if(n&&n.length>0&&r&&r.length>0){
		if(n!=r){
			e=false;
			$("#msg_cpassword").html("Password mismatch").show()
		}
		else{
			$("#msg_cpassword").html("").hide()
		}
	}
	var i=$("#email").val();
	if(i&&i.length>0){
		if(!isValidEmailAddress(i)){
			e=false;
			$("#msg_email").html("Email is invalid").show()
		}
		else{
			$("#msg_email").html("").hide()
		}
	}
	else{
		e=false;
		$("#msg_email").html("Please enter email").show()
	}
	var s=$("#cemail").val();
	if(s&&s.length>0){
		if(!isValidEmailAddress(s)){
			e=false;
			$("#msg_cemail").html("Email Confirmation is invalid").show()
		}
		else{
			$("#msg_cemail").html("").hide()
		}
	}
	else{
		e=false;
		$("#msg_cemail").html("Please enter confirmation email").show()
	}
	return e
}
function validateStep2(){
	var e=true;
	return e
}
function validateStep4(){
	var e=true;
	var n=$("#google").val();
	if(n&&n.length>0){
		if(n!=9){
			e=false;
			$("#msg_google").html("Bot Check Failure").show()
		}
		else{
			$("#msg_google").html("").hide()
		}
	}
	else{
		e=false;
		$("#msg_google").html("Bot Check Failure").show()
	}
	return e
}
function isValidEmailAddress(e){var t=/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;return t.test(e)}
function timeoutUsernameCheck(){if(window.mytimeout)window.clearTimeout(window.mytimeout);window.mytimeout=window.setTimeout("toggle_username()",delay);return true}function toggle_username(){if(window.XMLHttpRequest){http=new XMLHttpRequest}else if(window.ActiveXObject){http=new ActiveXObject("Microsoft.XMLHTTP")}handle=document.getElementById("username");var e="includes/check-username.php?";if(handle.value.length>minlength){fetch_unix_timestamp=function(){return parseInt((new Date).getTime().toString().substring(0,10))};var t=fetch_unix_timestamp();var n=e+"do=check_username_exists&username="+encodeURIComponent(handle.value)+"&timestamp="+t;http.open("GET",n,true);http.send(null);http.onreadystatechange=statechange_username}else{document.getElementById(divid).innerHTML=""}}function statechange_username(){if(http.readyState==4){var e=http.responseText;document.getElementById(divid).innerHTML=e}}$(document).ready(function(){function e(e){var t=e.attr("rel");return validateSteps(t)}function t(){if(validateAllSteps()){$("form").submit()}}$("#wizard").smartWizard({transitionEffect:"slideleft",onLeaveStep:e,onFinish:t,enableFinishButton:true})});var minlength="3";var delay="1000";var divid="msg_username"