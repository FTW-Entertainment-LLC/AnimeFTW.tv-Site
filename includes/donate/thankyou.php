<?php

require "config.php";
require "connect.php";

if(isset($_POST['submitform']) && isset($_POST['txn_id']))
{
	$_POST['nameField'] = esc($_POST['nameField']);
	$_POST['websiteField'] =  esc($_POST['websiteField']);
	$_POST['messageField'] = esc($_POST['messageField']);
	
	$error = array();
	
	if(mb_strlen($_POST['nameField'],"utf-8")<2)
	{
		$error[] = 'Please fill in a valid name.';
	}
	
	if(mb_strlen($_POST['messageField'],"utf-8")<2)
	{
		$error[] = 'Please fill in a longer message.';
	}
	
	if(!validateURL($_POST['websiteField']))
	{
		$error[] = 'The URL you entered is invalid.';
	}

	$errorString = '';
	if(count($error))
	{
		$errorString = join('<br />',$error);
	}
	else
	{
		mysql_query("	INSERT INTO dc_comments (transaction_id, name, url, message)
						VALUES (
							'".esc($_POST['txn_id'])."',
							'".$_POST['nameField']."',
							'".$_POST['websiteField']."',
							'".$_POST['messageField']."'
						)");
		
		if(mysql_affected_rows($link)==1)
		{
			$messageString = '<a href="donate.php">You were added to our donor list! &raquo;</a>';
		}
	}
}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Thank you!</title>

<link rel="stylesheet" type="text/css" href="styles.css" />

</head>

<body class="thankyouPage">

<div id="main">
    <h1>Thank you!</h1>
    <h2>Add Yourself to our Donor Section</h2>

	<div class="lightSection">
    	<form action="" method="post">
        	<div class="field">
                <label for="nameField">Name</label>
                <input type="text" id="nameField" name="nameField" />
			</div>
            
            <div class="field">
                <label for="websiteField">Web Site</label>
                <input type="text" id="websiteField" name="websiteField" />
			</div>
            
			<div class="field">
                <label for="messageField">Message</label>
                <textarea name="messageField" id="messageField"></textarea>
            </div>
            
            <div class="button">
            	<input type="submit" value="Submit" />
                <input type="hidden" name="submitform" value="1" />
                <input type="hidden" name="txn_id" value="<?php echo $_POST['txn_id']?>" />
            </div>
        </form>
        
        <?php
		if($errorString)
		{
			echo '<p class="error">'.$errorString.'</p>';
		}
		else if($messageString)
		{
			echo '<p class="success">'.$messageString.'</p>';
		}
		?>
        
    </div>


</body>
</html>


<?php

function esc($str)
{
	global $link;
	
	if(ini_get('magic_quotes_gpc'))
			$str = stripslashes($str);
	
	return mysql_real_escape_string(htmlspecialchars(strip_tags($str)),$link);
}

function validateURL($str)
{
	return preg_match('/(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?/i',$str);
}
?>