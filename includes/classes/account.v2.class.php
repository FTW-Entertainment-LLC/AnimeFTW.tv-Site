<?php
/****************************************************************\
## FileName: account.v2.class.php									 
## Author: Brad Riemann										 
## Usage: All Profile updates and queries go through the account class.
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Account extends Config {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function array_validateLogin($Type,$FormData)
	{
		// The object of this method is to validate a login with information given to it.
		// once validated, it will set the session details.
		//$query = "SELECT  FROM `" . $this->MainDB . "`.`users`";
		//$results = $this->mysqli->query($query);
		
		// a type of zero is from the mobile app, from 1 is the normal webpage.
		
		if($Type == 0)
		{
			$Username = $FormData['Username'];
			$Password = $FormData['Password'];
		}
		else
		{
			$Username  = $_POST['username'];
			$Password = $_POST['password'];
		}
		
		// validate that the post comes from a valid AFTW location.
		if(isset($FormData['_submit_check']))
		{
			if ($Username != '' && $Password != '')
			{
				if($this->bool_checkEmail($Username))
				{
					// if the username is an email, we change the query string a bit.
					$query = 'SELECT `ID`, `Username`, `Active`, `Reason`, `Password` FROM `' . $this->MainDB . '`.`users` WHERE Email = \'' . $this->mysqli->real_escape_string( $Username ) . '\' AND Password = \'' . $this->mysqli->real_escape_string(md5($Password)) . "'";
				}
				else
				{
					// if the username is a real username.. then we use a different string.
					$query = "SELECT `ID`, `Username`, `Active`, `Reason`, `Password` FROM `" . $this->MainDB . "`.`users` WHERE `Username` = '" . $this->mysqli->real_escape_string( $Username ) . "' AND `Password` = '" . $this->mysqli->real_escape_string(md5($Password)) . "'";
				}
				$results = $this->mysqli->query($query);
				$count = mysqli_num_rows($results); // count the rows, if one comes back then the login was successful.
				if($count == 1)
				{
					$row = $results->fetch_assoc();
					if ( $row['Active'] == 1 )
					{
						$this->setLoginSessions($row['ID'],$row['Password'],(@$FormData['remember']) ? TRUE : FALSE);
						$query = "UPDATE `" . $this->MainDB . "`.`users` SET `lastLogin` = '" . time() . "' WHERE `ID`='" . $row['ID'] . "'";
						$this->mysqli->query($query);
						$query = "INSERT INTO `" . $this->MainDB . "`.`logins` (`ip`, `date`, `uid`, `agent`) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', '" . time() . "', '" .$row['ID'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "')";
						$this->mysqli->query($query);	
						if($Type == 1)
						{
							// its a normal web page
							if ($FormData['last_page'] == '')
							{
								header("Location: http://" . $_SERVER['HTTP_HOST'] . "/user/" . $Username);
								exit;
							}
							else if ($FormData['last_page'] == 'http://' . $_SERVER['HTTP_HOST'] . '/login.php' || $FormData['last_page'] == 'https://' . $_SERVER['HTTP_HOST'] . '/login')
							{
								header("Location: http://" . $_SERVER['HTTP_HOST'] . "/user/" . $Username);
								exit;
							}
							else 
							{
								header("Location: " . $FormData['last_page']);
								exit;
							}
						}
						else
						{
							$error = "<!-- Success --> Login Successful.";
							$failed = FALSE;
						}
					}
					else if($row['Active'] == 0)
					{
						$error = 'Your membership was not activated. Please open the email that we sent and click on the activation link.';
						$failed = TRUE;
					}
					else if($row['Active'] == 2)
					{
						$error = 'You are suspended!<br /><br /> Reason: ' . $row['Reason'] . '<br /><br />If you feel this suspension is in error, please email: support@animeftw.tv with your username and the reason given above.';
						$failed = TRUE;
					}
				}
				else 
				{		
					$query = "INSERT INTO `failed_logins` (`name`, `password`, `ip`, `date`) VALUES	('" . $this->mysqli->real_escape_string($Username) . "', '" . $this->mysqli->real_escape_string($Password) . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . time() . "')";
					$this->mysqli->query($query);	
					$error = 'Login failed! Password or Username is Incorrect.';
					$failed = TRUE;
				}
			}
			else {
				$error = 'Please use both your username and password to access your account.';
				$failed = TRUE;
			}
		}
		else
		{
			$error = 'The path you took to get to this location was incorrect, go back and try again.';
			$failed = TRUE;
		}
		return $arrray = array("failed" => $failed, "message" => $error);
	}
	
	
	private function bool_checkEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
	}
	
	private function setLoginSessions($id,$password,$remember)
	{
		// was creating this function last.
		//start the session
		session_start();
		
		//set the sessions
		$_SESSION['user_id'] = $id;
		$_SESSION['logged_in'] = TRUE;
		
		//do we have "remember me"?
		if($remember) 
		{
			setcookie("cookie_id", $id, time() + (60*60*24*365), "/", ".animeftw.tv");
			setcookie("authenticate", md5($_SERVER['REMOTE_ADDR'] . $password . $_SERVER['HTTP_USER_AGENT'] ), time() + (60*60*24*365), "/", ".animeftw.tv" );
		}
	}
	
	public function removeLoginSessions($Type = 1)
	{
		//session must be started before anything
		session_start ();
	
		//if we have a valid session
		if(@$_SESSION['logged_in'] == TRUE)
		{	
			//unset the sessions (all of them - array given)
			unset ( $_SESSION ); 
			//destroy what's left
			session_destroy (); 
		}
		
		//It is safest to set the cookies with a date that has already expired.
		if(isset($_COOKIE['cookie_id']) && isset($_COOKIE['authenticate']))
		{
			/**
			 * uncomment the following line if you wish to remove all cookies 
			 * (don't forget to comment ore delete the following 2 lines if you decide to use clear_cookies)
			 */
			//clear_cookies ();
			setcookie("cookie_id", '', time() - (60*60*24*365), "/", ".animeftw.tv");
			setcookie("authenticate", '', time() - (60*60*24*365), COOKIE_PATH, ".animeftw.tv");
		}
		
		if($Type == 0)
		{
			// 0 type is for the mobile site
			//header("location: http://www.animeftw.tv/m/?logout=success");
			echo '
			<script>
				var url = "http://www.animeftw.tv/m/?logout=success";    
				$(location).attr(\'href\',url);
			</script>';
		}
		else
		{
			//redirect the user to the default "logout" page
			header ( "Location: /login" );
		}
	}
	
	public function __destruct()
	{
	}
}