<?php
/****************************************************************\
## FileName: request.class.php									 
## Author: Hani Mayahi
## Edits by: Brad Riemann		 
## Usage: Handles the Requests system
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AnimeRequest extends Config{
	var $uid;
	var $maxvotes;
	var $votes;
	var $oldvotes;
	var $editmode;
	var $highlight;
	
	public function getRemainingVotes()
	{
		$result = mysql_query("SELECT
							 COUNT(request_votes.id) AS NumVotes
							FROM
							 requests, request_votes
							WHERE 
							 request_votes.voted_to=requests.id AND
							 requests.status < 3 AND 
							 request_votes.voted_by = ".$this->UserArray[1]."");
		return mysql_result($result, 0);
	}
	
	public function getOldVotes()
	{
		$result = mysql_query("SELECT
							 COUNT(request_votes.id) AS NumVotes
							FROM
							 requests, request_votes
							WHERE 
							 request_votes.voted_to=requests.id AND
							 requests.status < 3 AND 
							 request_votes.voted_by = ".$this->UserArray[1]."");
		return mysql_result($result, 0);
	}
	
	public function getMaxVotes()
	{
		return $this->maxvotes;
	}
	
	function setMaxVotes()
	{
		if($this->UserArray[2]==0){
			return 0;
		}else if($this->UserArray[2]==3){
			return 5;
		}else{
			return 10;
		}
	}
	function init()
	{
		$this->uid = $this->UserArray[1];
		$this->maxvotes = $this->setMaxVotes();
		$this->votes = $this->getRemainingVotes();
		$this->oldvotes = $this->getOldVotes();
		if($this->UserArray[2] == 1 || $this->UserArray[2] == 2 || $this->UserArray[2] == 6)
		{
			$this->editmode = isset($_GET["edit"]); //check for video techs
		}
		if((isset($_GET['highlight']) && is_numeric($_GET["highlight"]))){
			$this->highlight = $_GET['highlight'];
		}
		$this->style();
		echo '<div class="side-body-bg">
		<span class="scapmain">AnimeFTW.tv\'s Anime Requests</span>
		<br>
		<span class="poster">&nbsp;Request an Anime or vote below</span>
		</div>';
		$this->scripts();
		
		echo '
		<div id="dialog-form"></div>
		<div style="font-size: 11px">
		<a href="javascript:;" id="requestlink" >Request new anime</a><br />
		Votes available: '.($this->maxvotes-$this->votes).'</br>
		Current votes: '.$this->votes.' times<br>
		Previous votes: '.$this->oldvotes.'
		</div>
		<div class="container">
		<div class="heading">
			<div class="hcol" style = "width: 381px" align="left"><a href="?sort=name">Name</a></div>
			<div class="hcol" style = "width: 61px"><a href="?sort=votes">Votes</a></div>
			<div class="hcol" style = "width: 61px"><a href="?sort=status">Status</a></div>
			<div class="hcol" style = "width: 51px"><a href="?sort=type">Type</a></div>
			<div class="hcol" style = "width: 81px"><a href="?sort=episodes">Episodes</a></div>
			<div class="hcol" style = "width: 61px"><a href="?sort=anidb">AniDB</a></div>
			<div class="hcol" style = "width: 161px">Requested by</div>
			<div class="hcol" style = "width: 101px"><a href="?sort=date">Date</a></div>
		</div>';
		$sort = "requests.status, vote_count DESC";
		if(isset($_GET["sort"])){
			$s = $_GET["sort"];
			if($s=="votes"){
				$sort = "vote_count DESC";
			}else if($s=="requestedby"){
				$sort = "requests.userid";
			}else{
				$sort = "requests.".$s." DESC";
			}
		}
		$query = "SELECT requests.*, COUNT(voted_to) AS vote_count
			FROM requests LEFT JOIN request_votes
			ON requests.id = request_votes.voted_to
			GROUP BY requests.id
			ORDER BY $sort";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$i = 0;
		while(list($id, $name, $status, $type, $episodes, $anidb, $user_id, $date, $details) = mysql_fetch_array($result)) {
			$background_color = "";
			if($i%2==0){
				$background_color = "#fff";
			}else{
				$background_color = "#e8e8e8 ";
			}
			
			if($this->highlight==$id){
				$background_color = "#00CCFF";
			}
			echo'
			
			<div id = "reqinfo'.$id.'" class="reqinfo" align = "center" style="background-color: '.$background_color.'" name="request-'.$id.'">
				<div class = "table-row">
				<div class="col" style="width: 380px" align = "left"><a href="javascript:;">'.$name.'</a></div>
				';
				$result2 = mysql_query("SELECT count(*) from request_votes WHERE voted_to=$id");
				$rvotes = mysql_result($result2, 0);
				
				
				echo '
				<div class="col" style="width: 60px;">'.$rvotes.' <div id="reqlink'.$i.'" style="display:inline-block">';
				if($status<3){
					echo '(';
					echo '<a id="voteclick_'.$id.'" href="#">+</a>';
					if($rvotes>0){
						echo '<a id="votedeleteclick_'.$id.'" href="#"> -</a>';
					}
					echo ')';
				}
				echo '</div></div>
				<div class="col" style="width: 60px;">';
				$this->status($status, $id);
				
				echo '</div>
				<div class="col" style="width: 50px;">';
				switch($type){
					case 1:
						echo 'Series';
						break;
					case 2:
						echo 'OVA';
						break;
					case 3:
						echo 'Movie';
						break;
				}
				echo '</div>
				<div class="col" style="width: 80px;">'.$episodes.'</div>
				<div class="col" style="width: 60px;"><div id="areqlink'.$i.'" style="display:inline-block"><a href="http://anidb.net/perl-bin/animedb.pl?show=anime&aid='.$anidb.'">'.$anidb.'</a></div></div>
				<div class="col" style="width: 160px;"><div id="ureqlink'.$i.'" style="display:inline-block">'.$this->formatUsername($user_id).'</div></div>
				<div class="col" style="width: 100px;">'.date("Y-m-d", $date).'</div>
			</div>
			';
			$this->indScripts($id, $i, $name);
			
			echo'<div id="reqdetail'.$i.'" class = "reqdetail" style="background-color: '.$background_color.'">';
			
			if($this->editmode){
				echo '<div class="ardelete"><a id = "ardeletelink'.$i.'" href = "javascript:;">Delete entry</a></div>';
			}if($this->UserArray[2]==1||$this->UserArray[2]==2||$this->UserArray[2]==5){
				$extra = "";
				if($this->editmode){
					$extra = "margin-top: -10px;";
				}
				echo '<div class="ardelete" style="'.$extra.'"><a id = "arclaimlink'.$i.'" href = "javascript:;">Claim request!</a></div>';
			}
			
			echo $details.'</div>
			</div>';
			$i++;
		}
		echo "</div><br>";
		if($i==0){
			echo '<div class = "reqinfo" style="background-color: #fff;text-align: center;padding: 20px; width: 96%">Request an anime to display!</div>';
		}
	}
	
	// The object of this script is to hide all internal functions behind one public function, it makes things cleaner
	// and we can centralize in this class file (modular yo!)
	public function initFunctions()
	{
		// We need to build the form for requesting an anime.
		if(isset($_GET['mode']) && $_GET['mode'] == 'request-anime-vote-form')
		{
			// This is that form.
			$this->animeRequestForm();
		}
		//On votes
		else if(isset($_GET['mode']) && isset($_GET["id"]) && $_GET['mode'] == 'request-anime-vote')
		{
			$this->vote($_GET["id"]);
		}
		//This is when a manager updates the status. I'll make it more modular to make it possible to update other fields too.
		else if(isset($_GET['mode']) && $_GET['mode'] == 'manage' && (isset($_GET['status']) && is_numeric($_GET["status"])) && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->updateStatus($_GET["status"], $_GET["id"]);
		}
		//This is when a mod deletes an entry.
		else if(isset($_GET["mode"]) && $_GET["mode"]=="delete" && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->deleteEntry($_GET["id"]);
		}
		//This is for video techs, allowing them to claim a request.
		else if(isset($_GET["mode"]) && $_GET["mode"]=="claim" && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->claimEntry($_GET["id"]);
		}
		//Called when a user subtracts their vote.
		//Needs to be dumbproofed to only be able to subtract votes where the status is neither live nor denied (The votes are returned when they are at that status)
		else if(isset($_GET["mode"]) && $_GET["mode"]=="subvote" && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->subtractVote($_GET["id"]);
		}
		
		else if(isset($_GET["mode"]) && $_GET["mode"]=="add" && isset($_GET["name"]) &&isset($_GET["type"]) && (isset($_GET["episodes"]) && is_numeric($_GET["episodes"])) && (isset($_GET["anidb"]) && is_numeric($_GET["anidb"])) && isset($_GET["details"])){
			$this->addRequest($_GET["name"], $_GET["type"], $_GET["episodes"], $_GET["anidb"], $_GET["details"]);
		}
		else
		{
		}
		
		/*
		//This is called when a user presses the submit button.
		if(isset($_GET["requestanimename"]) && isset($_GET["requestanimetype"]) && isset($_GET["requestanimeepisodes"]) && isset($_GET["requestanimeanidb"]) && isset($_GET["requestanimedetails"]))
		{
			
		}*/
	}
	
	public function checkAmountOfVotes()
	{
		echo $this->getRemainingVotes() . '<br />';
		$this->maxvotes = $this->setMaxVotes();
		echo $this->maxvotes . '<br />';
		if($this->getRemainingVotes()<$this->setMaxVotes())
		{
			$rid = $_GET["requestanimevote"];
			$query = "INSERT INTO `request_votes` (`voted_by`, `voted_to`) VALUES (" . $this->UserArray[1] . ", " . mysql_real_escape_string($rid) . ")"; // Added the escape string, cant have people doing silly stuff..
			mysql_query($query) or die('Error : ' . mysql_error());
			return $this->getRemainingVotes(); // Return the remaining votes so we can be sure they dont double dip... this might be needed?
		}
		else
		{
			return 0;
		}
	}
	
	private function animeRequestForm()
	{
		echo '
			<div style="height:15px;">
				<div class="micro_form_results" style="display:none;"></div>
			</div>
			<form id="requestanimeform" method="get">
			<div class="container">
				<div class="table-row">
					<div class="col">Name: </div>
					<div class="col"><input type="text" name="requestanimename" id="requestanimename" /></div>
				</div>
				<div class="table-row">
					<div class="col">Type:</div>
					<div class="col">
					<select name="requestanimetype" id="requestanimetype">
						  <option value="1">Series</option>
						  <option value="2">OVA</option>
						  <option value="3">Movie</option>
						</select>
					</div>
				</div>
				<div class="table-row">
					<div class="col">Episodes:</div>
					<div class="col"><input type="number" name="requestanimeepisodes" id="requestanimeepisodes" /></div>
				</div>
				<div class="table-row">
					<div class="col">AniDB ID:</div>
					<div class="col"><input type="number" name="requestanimeanidb" id="requestanimeanidb" /></div>
				</div>
				<div class="table-row">
					<div class="col" style="vertical-align:top;">Details:</div>
					<div class="col"><textarea rows="8" cols="50" name="requestanimedetails" id="requestanimedetails" /></div>
				</div>
				<br>
			</div>
			</form>';
	}
	
	private function style()
	{
		echo '
		<style>
		.reqinfo{
			font-size: 11px;
			color: #777;
			float: left;
			width: 100%;
		}
		.reqdetail{
			display:none;
			float: left;
			padding: 10px;
			text-align: left;
			width: 98%
		}
		.container{
			display:table;
			width:100%;
			border-collapse: collapse;
			}
		 .heading{
			 display:table-row;
			 text-align: center;
			 line-height: 25px;
			 font-size: 14px;
			 border-width: 0px;
			 float: left;
			 
		 }
		 .table-row{  
			 display:table-row;
		 }
		 .col{ 
			display:table-cell;
			border-bottom: 0px;
			padding-top: 6px;
			padding-bottom: 6px;
		 }
		 .hcol{ 
			display:table-cell;
		 }
		 .requestanime{
			display: none;
			padding-top: 10px;
			padding-bottom: 10px;
		 }.ardelete{
			margin-top: -20px;
			float: right;
			text-align: right;
		 }

		</style>';
	}
	
	private function scripts()
	{
		echo '
		
		<script type="text/javascript" src="/scripts/jquery.form.js"></script>
		<script>';
		$this->changeStatusScript();
		echo '
		$(document).ready(function(){
			//$("#requestlink").click(function(){
			//	$("#request-anime").slideToggle("fast");
			//});
				
			$("#dialog-form").dialog({
				autoOpen: false,
				width: 500,
				show: {
					effect: "blind",
					duration: 1000
				},
				hide: {
					effect: "explode",
					duration: 1000
				},
				 buttons: {
					"Submit": function() {
						$(\'.micro_form_results\').slideUp();												
						$(\'label\').hide();
						var tripped = 0;
						var AnimeName = $("input#requestanimename").val();
						if (AnimeName == "") {
							 var styles = {
								border : "1px solid red",
								padding: "2px"
							};
							$("#requestanimename").css(styles);
							tripped = 1;
						}
						var Episodes = $("input#requestanimeepisodes").val();
						if (Episodes == "") {
							 var styles = {
								border : "1px solid red",
								padding: "2px"
							};
							$("#requestanimeepisodes").css(styles);
							tripped = 1;
						}
						var AniDB = $("input#requestanimeanidb").val();
						if (AniDB == "") {
							 var styles = {
								border : "1px solid red",
								padding: "2px"
							};
							$("#requestanimeanidb").css(styles);
							tripped = 1;
						}
						if(tripped == 1)
						{
							$(\'.micro_form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Please fill in the required fields.</div>");
							return false;
						}
						var type = $("select#requestanimetype").val();
						var details = $("textarea#requestanimedetails").val();
						$.ajax({
							type: "POST",
							url: "scripts.php?view=anime-requests&mode=add&name="+AnimeName+"&episodes="+Episodes+"&anidb="+AniDB+"&type="+type+"&details="+details,
							data: $(\'#requestanimeform\').serialize(),
							success: function(html) {
								var first = html.substr(0, 7);
								var second = html.substr(8);
								if(first == \'Success\'){
									// We need to close the dialog box
									$(\'.micro_form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#00FF00;padding:2px;\'>Success!</div>");
									
									window.location.href = "?highlight="+second+"#reqinfo"+second;
									// then give them a message saying it was successful, while refreshing the listing highlighting their entry.
								}
								else{
									// there was an error, dont close the form, make sure they are aware of the issue with a dropdown.
									$(\'.micro_form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Error: " + html + "</div>");
								}
							}
						});
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
			$("#requestlink").click(function() {
				$("#dialog-form").load("/scripts.php?view=anime-requests&mode=request-anime-vote-form");
				$("#dialog-form").dialog( "open" );
			});
			
			//$("#dialog-form").load("/scripts.php?view=anime-requests&mode=request-anime-vote-form");
			$("#requestanimeform").ajaxForm(function(data) { 
				if(data){
					alert("Error: "+data);
				}else{
					alert("Your request has been submitted!");
					location.reload();
				}
			});
			  
		});

		</script>';
	}
	private function indScripts($id, $i, $name){
		$extras = ', #status'.$i.', #ardeletelink'.$i.', #arclaimlink'.$i.'';
		echo '
		<script>
		$(document).ready(function(){
			$("#reqinfo'.$id.'").click(function(){
				$("#reqdetail'.$i.'").slideToggle("fast");
				$(".reqdetail:not(#reqdetail'.$i.')").slideUp("fast");
			});
			$("#reqlink'.$i.', #areqlink'.$i.', #ureqlink'.$i.''.$extras.'").click(function(e){
				e.stopPropagation();
			});
			$("#ardeletelink'.$i.'").click(function(e){
				if(confirm("Delete '.$name.'?")){
					$.ajax({
						url: "scripts.php?view=anime-requests&mode=delete&id='.$id.'",
						success: function(data){
							location.reload();
						 }
					});
				}
				else
				{
					e.preventDefault();
				}
			});
			$("#arclaimlink'.$i.'").click(function(e){
				$.ajax({
					url: "scripts.php?view=anime-requests&mode=claim&id='.$id.'",
					success: function(data){
						location.reload();
					 }
				});
			});
		});
		var maxvotes = '.$this->maxvotes.';
		var votes = '.$this->votes.';
		var m = maxvotes-votes;
		$("#voteclick_'.$id.'").click(function(){
			if(m<=0){
				alert("You have no more votes left.");
			}else{
				$.ajax({
					url: "scripts.php?view=anime-requests&mode=request-anime-vote&id='.$id.'",
					success: function(data){
						location.reload();
					 }
				});
			}
		});
		$("#votedeleteclick_'.$id.'").click(function(){
			$.ajax({
				url: "scripts.php?view=anime-requests&mode=subvote&id='.$id.'",
				success: function(data){
					location.reload();
				 }
			});
		});
		</script>';
	}
	private function status($status, $id){
		$msg = array("Pending", "Claimed", "Live", "Denied");
		if(!$this->editmode)
		{
			echo $msg[$status-1];
		}
		else
		{
			$selected = array("", "", "", "");
			$selstring = "selected = 'selected'";
			$selected[$status-1]=$selstring;
			echo '<select id = "status'.$id.'" name="status" onchange="changeStatus(this)">';
			for($i=0;$i<4;$i++){
				echo '<option value="'.($i+1).'" '.$selected[$i].'>'.$msg[$i].'</option>';
			}
				  
			echo '</select>';
		}
	}
	private function changeStatusScript()
	{
		if($this->editmode){
			echo 'function changeStatus(selected){
					var val = selected.value;
					var id = selected.id.replace("status", "");
					$.ajax({
						url: "scripts.php?view=anime-requests&mode=manage&status="+val+"&id="+id,
						success: function(data){
							location.reload();
						 }
					});
			}';
		}
	}
	
	private function vote($rid)
	{
		echo $this->getRemainingVotes() . '<br />';
		$this->maxvotes = $this->setMaxVotes();
		echo $this->maxvotes . '<br />';
		if($this->getRemainingVotes()<$this->maxvotes) //Check if it's possible to vote
		{
			$query = "INSERT INTO `request_votes` (`voted_by`, `voted_to`) VALUES (" . $this->UserArray[1] . ", " . mysql_real_escape_string($rid) . ")"; // Added the escape string, cant have people doing silly stuff..
			mysql_query($query) or die('Error : ' . mysql_error());
		}
	}
	
	private function updateStatus($status, $arid)
	{
		if($this->UserArray[2]==1 || $this->UserArray[2]==2 || $this->UserArray[2]==6)
		{
			$query = "UPDATE `requests` SET `status` = '" . mysql_real_escape_string($status) . "' WHERE `requests`.`id` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$this->ModRecord("Updated an Anime Request Status (ID: ".$arid.")."); // Make sure you log the action, to ensure if someone breaks everything we know who to blame.
		}
	}
	
	private function deleteEntry($arid)
	{
		if($this->UserArray[2]==1 || $this->UserArray[2]==2 || $this->UserArray[2]==6)
		{
			$query = "DELETE FROM `requests` WHERE `requests`.`id` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$query = "DELETE FROM `request_votes` WHERE `voted_to` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$this->ModRecord("Deleted an Anime Request (ID: ".$arid.")."); // Make sure you log the action, to ensure if someone breaks everything we know who to blame.
		}
	}
	
	private function claimEntry($arid)
	{
		if($this->UserArray[2]==1 || $this->UserArray[2]==2 || $this->UserArray[2]==5)
		{
			$arid = $_GET["id"];
			$query = "UPDATE `requests` SET `status` = '2' WHERE `requests`.`id` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$this->ModRecord("Marked an Anime Request (".$arid.") as Claimed."); // Make sure you log the action, to ensure if someone breaks everything we know who to blame.
		}
		
	}
	
	private function subtractVote($arid)
	{
		$query = "DELETE FROM `request_votes` WHERE `voted_to` = " . mysql_real_escape_string($arid) . " AND voted_by = " . $this->UserArray[1] . " LIMIT 1";
		mysql_query($query) or die('Error : ' . mysql_error());
	}
	
	private function addRequest($name, $type, $episodes, $anidb, $details){
		if(empty($name))
		{
			echo "The field 'Name' is empty";
			return;
		}
		if(empty($episodes))
		{
			echo "The field 'Episodes' is empty";
			return;
		}
		if(empty($anidb))
		{
			echo "The field 'AniDB ID' is empty";
			return;
		}
		if(empty($details))
		{
			echo "The field 'Details' is empty";
			return;
		}
		$query = "SELECT COUNT(`id`) FROM `requests` WHERE `anidb`=".$anidb.""; //check if it already exist
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$res = mysql_result($result, 0);
		//echo $res;
		
		// Check the results, if there are no rows, then we allow them to be added.
		if($res<=0)
		{
			$query = "INSERT INTO `requests` (`name`, `status`, `type`, `episodes`, `anidb`, `user_id`, `date`, `details`) VALUES ('" . mysql_real_escape_string($name) . "', 1, " . mysql_real_escape_string($type) . ", " . mysql_real_escape_string($episodes) . ", " . mysql_real_escape_string($anidb) . ", " . $this->UserArray[1] . ", " . time() . ", '" . mysql_real_escape_string($details) . "')";
			mysql_query($query) or die('Error : ' . mysql_error());
			echo "Success ".mysql_insert_id();
		}
		else
		{
			echo "Request already exist";
		}
	}
	
	
}
?>