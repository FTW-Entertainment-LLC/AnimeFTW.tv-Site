<?php
require_once('includes/recaptchalib.php');
$publickey = "6Le0a8cSAAAAAJyAV1Koo1_VykJ63Ji8FXTLCoY7"; // you got this from the signup page
$privatekey = "6Le0a8cSAAAAADkPIzpVozNckauf4dXs3HxHJvsF";
 
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
 
if ($resp->is_valid) {
    ?>success<?
}
else{
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
       "(reCAPTCHA said: " . $resp->error . ")");
}
?>