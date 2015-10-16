<?php
/****************************************************************\
## FileName: register.v2.class.php									 
## Author: Brad Riemann										 
## Usage: Registration Class
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Register extends Config {

	public $Data, $UserID, $DevArray;
	private $AllowSpecialChars, $MessageCodes;

	public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL)
	{
		parent::__construct();
		
		$this->MessageCodes = array (
			'About' => 'These are the available status codes for Registration feedback.',
			'Result Codes' => array(
				'01-200' => array(
					'Status' => '200',
					'Message' => 'Account registration has been completed successfully, activation is required to continue.',
					'Explanation' => 'The registration request has been submitted correctly and without issue.'
				),
				'01-400' => array(
					'Status' => '400',
					'Message' => 'The Username is already taken, please try again.',
					'Explanation' => 'The requested username is already in use, the user will need to try again.'
				),
				'01-401' => array(
					'Status' => '401',
					'Message' => 'The Password does not meet the requirements of the system, please try again.',
					'Explanation' => 'If a password does not meet the predefined password requirements, this error is thrown.'
				),
				'01-402' => array(
					'Status' => '402',
					'Message' => 'The Email address is not valid or is already in use, please try again.',
					'Explanation' => 'If the email address is invalid, or is considered a spam email, or is already in use, this error is given.'
				),
				'01-403' => array(
					'Status' => '403',
					'Message' => 'The birthday given is invalid, please complete and try again.',
					'Explanation' => 'The user MUST give a valid email address, if it is not valid, this error will be thrown.'
				),
				'01-404' => array(
					'Status' => '404',
					'Message' => 'There was a missing registration piece, please try again.',
					'Explanation' => 'A portion of the registration details were missing, so we have to make the user retry the steps.'
				)
			)
		);
		
		$this->Data = $Data;
		$this->UserID = $UserID;
		$this->DevArray = $DevArray;
		$this->AllowSpecialChars = 0;
	}
	
	public function showRegistrationCodes()
	{
		return $this->MessageCodes;
	}
	
	/*	** Q&D exception-based registerUser()	*		private $ERROR_NOUSERNAME 	= 0;	private $ERROR_NOPASSWORD 	= 1;	private $ERROR_NOEMAIL		= 2;	private $ERROR_NOBIRTHDAY	= 3;	private $ERROR_INVALIDEMAIL	= 4;	private $ERROR_EMAILINUSE	= 5;	private $ERROR_USERINUSE	= 6;	private $ERROR_INVALIDBDAY	= 7;		public function registerUser()	{		try		{			$this->register_verifyData();			$this->bool_validateEmailAddress();			$this->bool_checkEmailUsage();			$this->bool_validateUsername();			$this->bool_validateBirthday();			return $this->array_processAccountRegistration();		}		catch( Exception $e )		{			$message = array();			switch( $e )			{				case $ERROR_NOUSERNAME:					$message = array( DATAHERE );					break;				case $ERROR_NOPASSWORD:					$message = array( DATAHERE );					break;								(...)			}						return $message;		}	}		private function verifyData()	{		if( !isset( $this->Data['username'] )		{			throw new Exception( $ERROR_NOUSERNAME );		}		elseif( !isset( $this->Data['password'] )		{			throw new Exception( $ERROR_NOPASSWORD );		}		elseif( !isset( $this->Data['email'] )		{			throw new Exception( $ERROR_NOEMAIL );		}		elseif( !isset( $this->Data['birthday'] )		{			throw new Exception( $ERROR_NOBIRTHDAY );		}	}		*/						public function registerUser()
	{
		//return $this->Data;
		if(isset($this->Data['username']) && isset($this->Data['password']) && isset($this->Data['email']) && isset($this->Data['birthday']))
		{
			// Check to make sure the email address is indeed valid as well as not used in the system
			if($this->bool_validateEmailAddress() == TRUE && $this->bool_checkEmailUsage() == TRUE)
			{
				//success, now we need to check the username, to make sure it is not active and valid
				if($this->bool_validateUsername() == TRUE)
				{
					// there was no username or display name by the request, so we can move forward and make sure the birthday is in a format we understand.
					if($this->bool_validateBirthday() == TRUE)
					{
						// the birthday passes, we can now let the signup process proceed.
						return $this->array_processAccountRegistration();
					}
					else
					{
						// failed.. just failed..
						return array('status' => $this->MessageCodes["Result Codes"]["403"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["403"]["Message"]);
					}
				}
				else
				{
					// The username was in use on the system already, let's let them know.
					return array('status' => $this->MessageCodes["Result Codes"]["400"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["400"]["Message"]);					
				}
			}
			else
			{
				// the email was not valid, we will need to let them know
				return array('status' => $this->MessageCodes["Result Codes"]["402"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["402"]["Message"]);
			}
		}
		else
		{
			return array('status' => $this->MessageCodes["Result Codes"]["404"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["404"]["Message"]);
		}
	}
	
	private function bool_validateEmailAddress()
	{
		return filter_var($this->Data['email'], FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $this->Data['email']);
	}
	
	private function bool_checkEmailUsage()
	{
		// We need to check to see if the email is actually in use by someone else in the system.
		$query = "SELECT `id` FROM `" . $this->MainDB . "`.`users` WHERE `Email` = '" . $this->mysqli->real_escape_string($this->Data['email']) . "'";
		$result = $this->mysqli->query($query);
		// We need to count the rows
		$count = $result->num_rows; // this gets us that
		
		if($count > 0)
		{
			// there is an account in the system with that email, they cannot pass
			return FALSE;
		}
		else
		{
			// CLEAR!
			return TRUE;
		}
	}
	
	private function bool_validatePasswordComplexity()
	{
		// down the line, we might want to enforce password complexity rules.. this can help with that..
		return TRUE;
	}
	
	private function bool_validateUsername()
	{
		// we need to validate the username, formatting it correctly and then making sure it's not on the site already..
		$Username = $this->formatUsername($this->Data['username']); // Format the username to site standards.
		$query = "SELECT `id` FROM `" . $this->MainDB . "`.`users` WHERE `Username` = '" . $this->mysqli->real_escape_string($Username) . "' OR `display_name` = '" . $this->mysqli->real_escape_string($Username) . "'";
		$result = $this->mysqli->query($query);
		// We need to count the rows
		$count = $result->num_rows; // this gets us that
		
		if($count > 0)
		{
			// an account has the display name or username (or both!) so the user cannot use that name, in any shape..
			return FALSE;
		}
		else
		{
			// nothing turned up.. feel free to move forward..
			return TRUE;
		}
	}
	
	// The birthday system on AnimeFTW.tv is convoluted, and could be done better.. it currently works through a three part solution for the month, day and year
	// as such we have to validate that the birthday string given to us can be pushed to the database without issue.
	private function bool_validateBirthday()
	{
		if(strlen($this->Data['birthday']) == 8)
		{
			// our defacto standard length is 8 characters MMDDYYYY, they need to supply that at all times
			return TRUE;
		}
		else
		{
			// formatted incorrectly.
			return FALSE;
		}
	}
	
	// The original AnimeFTW.tv site couldn't handle spaces, as of 5/16/2014 90% of the scripts do not rely on the username
	// however because we are still transitioning to 100% user id based system, we cannot let this not continue.
	private function formatUsername()
	{
		if($this->AllowSpecialChars == 1)
		{
			$output = $this->Data['username'];
		}
		else
		{
			// Replace spaces with underscores
			$output = preg_replace("/\s/e" , "_" , $this->Data['username']);
		
			// Remove non-word characters
			$output = preg_replace("/\W/e" , "" , $output);
		}
		
		// We make it all lower so that the Premium Members have more incentive to become said members
		return strtolower($output);
	}
	
	private function array_processAccountRegistration()
	{
		$Birthday = array('Month' => substr($this->Data['birthday'], 0, 2), 'Day' => substr($this->Data['birthday'], 2, 2), 'Year' => substr($this->Data['birthday'], 4, 4));
		$RandomString = $this->stringRandomizer('alnum',32);
		$query = "INSERT INTO `" . $this->MainDB . "`.`users` (`Username`, `display_name`, `Password`, `registrationDate`, `Email`, `Random_key`, `ageDate`, `ageMonth`, `ageYear`, `staticip`, `timeZone`) VALUES ('" . $this->mysqli->real_escape_string($this->formatUsername()) . "', '" . $this->mysqli->real_escape_string($this->formatUsername()) . "', '" . $this->Build($this->Data['password'],$this->Data['username'],'md5') . "', '" . time() . "', '" . $this->mysqli->real_escape_string($this->Data['email']) . "', '" . $RandomString . "', '" . $Birthday['Day'] . "', '" . $Birthday['Month'] . "', '" . $Birthday['Year'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '-6')";
		// grab the results.
		$result = $this->mysqli->query($query);
		
		// compose what the body will be made of.
		$body = "Dear " . $this->Data['username'] . ", this is your activation link to join our website at animeftw.tv. \n\n In order to confirm your membership please click on the following link: https://www.animeftw.tv/confirm?ID=" . $this->mysqli->insert_id . "&key=" . $RandomString . " \n\n After you confirm your status with us, please go visit https://www.animeftw.tv/rules - \"our Rules\" and https://www.animeftw.tv/faq - our FAQ and become associated with the basics of the site, we try to keep order as best as we can so we have some rules in place. \n\n Thank you for joining, please go and visit our rules after you have logged in to familiarize yourself with our site policies! https://www.animeftw.tv/rules \n\n Regards, \n\n FTW Entertainment LLC & AnimeFTW Staff.";
		
		// Sending the text based email ONLY.
		include("email.v2.class.php");
		$Email = new Email($this->Data['email']);
		$Email->Send(7,$body);
		
		// Insert default notification values into the database.
		$this->mysqli->query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $this->mysqli->insert_id . "', " . time() . ", " . time() . ", '2', '4', '0');");
		$this->mysqli->query("INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . $this->mysqli->insert_id . "', " . time() . ", " . time() . ", '7', '14', '0');");
		
		// success, now let them know they need to check their email to validate
		return array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => $this->MessageCodes["Result Codes"]["01-200"]["Message"]);	
	}
}
