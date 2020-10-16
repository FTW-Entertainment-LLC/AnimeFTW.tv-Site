<?php
/****************************************************************\
## FileName: logs.class.php								 
## Author: Brad Riemann								 
## Usage: Logs sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Logs extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		echo '<div  class="body-container">';
		$link = 'ajax.php?node=logs'; // base url
		if(!isset($_GET['stage']))
		{
			echo 'Please Choose a log to view:';
			echo '
				<div>
					<div class="log-container">
						<div style="padding:5px;display:inline-block;vertical-align:top;">
							<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Paypal Logs</div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=advance-member-logs\'); return false;">Advance Member Logs</a></div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=store-transaction-logs\'); return false;">Store Transaction Logs</a></div>
						</div>
						<div style="padding:5px;display:inline-block;vertical-align:top;">
							<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Cron Logs</div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=cron-runtime-logs\'); return false;">Cron Runtime Logs</a></div>
						</div>
						<div style="padding:5px;display:inline-block;vertical-align:top;">
							<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">API Logs</div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=api-hit-logs\'); return false;">API Hit Logs</a></div>
						</div>
					</div>
					<div class="log-container">
						<div style="padding:5px;display:inline-block;vertical-align:top;">
							<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Email Logs</div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=email-failure-logs\'); return false;">Failure Logs</a></div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=email-outbound-logs\'); return false;">Outbound Logs</a></div>
						</div>
						<div style="padding:5px;display:inline-block;vertical-align:top;">
							<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">User Logs</div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=moderation-logs\'); return false;">Moderation Logs</a></div>
							<div style="padding:2px;"><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=site-search-logs\'); return false;">Site Search Logs</a></div>
						</div>
					</div>
				</div>';
		}
		else
		{
			if(isset($_GET['stage']) && $_GET['stage'] == 'advance-member-logs')
			{
				echo '
				<div>
					<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">PayPal IPN Logs</div>
					<div class="individual-log-container">';
					echo $this->advancedMemberLogs();
					echo '</div>
				</div>';
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'store-transaction-logs')
			{
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'cron-runtime-logs')
			{
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'api-hit-logs')
			{
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'email-failure-logs')
			{
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'email-outbound-logs')
			{
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'moderation-logs')
			{
			}
			else if(isset($_GET['stage']) && $_GET['stage'] == 'site-search-logs')
			{
				echo '
				<div>
					<div style="margin-left:15px;font-size:16px;border-bottom:1px solid gray;width:200px;margin-bottom:5px;">Site Search Logs</div><div><a href="#" onClick="$(\'#right-column\').load(\''.$link.'&stage=site-search-logs\'); return false;">refresh</a></div>
					<div class="individual-log-container">';
					echo $this->siteSearchLogs();
					echo '</div>
				</div>';
			}
			else
			{
				echo 'What you requested.. Does not exist..';
			}
		}
		echo '</div>';
	}
	
	/*
	* private function advancedMemberLogs
	* Shows any logs for Advanced member changes
	*/
	
	private function advancedMemberLogs()
	{
		$query = "SELECT * FROM `paypal_logs` ORDER BY `submission_date` DESC LIMIT 0, 100";
		
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		
		while($row = mysqli_fetch_assoc($result))
		{
			if($row['txn_type'] == 'subscr_eot' || $row['txn_type'] == 'subscr_cancel')
			{
				$addedstyle = 'background-color:#ed5757;';
			}
			else if($row['txn_type'] == 'subscr_signup' || $row['txn_type'] == 'subscr_payment')
			{
				$addedstyle = 'background-color:#46e146;';
			}
			else
			{
				$addedstyle = '';
			}
			$basicdata = '<div style="display:inline-block;">On ' . date('F jS, Y \a\t g:i a',$row['submission_date']) . ', ' . $this->formatUsername($row['uid']) . '\'s ' . $this->deciperPaypalActionType($row['txn_type']) . '</div>';
			echo '
			<div class="log-row" style="padding:5px;border-bottom:1px solid black;width:100%;min-height:14px;' . $addedstyle . '">';
			if($this->UserArray[2] == 1)
			{
				// Admin can see moar stuffs..
				echo '
				<div>';
				if($row['txn_type'] == 'subscr_payment')
				{
					echo '
					<div style="width:20px;display:inline-block;"><a id="btn-' . $row['id'] . '" href="#" onClick="return false;" class="expand-hidden-data">+</a></div>';
				}
				else
				{
					echo '<div style="width:20px;display:inline-block;">&nbsp;</div>';
				}
				echo $basicdata;
				echo '
				</div>
				<div id="hidden-data-' . $row['id'] . '" style="display:none;" class="the-hidden-data">
					<div style="display:inline-block;width:49%;vertical-align:top;">
						<div style="font-size:8px;">Item Name</div>
						<div>' . $row['item_name'] . '</div>
					</div>
					<div style="display:inline-block;width:49%;vertical-align:top;">
						<div style="width:32%;vertical-algin:top;display:inline-block;">
							<div style="font-size:8px;">Gross Income</div>
							<div>' . $row['mc_gross'] . '</div>
						</div>
						<div style="width:32%;vertical-algin:top;display:inline-block;">
							<div style="font-size:8px;">Net Income</div>
							<div>' . ($row['mc_gross']-$row['mc_fee']) . '</div>
						</div>
						<div style="width:32%;vertical-algin:top;display:inline-block;">
							<div style="font-size:8px;">Total Fees</div>
							<div>' . $row['mc_fee'] . '</div>
						</div>
					</div>
				</div>';
			}
			else
			{
				echo $basicdata;
			}
			echo '
			</div>';
			unset($basicdata);
		}
		echo '
		<script>
			$(function() {
				$(".expand-hidden-data").on("click", function() {
					var this_id = $(this).attr("id").substring(4);
					$(".the-hidden-data").hide();
					$("#hidden-data-" + this_id).toggle();
				});
			});
		</script>';
	}
	
	private function deciperPaypalActionType($txn_type)
	{
		//subscr_cancel, subscr_eot, subscr_failed, subscr_payment, subscr_signup
		if($txn_type == 'subscr_cancel')
		{
			return 'subscription was cancelled';
		}
		elseif($txn_type == 'subscr_eot')
		{
			return 'advanced membership expired and was moved back';
		}
		elseif($txn_type == 'subscr_failed')
		{
			return 'subscription payment failed, a new attempt will be made in 10 days';
		}
		elseif($txn_type == 'subscr_payment')
		{
			return 'account sent a payment';
		}
		elseif($txn_type == 'subscr_signup')
		{
			return 'account was signed up for advanced membership';
		}
		else
		{
		}
	}
	
	private function siteSearchLogs()
	{
		$query = "SELECT * FROM `search` ORDER BY `date` DESC LIMIT 0, 300";
		
		$result = mysqli_query($query) or die('Error : ' . mysqli_error());
		
		while($row = mysqli_fetch_assoc($result))
		{
			if($row['uid'] == NULL)
			{
				// this was a gust
				$Username = 'Guest';
			}
			else
			{
				$Username = $this->formatUsername($row['uid']);
			}
			
			echo '
			<div class="log-row" style="padding:5px;border-bottom:1px solid black;width:100%;min-height:14px;">
				<div style="display:inline-block;width:100px;vertical-align:top;">' . date('j/m/Y h:j:s a',$row['date']) . '</div>
				<div style="display:inline-block;width:100px;vertical-align:top;">' . $Username . '</div>
				<div style="display:inline-block;width:180px;vertical-align:top;">' . $row['ip'] . '<br />' . gethostbyaddr($row['ip']) . '</div>
				<div style="display:inline-block;width:250px;vertical-align:top;">' . $row['user_agent'] . '</div>
				<div style="display:inline-block;width:150px;vertical-align:top;">' . $row['string'] . '</div>
			</div>';
		}
	}
}

?>