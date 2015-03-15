<?php
error_reporting(E_ALL);
    // get the data from the user information form
	
	# added @ symbols in case something isnt set you dont get an error
    $first_name = @$_POST['first_name'];
    $last_name = @$_POST['last_name'];
    $age_data = @$_POST['age_data'];
	$phone_data = @$_POST['phone_data'];
    $email_data = @$_POST['email_data'];
    $comments_data = @$_POST['comments_data'];
    

	if($_POST){
		//set the vars
		$error_message = NULL;
		$failcheck = TRUE;
		// validate first name
		if ( empty($first_name) ) { # if needs to be lowercase
			$error_message .= 'First name is a required field.<br />';
			$failcheck == FALSE;
		}
		else if ( is_numeric($first_name) ) {
			$error_message .= 'First name can only have letters<br />'; # forgot the ;
			$failcheck == FALSE;
		}
		else {
		}
		// validate last name
		if ( empty($last_name) ) {
			$error_message .= 'last name is a required field.<br />';
			$failcheck == FALSE;
		}
		else if ( is_numeric($last_name) ) {
			$error_message .= 'last name can only have letters<br />'; # forgot the ;
			$failcheck == FALSE;
		}	
		else {
		}
		// validate age
		if ( !is_numeric($age_data) ) {
			$error_message .= 'The age field can only have numbers<br />'; # forgot the ;
			$failcheck == FALSE;
		}
		else {
		}
		// validate phone
		if ( empty($phone_data) ) {
			$error_message .= 'Phone is a required field.<br />';
			$failcheck == FALSE;
		}
		else if ( !is_numeric($phone_data) ) {
			$error_message .= 'The phone field can only have numbers<br />'; # forgot the ;
			$failcheck == FALSE;
		}
		else {
		}
		// validate email
		if ( empty($email_data) ) {
			$error_message .= 'email is a required field.<br />';
			$failcheck == FALSE;
		}
		else {
		}
		
		function checkEmail($email) {
			if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",
				$email)){
				list($username,$domain)=split('@',$email);
				if(!checkdnsrr($domain,'MX')) {
					return false;
				}
				return true;
			}
			return false;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Welcome to your dooooooom!!! Mahahaha!</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="a2.css" type="text/css" />
</head>
<body>
    <div id="content">
        <h1>Welcome - LIS5364 Web Dev and Admin!</h1>
          
        <?php
			if($error_message == NULL && (isset($failcheck) && $failcheck == TRUE)){ 
		?>
		  <p class="error">Form Validated Successfully.</p>

        <label>First Name:</label>
        <span><?php echo $first_name; ?></span><br />

        <label>Last Name:</label>
        <span><?php echo $last_name; ?></span><br />

        <label>Age:</label>
        <span><?php echo $age_data; ?></span><br />

        <label>Phone:</label>
        <span><?php echo $phone_data; ?></span><br />

        <label>Email:</label>
        <span><?php echo $email_data; ?></span><br />
		
		<label>Comments:</label>
        <span><?php echo $comments_data; ?></span><br />

        <p>&nbsp;</p>
		<?php 
		}
		else {
		?>
        
		
		<?php if (isset($error_message)) {  # isset is more global.. !empty is a tad redundant.. ?>
		  <p class="error"><?php echo $error_message; ?></p>
		  <?php } ?>
		<form action="welcomea2.php" method="post">

            <div id="endUserInfo">
                <label>*First Name:</label>
                <input type="text" name="first_name"
				       value="<?php echo @$first_name; ?>" /> <br />

                <label>*Last Name:</label>
                <input type="text" name="last_name"
				       value="<?php echo @$last_name; ?>" /><br />

                <label>*Age:</label>
                <input type="text" name="age_data"
				       value="<?php echo @$age_data; ?>" /><br />
				
				<label>*Phone:</label>
                <input type="text" name="phone_data"
				       value="<?php echo @$phone_data; ?>" /><br />
				
                <label>*E-mail:</label>
                <input type="text" name="email_data"
				       value="<?php echo @$email_data; ?>" /><br />
				
				<label>*Comments:</label>
                <input type="text" name="comments_data"value="<?php echo @$comments_data; ?>" /><br />
            </div>

            <div id="subbutton">
                <label>&nbsp;</label>
                <input type="submit" value="Submit" /><br />
            </div>

        
		</form>
        <?php
		}
		?>
		
    </div>

</body>
</html>