<?php
//new function

$to = "brad@ftwentertainment.com";
$nameto = "Brad";
$from = "notifications@animeftw.tv";
$namefrom = "AnimeFTW.tv Notifications";
$subject = "Hello World Again!";
$message = "World, Hello!";
authSendEmail($from, $namefrom, $to, $nameto, $subject, $message);

//Authenticate Send - 21st March 2005
//This will send an email using auth smtp and output a log array
//logArray - connection,

function authSendEmail($from, $namefrom, $to, $nameto, $subject, $message)
{
//SMTP + SERVER DETAILS
/* * * * CONFIGURATION START * * * */
$smtpServer = "localhost";
$port = "25";
$timeout = "30";
$username = "notifications+animeftw.tv";
$password = "Ftw3nt3rtainm3nt";
$localhost = "localhost";
$newLine = "\r\n";
/* * * * CONFIGURATION END * * * * */

///Connect to the host on the specified port
$smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
$smtpResponse = fgets($smtpConnect, 515);
echo 'Connection response was: ' . $smtpResponse."\n<br />";

if(empty($smtpConnect))
{
$output = "Failed to connect: $smtpResponse"."\n<br />";
return $output;
}
else
{
$logArray['connection'] = "Connected: $smtpResponse"."\n<br />";

}
echo 'Response from connection attempt was: ' . $smtpResponse;

//Say Hello to SMTP
fputs($smtpConnect, "EHLO $localhost" . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['heloresponse'] = "$smtpResponse";
echo 'Response from HELO was: ' . $smtpResponse."\n<br />";

//Request Auth Login
fputs($smtpConnect,"AUTH LOGIN" . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['authrequest'] = "$smtpResponse";
echo 'Response from AUTH LOGIN was: ' . $smtpResponse."\n<br />";


//Send username
fputs($smtpConnect, base64_encode($username) . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['authusername'] = "$smtpResponse";
echo 'Response from USERNAME was: ' . $smtpResponse."\n<br />";

//Send password
fputs($smtpConnect, base64_encode($password) . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['authpassword'] = "$smtpResponse";
echo 'Response from PASSWORD was: ' . $smtpResponse."\n<br />";

//Email From
fputs($smtpConnect, "MAIL FROM: $from" . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['mailfromresponse'] = "$smtpResponse";
echo 'Response from EMAIL FROM was: ' . $smtpResponse."\n<br />";

//Email To
fputs($smtpConnect, "RCPT TO: $to" . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['mailtoresponse'] = "$smtpResponse";
echo 'Response from EMAIL TO was: ' . $smtpResponse."\n<br />";

//The Email
fputs($smtpConnect, "DATA" . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['data1response'] = "$smtpResponse";
echo 'Response from DATA was: ' . $smtpResponse."\n<br />";

//Construct Headers
$headers = "MIME-Version: 1.0" . $newLine;
$headers .= "Content-type: text/html; charset=iso-8859-1" . $newLine;
$headers .= "To: $nameto <$to>" . $newLine;
$headers .= "From: $namefrom <$from>" . $newLine;

fputs($smtpConnect, "To: $to\nFrom: $from\nSubject: $subject\n$headers\n\n$message\n.\n");
$smtpResponse = fgets($smtpConnect, 515);
$logArray['data2response'] = "$smtpResponse";
echo 'Response from EMAIL RESPONSE was: ' . $smtpResponse."\n<br />";

// Say Bye to SMTP
fputs($smtpConnect,"QUIT" . $newLine);
$smtpResponse = fgets($smtpConnect, 515);
$logArray['quitresponse'] = "$smtpResponse";
echo 'Response from QUIT was: ' . $smtpResponse."\n<br />";
} 
?>