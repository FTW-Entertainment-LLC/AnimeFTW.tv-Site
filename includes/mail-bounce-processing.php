#!/usr/local/bin/php
<?php

error_reporting(E_ERROR | E_PARSE);

include("/home/mainaftw/public_html/includes/config_site.php");

// Database info
$hostname = $newsdbhost;
$dbname   = $newsdbname;
$username = $newsdbuser;
$password = $newsdbpass;

// Email address to send errors to
$email		= "support@animeftw.tv";

// fetch data from stdin
$data = file_get_contents("php://stdin");

// extract the body
// NOTE: a properly formatted email's first empty line defines the separation between the headers and the message body
list($data, $body) = explode("\n\n", $data, 2);

// explode on new line
$data = explode("\n", $data);

// define a variable map of known headers
$patterns = array(
  'Return-Path',
  'X-Original-To',
  'Delivered-To',
  'Received',
  'To',
  'Message-Id',
  'Date',
  'From',
  'Subject',
  'Content-Transfer-Encoding',
);

// define a variable to hold parsed headers
$headers = array();

// loop through data
foreach ($data as $data_line) {

  // for each line, assume a match does not exist yet
  $pattern_match_exists = false;

  // check for lines that start with white space
  // NOTE: if a line starts with a white space, it signifies a continuation of the previous header
  if ((substr($data_line,0,1)==' ' || substr($data_line,0,1)=="\t") && $last_match) {

    // append to last header
    $headers[$last_match][] = $data_line;
    continue;

  }

  // loop through patterns
  foreach ($patterns as $key => $pattern) {

    // create preg regex
    $preg_pattern = '/^' . $pattern .': (.*)$/';

    // execute preg
    preg_match($preg_pattern, $data_line, $matches);

    // check if preg matches exist
    if (count($matches)) { 

      $headers[$pattern][] = $matches[1];
      $pattern_match_exists = true;
      $last_match = $pattern;

    }

  }

  // check if a pattern did not match for this line
  if (!$pattern_match_exists) {
    $headers['UNMATCHED'][] = $data_line;
  }

}

try {
  $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $dbh->prepare("INSERT INTO `mail_failures`(`created`, `body`, `mailto`, `return_path`, `x_original_to`, `delivered_to`, `message_id`, `subject`, `maildate`, `mailfrom`, `content_transfer_encoding`) VALUES (:created, :body, :mailto, :return_path, :x_original_to, :delivered_to, :message_id, :subject, :maildate, :mailfrom, :content_transfer_encoding)");

  $stmt->bindParam(':created', time(), PDO::PARAM_INT);  
  $stmt->bindParam(':body', $body, PDO::PARAM_STR);  
  $stmt->bindParam(':mailto', $headers['To'][0], PDO::PARAM_STR, 255);
  $stmt->bindParam(':return_path', $headers['Return-Path'][0], PDO::PARAM_STR, 255);  
  
  $re1='(RCPT)';	# Word 1
  $re2='( )';	# White Space 1
  $re3='(TO)';	# Word 2
  $re4='(:)';	# Any Single Character 1
  $re5='(<)';	# Any Single Character 2
  $re6='([\\w-+]+(?:\\.[\\w-+]+)*@(?:[\\w-]+\\.)+[a-zA-Z]{2,7})';	# Email Address 1
  $re7='(>)';	# Any Single Character 3

	if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5.$re6.$re7."/is", $body, $matches))
	{
		$orig_to_email1 = $matches[6][0];
	}
	
	// This was a failure message.. we will need to update the user so that they dont get future emails
	if($headers['Subject'][0] == 'Mail delivery failed: returning message to sender' || $headers['Subject'][0] == 'Delivery Status Notification (Failure)')
	{
		mysqli_query("UPDATE `users` SET `notifications` = 0 WHERE `Email` = '$orig_to_email1'");
	}
  
  $stmt->bindParam(':x_original_to',  $orig_to_email1, PDO::PARAM_STR, 255);
  $stmt->bindParam(':delivered_to', $headers['Delivered-To'][0], PDO::PARAM_STR, 255);
  $stmt->bindParam(':message_id', $headers['Message-Id'][0], PDO::PARAM_STR, 255);
  $stmt->bindParam(':subject', $headers['Subject'][0], PDO::PARAM_STR, 255);
  $stmt->bindParam(':maildate', strtotime($headers['Date'][0]), PDO::PARAM_INT);
  $stmt->bindParam(':mailfrom', $headers['From'][0], PDO::PARAM_STR, 255);
  $stmt->bindParam(':content_transfer_encoding', $headers['Content-Transfer-Encoding'][0], PDO::PARAM_STR, 255);
  $stmt->execute();
  $dbh = null;

  }
catch(PDOException $e)
  {
    // If error send email to me
    mail($email, "DB ERROR: " . $headers['Subject'][0], $e->getMessage(), $header);
  }
