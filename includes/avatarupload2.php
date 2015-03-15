<?php
include('global_functions.php');
session_start();
if(isset($_COOKIE['cookie_id'])){
	$globalnonid = $_COOKIE['cookie_id'];
}
else if(isset($_SESSION['user_id'])){
	$globalnonid = $_SESSION['user_id'];
}	
$profileArray = checkLoginStatus($globalnonid,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT']);


//Build your gvars (gvars.. hehe..)
//Grab the File Name
//$tmpName = $_FILES['myfile']['name'];
$tmpName = basename( $_FILES['myfile']['name']);
//$target = "images/avatars/";
$target = "../images/avatars/";   

list($width, $height, $type, $attr) = getimagesize($_FILES['myfile']['tmp_name']);
if($profileArray[2] != 3){
	$sizeVar = 307200;
	$uwidth = 250;
	$uheight = 400;
}
else {
	$sizeVar = 102401;
	$uwidth = 100;
	$uheight = 100;
}
$truesize = $_FILES['myfile']['size'];
$TName = $_FILES['myfile']['tmp_name'];
// Users that get the "bigger" images can go here
if($width>$uwidth || $height>$uheight){
	$result = 2;
}
else{
	if (($_FILES['myfile']['type'] == "image/gif") || ($_FILES['myfile']['type'] == "image/jpeg") || ($_FILES['myfile']['type'] == "image/jpg") || ($_FILES['myfile']['type'] == "image/png") || ($_FILES['myfile']['type'] == "image/bmp")){
		if($truesize < $sizeVar){
			if ($_FILES['myfile']['error'] > 0){
				//ERROR return a failure to the Ajax
				$result = 3;
			}
			else{
				function findexts ($filename){
					$filename = strtolower($filename) ;
					$exts = split("[/\\.]", $filename) ;
					$n = count($exts)-1;
					$exts = $exts[$n];
					return $exts;
				}
				//This applies the function to our file
				$ext = findexts ($_FILES['myfile']['name']); 
				$avatarExtension = $ext;
				$avatarActivate = 'yes';
				$user1 = 'user';
				//This line assigns a random number to a variable. You could also use a timestamp here if you prefer.
				$ran = $user1.$profileArray[1];
				//This takes the random number (or timestamp) you generated and adds a . on the end, so it is ready of the file extension to be appended.
				$ran2 = $ran.".";
				//This assigns the subdirectory you want to save into... make sure it exists!
				//This combines the directory, the random file name, and the extension
				$target = $target . $ran2.$ext; 
				if (move_uploaded_file($TName, $target)){
					//TOTAL Success!
					$result = 1;
					$query = 'UPDATE users SET avatarExtension=\'' . mysql_escape_string($avatarExtension) . '\', avatarActivate=\'' . mysql_escape_string($avatarActivate) . '\'WHERE ID=\''.$profileArray[1].'\'';
					mysql_query($query) or die('Error : ' . mysql_error());
				}
				else{
					//File was invalid, was it?
					$result = 4;
				}
			}
		}
		else {
			//File Size too big, send a failure back.
			$result = 5;
		}
	}
	else {
		//Bad File Extension TRY AGAIN.
		$result = 6;
	}
}
sleep(1);
/*
//New stuff
   // Edit upload location here
   $destination_path = getcwd().DIRECTORY_SEPARATOR;

   $result = 0;
   
   $target_path = '../../images/avatars/thumbs/' . basename( $_FILES['myfile']['name']);

   if(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
      $result = 1;
   }
   
   sleep(1);
<script language="javascript" type="text/javascript">alert('tmp_name:<?=$TName;?>,target:<?=$target;?>,width:<?=$width;?>,height:<?=$height;?>,truesize:<?=$truesize;?>,maxsize:<?=$sizeVar;?>,result:<?=$result;?>')</script>
*/
?>
<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>);</script>   
