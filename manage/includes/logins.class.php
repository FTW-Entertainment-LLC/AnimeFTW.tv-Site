<?php
/****************************************************************\
## FileName: logins.class.php
## Author: Brad Riemann
## Usage: AFTW Management Logins are handled by this class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Logins extends Config {

	var $LoginError;

	public function __construct()
	{
		parent::__construct(TRUE);
		$this->LoginError = '';
	}

	public function processLogins()
	{
		// we need to get the email to make the process work.
		$query = mysqli_query("SELECT Email FROM users WHERE Username = '" . mysqli_real_escape_string($_POST['logname']) . "'");
		$row = mysqli_fetch_assoc($query);
		$UserEmail = $row['Email'];
		// now for the real login
		$PhaseTwo = $this->Build($_POST['passw0rd'],$UserEmail);
		$query = "SELECT ID, Username FROM users WHERE Email = '" . $UserEmail . "' AND phasetwo = '" . $PhaseTwo . "'";
		$results = mysqli_query($query);
		if(!$results)
		{
			echo 'Error in the MySQL Query:' . $PhaseTwo;
			exit;
		}
		else
		{
			$count = mysqli_num_rows($results);
			if($count < 1)
			{
				include("../includes/classes/email.class.php");
				if(isset($_POST['generatehash']))
				{
					echo 'Your new password has been sent to Brad, please hang tight.';

					$vars = "This is a Password update request.\n\n";
					$vars .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
					$vars .= "Username: " . $_POST['logname'] . "\n";
					$vars .= "Password Hash: " . $PhaseTwo . "\n\n";

					$Email = new Email();
					$Email->Send(6,$vars);
				}
				else
				{
					echo 'The Username or Password was incorrect. Please try again.';

					$vars = "There was a failed login attempt to the AnimeFTW.tv Management Console.\n\n";
					$vars .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . " (" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . ")\n";
					$vars .= "Username: " . $_POST['logname'] . "\n";
					$vars .= "Password: " . substr($_POST['passw0rd'], 0, 1) . str_repeat('*', (strlen($_POST['passw0rd']) -1)) . "\n\n";
					$vars .= "Please be aware of this login, if there are multiple attempts a banning needs to occur.\n";

					$Email = new Email("support@animeftw.tv");
					$Email->Send(5,$vars);
				}
			}
			else
			{
				echo 'Success';
				$row = mysqli_fetch_assoc($results);
				$this->setSessions($row['ID']);
			}
		}
	}

	public function loginCode()
	{
		$Data = '
		<div id="body-wrapper2">
			<div id="welcome-wrapper">
				<div style="margin:auto;float:left;position:absolute;z-index;-1;margin:-140px 0 0 -290px;"><img src="//i.animeftw.tv/fay-maid.png" alt="" /></div>
				<form id="welcome-form" autocomplete="off">';
				if(isset($_GET['create']) && $_GET['create'] == 'yes')
				{
					$Data .= '				<input type="hidden" name="generatehash" value="true" />';
				}
				$Data .= '
				<input type="hidden" name="submitcheck" value="10010" />
				<input type="hidden" name="method" value="login-form" />
				<div class="welcome-row" style="margin-bottom:-5px;">
					<div class="welcome-header" style="text-align:left;color:#6f6f6f;font-size:18px;padding:5px 0 0 10px;">Please Log in.</div>
				</div>
				<div id="login-error-wrapper">';
				if(isset($_GET['logout']))
				{
					$Data .= '<div class="welcome-row" style="margin-bottom:-5px;"><div id="welcome-error">Your Session has been logged out.</div></div>';
				}
				else
				{}
				$Data .= '</div>
				<div class="welcome-row">
					<div class="welcome-text-mini"><label for="logname">Username</label></div>
					<div style="margin-left:-20px;">
						<input type="text" name="logname" id="logname" value="' . $this->UserArray[5] . '" style="width:200px;height:24px;" class="form-text-input" />
					</div>
				</div>
				<div class="welcome-row">
					<div class="welcome-text-mini"><label for="passw0rd">Password</label></div>
					<div style="margin-left:-20px;">
						<input type="password" name="passw0rd" id="passw0rd" value="" style="width:200px;height:24px;" class="form-text-input" />
					</div>
				</div>
				<div class="welcome-row">
					<div style="margin-left:-20px;display:none;" id="awesome-button">
						<input type="button" name="submit" value=" Submit " class="welcome-submit" id="welcome-submit" />
					</div>
				</div>
				<div id="loading-spot" style="display:none;">
				</div>
				</form>
			</div>
		</div>
		<script>
			$(document).ready(function(){
				$("#logname").on("keypress", function() {
					if(!$("#passw0rd").val())
					{
					}
					else
					{
						$("#awesome-button").show();
					}
				});
				$("#passw0rd").on("keypress", function() {
					if(!$("#logname").val())
					{
					}
					else
					{
						$("#awesome-button").show();
					}
				});
				$(document).keyup(function(event) {
					if (event.keyCode == 13) {
						$("#welcome-submit").click();
					}
				})
			});
		</script>
		<script>
			$(document).ready(function(){


				$("#welcome-submit").click(function() {
					$.post("ajax.php", $("#welcome-form").serialize(), function(){
					})
					.done(function(data) {
						if(data.indexOf("Success") >= 0)
						{
							$("#login-error-wrapper").hide().html(\'<div class="welcome-row" style="margin-bottom:-5px;"><div id="welcome-msg">Login Successful. Redirecting..</div></div>\').delay(500).fadeIn();
							setTimeout(function(){ window.location = "https://' . $_SERVER['HTTP_HOST'] . '/manage/"; }, 3000);
						}
						else
						{
							$("#login-error-wrapper").hide().html(\'<div class="welcome-row" style="margin-bottom:-5px;"><div id="welcome-error">\' + data + \'</div></div>\').delay(500).fadeIn();
						}
					})
					.fail(function(data) {
						alert("failed..");
					});
				});
			});
		</script>';
		return $Data;
	}

	private function setSessions($UserID)
	{
		session_start();

		$_SESSION['m_user_id'] = $UserID;
		$_SESSION['m_logged_in'] = TRUE;
	}

	public function checkSessions()
	{
		session_start();

		if(isset($_SESSION['m_logged_in']) && $_SESSION['m_logged_in'] == TRUE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function removeSessions()
	{
		session_start();

		$_SESSION['m_user_id'] = 0;
		$_SESSION['m_logged_in'] = FALSE;
	}
}