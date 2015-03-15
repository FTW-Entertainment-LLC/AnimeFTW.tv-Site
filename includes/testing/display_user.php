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
	print_r($_POST);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Product Discount Calculator</title>
    <link rel="stylesheet" type="text/css" href="main.css" />
</head>
<body>
    <div id="content">
        <h1>Welcome - LIS5364 Web Dev and Admin!</h1>

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
    </div>
</body>
</html>