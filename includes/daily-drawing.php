<?php
	include 'config_site.php';
	include 'newsOpenDb.php';
		
//vars
	$now = time();
	$start = $now-604799;
	
// random user drawing, yeehaw!
	function NewSendMail($subject,$to,$body){
		ini_set('sendmail_from', 'no-reply@animeftw.tv');
		$headers = 'From: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'Reply-To: AnimeFTW.tv <no-reply@animeftw.tv>' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $body, $headers);
	}
	
	$query = "SELECT Username FROM users WHERE lastActivity >= ".$start." AND lastActivity <= ".$now." AND registrationDate < $start ORDER BY RAND() LIMIT 0,2;";
	$result = mysqli_query($query);
	$u = 'Users picked for AFTW\'s Christmas (weekly) Drawing:'."\n";
	while(list($Username) = mysqli_fetch_array($result)){
		$u.= $Username."\n";
	}
	$u.= "\nThis Update is for ".date("M j Y, h:i A",$start)." to ".date("M j Y, h:i A",$now);
	
	echo NewSendMail('AnimeFTW.tv Weekly Drawing','support@animeftw.tv',$u);

?>