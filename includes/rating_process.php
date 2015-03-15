<?
/*
Dynamic Star Rating Redux
Developed by Jordan Boesch
www.boedesign.com
Licensed under Creative Commons - http://creativecommons.org/licenses/by-nc-nd/2.5/ca/

Used CSS from komodomedia.com.
*/
header("Cache-Control: no-cache");
header("Pragma: nocache");

include("config.php");
// Cookie settings
$expire = time() + 99999999;
$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost

// escape variables
function escape($val){

	$val = trim($val);
	
	if(get_magic_quotes_gpc()) {
       	$val = stripslashes($val);
     }
	 
	 return mysql_real_escape_string($val);
	 
}

// IF JAVASCRIPT IS ENABLED

if($_POST){

	
	$id = escape($_POST['id']);
	$id = mysql_real_escape_string($id);
	$uid = escape($_POST['uid']);
	$uid = mysql_real_escape_string($uid);
	$rating = (int) $_POST['rating'];
	$rating = mysql_real_escape_string($rating);
	
	if($rating <= 5 && $rating >= 1){
	
		if(@mysql_fetch_assoc(mysql_query("SELECT id FROM ratings WHERE IP = '".$uid."' AND rating_id = '$id'")) || isset($_COOKIE['has_voted_'.$id])){
		
			echo 'already_voted';
			
		} else {

			
			setcookie('has_voted_'.$id,$id,$expire,'/',$domain,false);
			mysql_query("INSERT INTO ratings (rating_id,rating_num,IP) VALUES ('$id','$rating','".$uid."')") or die(mysql_error());
			
			$total = 0;
			$rows = 0;
			
			$sel = mysql_query("SELECT rating_num FROM ratings WHERE rating_id = '$id'");
			while($data = mysql_fetch_assoc($sel)){
			
				$total = $total + $data['rating_num'];
				$rows++;
			}
			
			$perc = ($total/$rows) * 20;
			
			echo round($perc,2);
			//echo round($perc/5)*5;
			
		}
		
	}

}

// IF JAVASCRIPT IS DISABLED

if($_GET){

	$id = escape($_GET['id']);
	$rating = (int) @$_GET['rating'];
	
	// If you want people to be able to vote more than once, comment the entire if/else block block and uncomment the code below it.
	
	if($rating <= 5 && $rating >= 1){
	
		if(@mysql_fetch_assoc(mysql_query("SELECT id FROM ratings WHERE IP = '".$_SERVER['REMOTE_ADDR']."' AND rating_id = '$id'")) || isset($_COOKIE['has_voted_'.$id])){
		
			echo 'already_voted';
			
		} else {
			
			setcookie('has_voted_'.$id,$id,$expire,'/',$domain,false);
			mysql_query("INSERT INTO ratings (rating_id,rating_num,IP) VALUES ('$id','$rating','".$_SERVER['REMOTE_ADDR']."')") or die(mysql_error());
			
		}
		
		header("Location:".@$_SERVER['HTTP_REFERER']."");
		die;
		
	}
	else {
	
		echo 'You cannot rate this more than 5 or less than 1 <a href="'.@$_SERVER['HTTP_REFERER'].'">back</a>';
		
	}
	
	

}

?>
