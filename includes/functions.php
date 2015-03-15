<?php
	// ------------------------------------------------------------------------
	
	/**
	 * checkLogin
	 *
	 * Applies restrictions to visitors based on membership and level access
	 * Also handles cookie based "remember me" feature
	 *
	 * @access	public
	 * @param	string
	 * @return	bool TRUE/FALSE
	 */
 
 
	function checkLogin ( $levels )
	{
		session_start ();
		global $db;
		$kt = split ( ' ', $levels );
		
		if ( ! $_SESSION['logged_in'] ) {
		
			$access = FALSE;
			
			if ( isset ( $_COOKIE['cookie_id'] ) ) 
			{//if we have a cookie
				$query =  'SELECT * FROM users WHERE ID = ' . $db->qstr ( $_COOKIE['cookie_id'] );
				if ( $db->RecordCount ( $query ) == 1 ) 
				{//only one user can match that query
					$row = $db->getRow ( $query );
						//let's see if we pass the validation, no monkey business
						if ( $_COOKIE['authenticate'] == md5 ( getIP () . $row->Password . $_SERVER['HTTP_USER_AGENT'] ) ) 
						{
							//we set the sessions so we don't repeat this step over and over again
							$_SESSION['user_id'] = $row->ID;
							$_SESSION['logged_in'] = TRUE;
							
							//now we check the level access, we might not have the permission
							if ( in_array ( get_level_access ( $_SESSION['user_id'] ), $kt ) ) {
								//we do?! horray!
								$access = TRUE;
							}
						}
						else {
							$access == FALSE;
						}
					
				}
			}
		}
		else {			
			$access = FALSE;
			
			if ( in_array ( get_level_access ( $_SESSION['user_id'] ), $kt ) ) {
				$access = TRUE;
			}
		}
		
		if ( $access == FALSE ) {
			header ( "Location: /logout" );
		}		
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * get_level_access
	 *
	 * Returns the level access of a given user
	 *
	 * @param	string
	 * @access	public
	 * @return 	string
	 */
	
	function get_level_access ( $user_id )
	{
		global $db;
		$row = $db->getRow ( 'SELECT Level_access FROM ' . DBPREFIX . 'users WHERE ID = ' . $db->qstr ( $user_id ) );
		return $row->Level_access;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * logout
	 *
	 * Handles logouts
	 *
	 * @param	none
	 * @access	public
	 */
	
	function logout ()
	{
		//session must be started before anything
		session_start ();
	
		//if we have a valid session
		if ( @$_SESSION['logged_in'] == TRUE )
		{	
			//unset the sessions (all of them - array given)
			unset ( $_SESSION ); 
			//destroy what's left
			session_destroy (); 
		}
		
		//It is safest to set the cookies with a date that has already expired.
		if ( isset ( $_COOKIE['cookie_id'] ) && isset ( $_COOKIE['authenticate'] ) ) {
			/**
			 * uncomment the following line if you wish to remove all cookies 
			 * (don't forget to comment ore delete the following 2 lines if you decide to use clear_cookies)
			 */
			//clear_cookies ();
			setcookie ( "cookie_id", '', time() - KEEP_LOGGED_IN_FOR, COOKIE_PATH, ".animeftw.tv" );
			setcookie ( "authenticate", '', time() - KEEP_LOGGED_IN_FOR, COOKIE_PATH, ".animeftw.tv" );
		}
		
		//redirect the user to the default "logout" page
		header ( "Location: /login" );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * clear_cookies
	 *
	 * Clears the cookies
	 * Not used by default but present if needed
	 *
	 * @param	none
	 * @access	public
	 */
	
	function clear_cookies ()
	{
		// unset cookies
		if ( isset( $_SERVER['HTTP_COOKIE'] ) ) {
			$cookies = explode ( ';', $_SERVER['HTTP_COOKIE'] );
			//loop through the array of cookies and set them in the past
			foreach ( $cookies as $cookie ) {
				$parts = explode ( '=', $cookie );
				$name = trim ( $parts [ 0 ] );
				setcookie ( $name, '', time() - KEEP_LOGGED_IN_FOR, '/', ".animeftw.tv" );
				setcookie ( $name, '', time() - KEEP_LOGGED_IN_FOR, '/', ".animeftw.tv" );
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * set_login_sessions - sets the login sessions
	 *
	 * @access	public
	 * @param	string
	 * @return	none
	 */
	
	function set_login_sessions ( $user_id, $password, $remember )
	{
		//start the session
		session_start();
		
		//set the sessions
		$_SESSION['user_id'] = $user_id;
		$_SESSION['logged_in'] = TRUE;
		
		//do we have "remember me"?
		if ( $remember ) {
			setcookie ( "cookie_id", $user_id, time() + (60*60*24*365), "/", ".animeftw.tv", 0, 1);
			setcookie ( "authenticate", md5 ( getIP () . $password . $_SERVER['HTTP_USER_AGENT'] ), time() + (60*60*24*365), "/", ".animeftw.tv", 0, 1);
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * Validate if email
	 *
	 * Determines if the passed param is a valid email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	
	function valid_email ( $str )
	{
		return ( ! preg_match ( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str ) ) ? FALSE : TRUE;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Check unique
	 *
	 * Performs a check to determine if one parameter is unique in the database
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
 
 
	function checkUnique ( $field, $compared )
	{
		global $db;

		$query = $db->getRow ( "SELECT COUNT(*) as total FROM `" . DBPREFIX . "users` WHERE " . $field . " = " . $db->qstr ( $compared ) );

		if ( $query->total == 0 ) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Validate if numeric
	 *
	 * Validates string against numeric characters
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
 
 
	function numeric ( $str )
	{
		return ( ! preg_match ( "/^[0-9\.]+$/", $str ) ) ? FALSE : TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Validate if alfa numeric
	 *
	 * Validates string against alpha numeric characters
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
 
	function alpha_numeric ( $str )
	{
		return ( ! preg_match ( "/^([-a-z0-9])+$/i", $str ) ) ? FALSE : TRUE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Create a Random String
	 *
	 * Useful for generating passwords or hashes.
	 *
	 * @access	public
	 * @param	string 	type of random string.  Options: alunum, numeric, nozero, unique
	 * @param	none
	 * @return	string
	 */
	 
	 
	function random_string ( $type = 'alnum', $len = 8 )
	{					
		switch ( $type )
		{
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			
					switch ($type)
					{
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'numeric'	:	$pool = '0123456789';
							break;
						case 'nozero'	:	$pool = '123456789';
							break;
					}
	
					$str = '';
					for ( $i=0; $i < $len; $i++ )
					{
						$str .= substr ( $pool, mt_rand ( 0, strlen ( $pool ) -1 ), 1 );
					}
					return $str;
			break;
			case 'unique' : return md5 ( uniqid ( mt_rand () ) );
			break;
		}
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Get username - Returns the username of the logged in member based on session ID
	 *
	 * @access	public
	 * @param	string
	 * @return	string/bool
	 */
	 
	 
	function get_username ( $id )
	{
		global $db;
		
		$query = "SELECT `Username` FROM `" . DBPREFIX . "users` WHERE `ID` = " . $db->qstr ( $id );
		
		if ( $db->RecordCount ( $query ) == 1 )
		{
			$row = $db->getRow ( $query );
			
			return $row->Username;
		}
		else {
			return FALSE;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Is admin - Determines if the logged in member is an admin
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	 
	
	function isadmin ( $id )
	{
		global $db;
		
		$query = "SELECT `Level_access` FROM `" . DBPREFIX . "users` WHERE `ID` = " . $db->qstr ( $id );
		
		if ( $db->RecordCount ( $query ) == 1 )
		{
			$row = $db->getRow ( $query );
			
			if ( $row->Level_access == 1 )
			{
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Is Moderator - Determines if the logged in member is a Moderator
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	 
	
	function ismod ( $id )
	{
		global $db;
		
		$query = "SELECT `Level_access` FROM `" . DBPREFIX . "users` WHERE `ID` = " . $db->qstr ( $id );
		
		if ( $db->RecordCount ( $query ) == 1 )
		{
			$row = $db->getRow ( $query );
			
			if ( $row->Level_access == 2 )
			{
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * Is advanced user - Determines if the logged in member is a advanced user
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	 
	
	function isaduser ( $id )
	{
		global $db;
		
		$query = "SELECT `Level_access` FROM `" . DBPREFIX . "users` WHERE `ID` = " . $db->qstr ( $id );
		
		if ( $db->RecordCount ( $query ) == 1 )
		{
			$row = $db->getRow ( $query );
			
			if ( $row->Level_access == 7 )
			{
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * html2txt - converts html to text
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	 
	function html2txt ( $document )
	{
		$search = array("'<script[^>]*?>.*?</script>'si",	// strip out javascript
				"'<[\/\!]*?[^<>]*?>'si",		// strip out html tags
				"'([\r\n])[\s]+'",			// strip out white space
				"'@<![\s\S]*?–[ \t\n\r]*>@'",
				"'&(quot|#34|#034|#x22);'i",		// replace html entities
				"'&(amp|#38|#038|#x26);'i",		// added hexadecimal values
				"'&(lt|#60|#060|#x3c);'i",
				"'&(gt|#62|#062|#x3e);'i",
				"'&(nbsp|#160|#xa0);'i",
				"'&(iexcl|#161);'i",
				"'&(cent|#162);'i",
				"'&(pound|#163);'i",
				"'&(copy|#169);'i",
				"'&(reg|#174);'i",
				"'&(deg|#176);'i",
				"'&(#39|#039|#x27);'",
				"'&(euro|#8364);'i",			// europe
				"'&a(uml|UML);'",			// german
				"'&o(uml|UML);'",
				"'&u(uml|UML);'",
				"'&A(uml|UML);'",
				"'&O(uml|UML);'",
				"'&U(uml|UML);'",
				"'&szlig;'i",
				);
		$replace = array(	"",
					"",
					" ",
					"\"",
					"&",
					"<",
					">",
					" ",
					chr(161),
					chr(162),
					chr(163),
					chr(169),
					chr(174),
					chr(176),
					chr(39),
					chr(128),
					"ä",
					"ö",
					"ü",
					"Ä",
					"Ö",
					"Ü",
					"ß",
				);

		$text = preg_replace($search,$replace,$document);

		return trim ( $text );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * send_email - Handles all emailing from one place
	 *
	 * @access	public
	 * @param	string
	 * @return	bool TRUE/FALSE
	 */
	 
	function send_email ( $subject, $to, $body )
	{
		require ( BASE_PATH . "/lib/phpmailer/class.phpmailer.php" );
		
		$mail = new PHPMailer();
		
		//do we use SMTP?
		if ( USE_SMTP ) {
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->Host = SMTP_HOST;
			$mail->Port = SMTP_PORT;
			$mail->Password = SMTP_PASS;
			$mail->Username = SMTP_USER;
		}

		$mail->From = ADMIN_EMAIL;
		$mail->FromName = 'AnimeFTW Registration';
		$mail->AddAddress( $to );
		$mail->AddReplyTo ( ADMIN_EMAIL, DOMAIN_NAME );
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->WordWrap = 100;
		$mail->IsHTML ( MAIL_IS_HTML );
		$mail->AltBody  =  html2txt ( $body );

		if ( ! $mail->Send() ) {
			if ( RUN_ON_DEVELOPMENT ) {
				echo $mail->ErrorInfo;//spit that bug out :P
			}
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	/**
	 * ip_first - let's get a clean ip
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */

	function ip_first ( $ips ) 
	{
		if ( ( $pos = strpos ( $ips, ',' ) ) != false ) {
			return substr ( $ips, 0, $pos );
		} 
		else {
			return $ips;
		}
	}
	
	/**
	 * ip_valid - will try to determine if a given ip is valid or not
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */

	function ip_valid ( $ips )
	{
		if ( isset( $ips ) ) {
			$ip    = ip_first ( $ips );
			$ipnum = ip2long ( $ip );
			if ( $ipnum !== -1 && $ipnum !== false && ( long2ip ( $ipnum ) === $ip ) ) {
				if ( ( $ipnum < 167772160   || $ipnum > 184549375 ) && // Not in 10.0.0.0/8
				( $ipnum < - 1408237568 || $ipnum > - 1407188993 ) && // Not in 172.16.0.0/12
				( $ipnum < - 1062731776 || $ipnum > - 1062666241 ) )   // Not in 192.168.0.0/16
				return true;
			}
		}
		return false;
	}
	
	/**
	 * getIP - returns the IP of the visitor
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 */

	function getIP () 
	{
		$check = array(
				'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED', 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM',
				'HTTP_CLIENT_IP'
				);

		foreach ( $check as $c ) {
			if ( ip_valid ( @$_SERVER [ $c ] ) ) {
				return ip_first ( $_SERVER [ $c ] );
			}
		}

		return $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * powered_by - let's thank the man for losing nights so I can play with such tools
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 */
	
	function powered_by ()
	{
		$out = '';

		$out .= '<div align="right" class="powered">' . "\n";
		$out .= '			Powered by ' . "\n";
		$out .= '			<a href="http://www.roscripts.com" title="roscripts - Programming articles, tutorials and scripts" target="_blank">' . "\n";
		$out .= '				roScripts' . "\n";
		$out .= '			</a>' . "\n";
		$out .= '		</div>' . "\n";
		
		return $out;
	}
	
	/**
	 * sanitize - a real sanitizer
	 *
	 * @access	public
	 * @param	none
	 * @return	string
	 */
	 
	function sanitize ( $var, $santype = 3 )
	{
		if ( $santype == 1 ) {
			return strip_tags ( $var );
		}
		if ( $santype == 2 ) {
			return htmlentities ( strip_tags ( $var ), ENT_QUOTES, 'UTF-8' );
		}
		if ( $santype == 3 ) {
			if ( ! get_magic_quotes_gpc () ) {
				return addslashes ( htmlentities ( strip_tags ( $var ), ENT_QUOTES, 'UTF-8' ) );
			}
			else {
			   return htmlentities ( strip_tags ( $var ), ENT_QUOTES, 'UTF-8' );
			}
		}
	}
?>