<?php
include("../includes/classes/config.class.php");


if(isset($_POST['method']))
{
	if($_POST['method'] == 'login-form')
	{
		// login function ho!
		if(isset($_POST['submitcheck']) && $_POST['submitcheck'] == '10010')
		{
			include("includes/logins.class.php");
			$L = new Logins();
			$L->processLogins();
		}
		else
		{
			print_r($_POST);
			//echo 'fail;';
		}
	}
	else
	{
		// an everything else catch..
		include("includes/processData.class.php");
		$pd = new processData();
	}
}

if(isset($_GET['node']))
{
	$RequestedNode = $_GET['node'];
	$RequestedClass = 'includes/' . $RequestedNode . '.class.php';
	if(file_exists($RequestedClass))
	{
		include($RequestedClass); //we include the file, since it exists
		$Class = ucwords($RequestedNode); //This will fix the first letter of the class, so it fits with our standards.
		$C = new $Class; // dynamiclly drive the class.
		//$C->Output(); // This relies on us having the standard of Output() on all classes, so no slacking off!
	}
	else // the file doesnt exist so there is no class...
	{
		echo 'There was no class for that..';
	}
}
else //the node was not set.. so we should really give them hell
{
}

?>