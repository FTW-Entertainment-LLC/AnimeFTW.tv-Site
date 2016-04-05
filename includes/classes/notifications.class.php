<?php
/****************************************************************\
## FileName: notifications.class.php									 
## Author: Brad Riemann										 
## Usage: Notifications Class, for the win
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWNotifications extends Config {
	/* 
	# This Script is the core to the AFTW Notification system.
	# The Notification System is a Hybrid notification system (or sorts), values are input into the table and output
	# to end users as a stream of events. The idea is that we can record one instance for an episode addition
	# (for Watch list users on airing series), then Friend List additions, Profile Comments and various other
	# additions to the site can be logged and rotated with a 7 day TTL on an individual basis. Hey, i'm making this up
	# as i go along.. -Brad
	*/
	
	//#- Vars -#\\
	var $uid, $darray, $UserArray;
	
	//#- Contruct -#\\
	public function __construct($uid = NULL)
	{
		parent::__construct();
		$this->darray = array(1 => 86400, 2 => 172800, 3 => 259200, 4 => 345600, 5 => 432000, 6 => 518400, 7 => 604800);
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	//#- Output -#\\
	public function Output(){
		$this->BuildList();
		$this->RecordView();
	}
	
	# function CronJob
	public function CronJob(){
		//build the date to bind off of.
		$ctime = time();
		$past = $ctime-(4*$this->darray[7]); //we want the date 4 weeks in the past
		//build the query, straight forward stuff
		mysql_query("DELETE FROM notifications WHERE date <= $past");
		mysql_query("INSERT INTO crons_log (`id`, `cron_id`, `start_time`, `end_time`) VALUES (NULL, '13', '" . time() . "', '" . time() . "');");
		mysql_query("UPDATE crons SET last_run = '" . time() . "', status = 0 WHERE id = 13");
	}
	
	public function ShowSprite()
	{
		if($this->UserArray[0] == 1)
		{
            $query = "SELECT COUNT(id) as Total FROM notifications WHERE notifications.date > ".$this->UserArray[12]." AND (notifications.uid = ".$this->UserArray[1]." OR (notifications.uid IS NULL AND notifications.d1 = (SELECT watchlist.sid FROM watchlist WHERE watchlist.sid = notifications.d1 AND watchlist.uid = ".$this->UserArray[1].")))";
			$result = mysql_query($query); 
			if(!$result)
			{
			}
			else
			{
				$CountNotes = mysql_result($result,0);
				if($CountNotes > 0)
				{
					return '<span class="JewelNotif2 disBlock" id="requestJewelNotif">'.$CountNotes.'</span>';
				}
				else 
				{
				}
			}
		}
		else {
		}
	}
	
	public function showProfile()
	{
		// make sure the user is logged in first.
		if($this->UserArray[0] == 1)
		{
			$query = "SELECT COUNT(notifications.id) FROM notifications WHERE (notifications.uid = ".$this->UserArray[1]." OR (notifications.uid IS NULL AND notifications.d1 = (SELECT watchlist.sid FROM watchlist WHERE watchlist.sid = notifications.d1 AND watchlist.uid = ".$this->UserArray[1]."))) ";
			$result = mysql_query($query); 
			if(!$result)
			{
				echo 'There was an error executing the query.';
			}
			else
			{
				$CountNotes = mysql_result($result,0);
				echo '<div class="fds">My Site notifications</div><br />';
				if($CountNotes > 0)
				{
					echo $this->BuildList(FALSE);
				}
				else
				{
					echo '<div style="font-size:16px;margin:10px;color:#d0d0d0;" align="center">You have no notifications for your account, oh noes!</div> ';
				}
			}
		}
		else
		{
			echo 'Please <a href="/login">Log in</a> to view your Notification listing.';
		}
	}
	
	//#- Private Functions -#\\
	
	# function Query
	private function Query($q){
		$query = mysql_query($q); 
		return $query;
	}
	
	# function BuildIcon
	private function BuildList($spriteMode=TRUE)
	{
		if($spriteMode == TRUE)
		{
			// we limit this to 8 rows.
			$queryLimit = " LIMIT 0, 8";
		}
		else
		{
			// we limit this to 8 rows.
			$queryLimit = " LIMIT 0, 30";
		}
		
		mysql_query("SET NAMES 'utf8'"); 
		$query = "SELECT notifications.* FROM notifications WHERE (notifications.uid = ".$this->UserArray[1]." OR (notifications.uid IS NULL AND notifications.d1 = (SELECT watchlist.sid FROM watchlist WHERE watchlist.sid = notifications.d1 AND watchlist.uid = ".$this->UserArray[1]."))) ORDER BY notifications.date DESC" . $queryLimit;
		$result = $this->Query($query);
		//echo '<ul>';
		if($spriteMode == TRUE)
		{
			echo '<li><div align="left" class="notificationHeader">AnimeFTW.tv Site Notifications</div></li>';
		}
		$count = mysql_num_rows($result);
		
		if($count < 1)
		{
			if($spriteMode == TRUE)
			{
				echo '<li class="mainli"><div align="left">You have no notifications.</div></li>';
			}
			else
			{
				echo '<div>You do not have any notifications! Add a friend or add an airing series to get updates!</div>';
			}
		}
		else
		{
			while(list($id,$uid,$date,$type,$d1,$d2,$d3) = mysql_fetch_array($result)){
				$this->PopulateRow($id,$uid,$date,$type,$spriteMode,$d1,$d2,$d3);
				//echo 'id: '.$id.', uid: '.$uid.', date: '.$date.', type: '.$type.', d1: '.$d1.', d2: '.$d2.', d3: '.$d3.'<br />';
			}
		}
		if($spriteMode == TRUE)
		{
			echo '<li><div align="center" class="notificationFooter"><a href="/user/'.$this->UserArray[5].'">View All Notifications</a></div></li>';
		}
		echo '
		<script>		
			$(".add-friend-link").click(function(){
				var this_id = $(this).attr("id").substring(5);
				
				var request_url = "/scripts.php?view=profile&subview=friend-notification&id=" + this_id;
				
				$.ajax({
					type: "GET",
					processData: true,
					url: request_url
				})
				.done(function(data) {
					if(data.indexOf("Success") >= 0)
					{
						$("#" + $(this).attr("id")).text("New Friend Added!");
						return false;
					}
					else
					{
						$("#" + $(this).attr("id")).text("Something went wrong");
						return false;
					}
				})
				.fail(function(data) {
					alert("failed to update your friend..");
				});
				return false;
			});
			//http://www.animeftw.tv/scripts.php?view=profile&subview=friendbutton&id=34846&add=after
		</script>';
		//echo '</ul>';
	}
	
	# function PopulateRow
	private function PopulateRow($id,$uid = NULL,$date,$type,$spriteMode,$d1 = NULL,$d2 = NULL,$d3 = NULL){
		if($uid == NULL)
		{ //uid is null, means it's global (aka an episode addition)
			$result = $this->Query("SELECT series.fullSeriesName, series.seoname, episode.id, episode.epnumber, episode.epname FROM series, episode WHERE episode.id=$d2 AND series.id=$d1");
			$row = mysql_fetch_array($result);
			$eimage = '';
			if($spriteMode == TRUE)
			{
				$startTag = '<li class="mainli" onclick="location.href=\'/anime/'.$row['seoname'].'/ep-'.$row['epnumber'].'\'">';
				$endTag = '</li>';
			}
			else
			{
				$startTag = '<div style="border-bottom:1px solid #e5e5e5;padding:5px 0 5px 0;" onclick="location.href=\'/anime/'.$row['seoname'].'/ep-'.$row['epnumber'].'\'">';
				$endTag = '</div>';
			}
			echo $startTag . '
				<div align="center"'.$eimage.'>
					Episode '.$row['epnumber'].' of '.$row['fullSeriesName'].' was added.<br /> 
					Titled: '.$row['epname'].'<br />
					<a href="/anime/'.$row['seoname'].'/ep-'.$row['epnumber'].'"><b>Watch this Episode Now</b></a>
				</div>
				<div class="notif_time">
					'.$this->BuildDate($date).'<img src="/images/new-icons/clock_new.png" width="12px" alt="" style="padding:0 3px 0 3px;" />
				</div>' . $endTag;
		}
		else
		{
			if($type == 1)
			{ //Friend request SENT, display the goodies				
				$result = $this->Query("SELECT users.ID, users.Username, users.avatarActivate, users.avatarExtension, friends.reqDate FROM users, friends WHERE friends.id = ".$d1." AND users.ID=friends.Asker");
				$row = mysql_fetch_array($result);
				if($row['avatarActivate'] == 'no')
				{
					$userimage = $this->Host . "/avatars/default.gif";
				}
				else
				{
					$userimage = $this->Host . "/avatars/user".$row['ID'].".".$row['avatarExtension'];
				}
				if($spriteMode == TRUE)
				{
					$startTag = '<li class="mainli">';
					$endTag = '</li>';
				}
				else
				{
					$startTag = '<div style="border-bottom:1px solid #e5e5e5;padding:5px 0 5px 0;">';
					$endTag = '</div>';
				}
				echo $startTag . '				
					<div align="left">
						<img src="'.$userimage.'" alt="" height="50px" style="padding:3px;" />
						<div class="inotif" align="left">
							' . $this->formatUsername($row['ID']) . ' added you as a friend.<br />
							<img src="' . $this->Host . '/new-icons/user_add.png" alt="" style="padding-right:5px;" width="14px" />
							<a href="/user/'.$row['Username'].'" class="add-friend-link" id="user-' . $row['ID'] . '">Add them as your Friend</a>
						</div>
					</div>
					<div class="notif_time">
						'.$this->BuildDate($date).'<img src="' . $this->Host . '/new-icons/clock_new.png" width="12px" alt="" style="padding:0 3px 0 3px;" />
					</div>' . $endTag;
			}
			else if($type == 2)
			{ // type == 2, post on profile, uid is for the user that is being posted on, and d1 is the comment id to map everything together
				// build the main query
				$result = $this->Query("SELECT users.ID, users.Username, users.avatarActivate, users.avatarExtension, page_comments.id AS cid, page_comments.comments FROM users, page_comments WHERE page_comments.id = ".$d1." AND users.ID=page_comments.uid");
				$row = mysql_fetch_array($result);
				//sub query.. for the username of the receiver..
				$result1 = $this->Query("SELECT Username FROM users WHERE ID = ".$uid);
				$row1 = mysql_fetch_array($result1);
				if($row['avatarActivate'] == 'no'){
					$userimage = $this->Host . "/avatars/default.gif";
				}
				else {
					$userimage = $this->Host . "/avatars/user".$row['ID'].".".$row['avatarExtension'];
				}
				$comment = $row['comments'];
				$comment = stripslashes($comment);
				if($spriteMode == TRUE)
				{
					$startTag = '<li class="mainli">';
					$endTag = '</li>';
					if(strlen($comment) <= 75){
						$comment = $comment;
					}
					else {
						$comment = substr($comment,0,73).'&hellip;';
					}
				}
				else
				{
					$startTag = '<div style="border-bottom:1px solid #e5e5e5;padding:5px 0 5px 0;">';
					$endTag = '</div>';
				}
				echo $startTag . '	
				<div align="left">
					<div style="display:inline-block;width:20%;" align="center">
						<img src="'.$userimage.'" alt="" width="50px" style="padding:10px 1px 1px 1px;" />
					</div>
					<div style="display:inline-block;width:79%;" class="inotif" align="left">
						Profile post by ' . $this->formatUsername($row['ID']) . '.
						<br /><a href="/user/'.$row1['Username'].'#'.$row['cid'].'"><i>'.$comment.'</i></a>
					</div>
				</div>
				<div class="notif_time">
					'.$this->BuildDate($date).'<img src="' . $this->Host . '/new-icons/clock_new.png" width="12px" alt="" style="padding:0 3px 0 3px;" />
				</div>' . $endTag;
			}
			else {
			}
		}
		//echo '<li></li>';
	}
	
	# function BuildDate
	private function BuildDate($date){
		$today = date('md',time());
		$var = date('md',$date);
		
		if($var == $today){ //aw yeah.. its today..
			$return = 'Today at '.date('h:ia',$date);
		}
		else { //Not yet, it's not today...
			if($var == ($today - 1)){ // yesterday..
				$return = 'Yesterday at '.date('h:ia',$date);
			}
			else if($var <= ($today - 6))
			{
				// dates later than a week..
				$return = date('M jS',$date).' at '.date('h:ia',$date);
			}
			else { //any other day
				$return = date('l',$date).' at '.date('h:ia',$date);
			}
		}
		return $return;
	}
	
	# function RecordView
	private function RecordView(){
		mysql_query("UPDATE users SET viewNotifications = '".time()."' WHERE ID = '".$this->UserArray[1]."'");
	}
}

?>