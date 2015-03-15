<?php
include('global_functions.php');

				$query = "SELECT id, name FROM page_comments WHERE uid='0' ORDER BY id";
			$result2 = mysql_query($query) or die('Error : ' . mysql_error());
			
  				while(list($id,$name) = mysql_fetch_array($result2))
				{
					$query   = "SELECT id FROM users WHERE Username='".$name."'";
					$result  = mysql_query($query) or die('Error : ' . mysql_error()); 
					$row     = mysql_fetch_array($result, MYSQL_ASSOC);
					
					$query = "UPDATE page_comments SET uid='".$row['id']."' WHERE id = $id";
					$result = mysql_query($query) or die('Error : ' . mysql_error());
				}
				echo "done";
?>