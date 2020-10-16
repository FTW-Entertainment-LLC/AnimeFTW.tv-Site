<?php
include('global_functions.php');

				$query = "SELECT id, name FROM page_comments WHERE uid='0' ORDER BY id";
			$result2 = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
			
  				while(list($id,$name) = mysqli_fetch_array($result2))
				{
					$query   = "SELECT id FROM users WHERE Username='".$name."'";
					$result  = mysqli_query($conn, $query) or die('Error : ' . mysqli_error()); 
					$row     = mysqli_fetch_array($result, MYSQL_ASSOC);
					
					$query = "UPDATE page_comments SET uid='".$row['id']."' WHERE id = $id";
					$result = mysqli_query($conn, $query) or die('Error : ' . mysqli_error());
				}
				echo "done";
?>