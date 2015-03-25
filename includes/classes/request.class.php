<?php
/****************************************************************\
## FileName: request.class.php									 
## Author: Hani Mayahi
## Edits by: Brad Riemann		 
## Usage: Handles the Requests system
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/



//TODO:
//Search function
//Redirect Video Techs to the management board to claim a series(?)
class AnimeRequest extends Config{
	var $uid;
	var $maxvotes;
	var $votes;
	var $oldvotes;
	var $editmode;
	var $highlight;
	var $foundhighlight;
	var $page;
	var $max_pages;
	var $fid = 3; //forum id
	var $rpp = 25; //requests per page
	
	public function getRemainingVotes()
	{
		$result = mysql_query("SELECT
							 COUNT(request_votes.id) AS NumVotes
							FROM
							 requests, request_votes
							WHERE 
							 request_votes.voted_to=requests.id AND
							 requests.status = 1 AND 
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
							 requests.status > 1 AND 
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
	}function canEdit(){
		if($this->UserArray[2] == 1 || $this->UserArray[2] == 2 || $this->UserArray[2] == 6){
			return true;
		}else{
			return false;
		}
	}
	function init()
	{
		$amount = $this->SingleVarQuery("SELECT count(*) AS amount FROM requests", "amount");
		$this->max_pages = ceil($amount / $this->rpp);
		$this->uid = $this->UserArray[1];
		$this->maxvotes = $this->setMaxVotes();
		$this->votes = $this->getRemainingVotes();
		$this->oldvotes = $this->getOldVotes();
		if($this->canEdit()||$this->UserArray[2]==5)
		{
			$this->editmode = isset($_GET["edit"]);
		}
		if(isset($_GET['highlight']) && is_numeric($_GET["highlight"])){ 
			$this->highlight = $_GET['highlight'];
			
		}
		$this->style();
		echo '<div class="side-body-bg">
		<span class="scapmain">AnimeFTW.tv\'s Anime Requests</span>
		<br>
		<span class="poster">&nbsp;Request an Anime or vote below. For help, click <a href="/forums/anime-requests">here! (Change link to specific thread)</a></span>
		</div>';
		$this->scripts();
		
		echo '
		<div class="request-filter side-body-bg">
			<form action="requests" method="get">
				<div class="table-row">
					<div class="col">
						Series name:
					</div>
					<div class="col">';
						$value = "";
						if(isset($_GET["search"])&&$_GET["search"]=="Submit"){
							if(isset($_GET["name"])){
								$value = ' value="'.$_GET["name"].'"';
							}
							
						}
						echo '<input type="text" name="name"'.$value.'></input>';
					echo '
					</div>
				</div>
				<div class="table-row">
					<div class="col">
						Status:
					</div>
					<div class="col">
						';
						$value = 1;
						if(isset($_GET["search"])&&$_GET["search"]=="Submit"){
							if(isset($_GET["status"])){
								$value = $_GET["status"];
							}
							
						}
						$this->getSelect(array("----", "Pending", "Claimed", "Encoding", "Uploading", "Ongoing", "Stalled", "Done", "Live", "Denied"), $value, null, "status", false);
						echo '
						<a href = "javascript:;" id="statushelp" title="Click to view status meanings!">?</a>
						<div id="statushelp-content" title="Status Help">
						<b>Pending</b> - No video technician has started working on this series yet.<br><br>
						<b>Claimed</b> - A Video Technician has claimed this series, meaning they\'ll start working on the series soon.<br><br>
						<b>Encoding</b> - The series is currently being encoded.<br><br>
						<b>Uploading</b> - The series is currently being uploaded to our servers.<br><br>
						<b>Ongoing</b> - This series is currently airing, and is live on the website.<br><br>
						<b>Stalled</b> - The series is stalled for some reason.<br><br>
						<b>Done</b> - The series is done and should come up on the website anytime soon.<br><br>
						<b>Live</b> - The series is live on the site.<br><br>
						<b>Denied</b> - The series has been denied, view the comments for specific information.<br><br>
						View the comments for more information about a series, or ask a question yourself!
						</div>
					</div>
				</div>
				<div class="table-row">
					<div class="col">
						Type:
					</div>
					<div class="col">
						';
						$value = 1;
						if(isset($_GET["search"])&&$_GET["search"]=="Submit"){
							if(isset($_GET["type"])){
								$value = $_GET["type"];
							}
							
						}
						$this->getSelect(array("----", "Series", "OVA", "Movie"), $value, null, "type", false);
						echo '
					</div>
				</div>
				<div class="table-row">
					<div class="col">
						AniDB ID:
					</div>
					<div class="col">';
						$value = "";
						if(isset($_GET["search"])&&$_GET["search"]=="Submit"){
							if(isset($_GET["anidbid"])){
								$value = ' value="'.$_GET["anidbid"].'"';
							}
							
						}
						echo '<input type="text" name="anidbid"'.$value.'></input>';
					echo '
					</div>
				</div>
				<div class="table-row">
					<div class="col">
						Username:
					</div>
					<div class="col">';
						$value = "";
						if(isset($_GET["search"])&&$_GET["search"]=="Submit"){
							if(isset($_GET["username"])){
								$value = ' value="'.$_GET["username"].'"';
							}
							
						}
						echo '<input type="text" name="username"'.$value.'></input>';
					echo '
					</div>
				</div>
				<div class="table-row">
					<input name="search" type="submit" style="width: 60px;">
				</div>
			</form>
		</div>
				
			
		
		<div id="dialog-form" title="Anime Request"></div>
		<div style="font-size: 11px;float: left">
		<a href="javascript:;" id="requestlink" title="Press to open the request form!">Request new anime</a><br />
		Votes available: '.($this->maxvotes-$this->votes).'</br>
		Current votes: '.$this->votes.' times<br>
		Previous votes: '.$this->oldvotes.'
		</div>';
		if($this->canEdit()){
			if($this->editmode){
				$_GET["edit"] = null;
				echo '<span style="float: right"><a href="?'.http_build_query($_GET).'">Leave edit mode</a></span><br>';
				$_GET["edit"] = TRUE; //Put it back so the next queries have edit mode on if user's still on edit mode.
			}else{
				$_GET["edit"] = TRUE;
				echo '<span style="float: right"><a href="?'.http_build_query($_GET).'">Enter edit mode</a></span><br>';
				$_GET["edit"] = null; //Reset so it doesn't put this in the http build querys after this one.
			}
			
		}
		$originalsort = null;
		$originaldesc = null;
		if(isset($_GET["sort"])){
			$originalsort = $_GET["sort"];
		}if(isset($_GET["DESC"])){
			$originaldesc = $_GET["DESC"];
		}
		$_GET["sort"]="name";
		$temphighlight;
		if(isset($_GET["highlight"])){
			$temphighlight = $_GET["highlight"];
			$_GET["highlight"] = null; //Delete this from the get variables, so it doesn't get inlcuded in the links below.
		}
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;} //Check if the sort is the same one as the selected one, if so, then change to descending order.
		if($originaldesc==true){$_GET["DESC"]=null;}; //If desc was there from the start, then we want it to go back to ascending order
		$sn = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="votes";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$sv = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="status";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$ss = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="type";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$st = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="episodes";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$se = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="anidb";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$sa = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="requestedby";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$sr = http_build_query($_GET);
		$_GET["DESC"] = null;
		$_GET["sort"]="date";
		if($originalsort==$_GET["sort"]){$_GET["DESC"]=true;}
		if($originaldesc==true){$_GET["DESC"]=null;};
		$sd = http_build_query($_GET);
		$_GET["DESC"] = $originaldesc;
		$_GET["sort"] = $originalsort; //Reset so the next http_build_query puts this in where it's not supposed to
		if(isset($temphighlight)){
			$_GET["highlight"] = $temphighlight; //Put it back, so it can find the page.
		}
		echo '
		<div class="container">
		<div class="heading">
			<div class="hcol" style = "width: 381px" align="left"><a href="?'.$sn.'" title="Sort by name">Name</a></div>
			<div class="hcol" style = "width: 61px"><a href="?'.$sv.'" title="Sort by votes">Votes</a></div>
			<div class="hcol" style = "width: 61px"><a href="?'.$ss.'" title="Sort by status">Status</a></div>
			<div class="hcol" style = "width: 51px"><a href="?'.$st.'" title="Sort by type">Type</a></div>
			<div class="hcol" style = "width: 81px"><a href="?'.$se.'" title="Sort by episodes">Episodes</a></div>
			<div class="hcol" style = "width: 61px"><a href="?'.$sa.'" title="Sort by AniDB">AniDB</a></div>
			<div class="hcol" style = "width: 161px"><a href="?'.$sr.'" title="Sort by username">Requested by</a></div>
			<div class="hcol" style = "width: 101px"><a href="?'.$sd.'" title="Sort by date">Date</a></div>
		</div>';
		$sort = "user_requests.status, vote_count DESC";
		if(isset($_GET["sort"])){
			$s = $_GET["sort"];
			if($s=="votes"){
				$sort = "vote_count ";
			}else if($s=="requestedby"){
				$sort = "user_requests.Username ";
			}else{
				$sort = "user_requests.".$s." ";
			}
			if(isset($_GET["DESC"])){
				$sort = $sort."DESC";
			}
		}
		$this->page = 0;
		if(isset($_GET["page"])){
			$this->page = $_GET["page"];
		}else{
			$this->page = 1;
		}
		$sname = "";
		$searchquery = "";
		if(isset($_GET["search"])&&$_GET["search"]=="Submit"){ 
			$sname = $_GET["name"];
			$sstatus = $_GET["status"];
			$sstatus = $sstatus-1;
			$stype = $_GET["type"];
			$stype = $stype-1;
			$sanidb = $_GET["anidbid"];
			$susername = $_GET["username"];
			if(!empty($sname)||$sstatus>0||$stype>0||!empty($sanidb)||!empty($susername)){
				$searchquery = "WHERE ";
			}
			if(!empty($sname)){
				$searchquery = $searchquery."user_requests.name LIKE '%".$sname."%' ";
				if($sstatus>0||$stype>0||!empty($sanidb)||!empty($susername)){ //If any of the fields after this is not empty, then we put a "AND " in the query.
					$searchquery = $searchquery."AND ";
				}
			}
			if($sstatus>0){
				$searchquery = $searchquery."user_requests.status ='".$sstatus."' ";
				if($stype>0||!empty($sanidb)||!empty($susername)){ //If any of the fields after this is not empty, then we put a "AND " in the query.
					$searchquery = $searchquery."AND ";
				}
				
			}if($stype>0){
				$searchquery = $searchquery."user_requests.type ='".$stype."' ";
				if(!empty($sanidb)||!empty($susername)){ //If any of the fields after this is not empty, then we put a "AND " in the query.
					$searchquery = $searchquery."AND ";
				}
			}if(!empty($sanidb)){
				$searchquery = $searchquery."user_requests.anidb = '".$sanidb."' ";
				if(!empty($susername)){ //If any of the fields after this is not empty, then we put a "AND " in the query.
					$searchquery = $searchquery."AND ";
				}
			}if(!empty($susername)){
				$searchquery = $searchquery."user_requests.Username LIKE '%".$susername."%' ";
			}
		}
		$query = "SELECT user_requests.*, COUNT(voted_to) AS vote_count
			FROM user_requests
			LEFT JOIN request_votes
			ON user_requests.id = request_votes.voted_to
			".$searchquery."
			GROUP BY user_requests.id
			ORDER BY $sort
			LIMIT ".($this->page-1)*$this->rpp.", ".$this->rpp; //page-1 because we want page 1 to be the first.
		//echo $query."<br>";
		$result = mysql_query($query) or die('Error : ' . mysql_error());
		$i = 0;
		while(list($username, $id, $name, $status, $type, $episodes, $anidb, $user_id, $date, $description, $details, $tid, $uid) = mysql_fetch_array($result)) { //$uid: upload board id.
			//echo $uid;
			$uploadstatus = null;
			if($uid&&$status!=9){ //If someone denies this request, then we no longer follow the uploads board status for it.
				$uploadstatus = $this->SingleVarQuery("SELECT status FROM uestatus WHERE ID=".$uid."", "status");
				$uploadstatus = $this->getStatusNum(ucfirst($uploadstatus));
				if($status!=$uploadstatus){
					//$status in the request database is not the same as the upload boards database.
					$this->SingleVarQuery("UPDATE requests SET status=".$uploadstatus." WHERE id=".$id."", "");
					$status = $uploadstatus; //Change it on this current run too.
				}
			}
			
			$background_color = "";
			if($i%2==0){
				$background_color = "#fff";
			}else{
				$background_color = "#e8e8e8 ";
			}
			
			if($this->highlight==$id){
				$background_color = "#00CCFF";
				$this->foundhighlight = true;
				$_GET["highlight"] = NULL; //Remove it from the get variables, so it doesn't get in the http_build_query function if the user changes page.
				//User wouldn't be able to change page since it would try to find the highlighted anime.
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
				if($status==1){ //Vote available only if the request is pending
					if($this->maxvotes-$this->votes>0||$rvotes>0)
						echo '(';
					if($this->maxvotes-$this->votes>0)
						echo '<a id="voteclick_'.$id.'" href="javascript:;">+</a>';
					if($rvotes>0){
						echo '<a id="votedeleteclick_'.$id.'" href="javascript:;">-</a>';
					}
					if($this->maxvotes-$this->votes>0||$rvotes>0)
						echo ')';
				}
				echo '</div></div>
				<div class="col" style="width: 60px;">';
				$this->selectField(array("Pending", "Claimed", "Encoding", "Uploading", "Ongoing", "Stalled", "Done", "Live", "Denied"), $status, $id, "status", "changestatus");
				
				echo '</div>
				<div class="col" style="width: 50px;">';
				$this->selectField(array("Series", "OVA", "Movie"), $type, $id, "type", "changetype");
				echo '</div>
				<div class="col" style="width: 80px;">';
				if($episodes==0){echo '?';}else{echo $episodes;};
				echo '</div>
				<div class="col" style="width: 60px;"><div id="areqlink'.$i.'" style="display:inline-block"><a href="http://anidb.net/perl-bin/animedb.pl?show=anime&aid='.$anidb.'">'.$anidb.'</a></div></div>
				<div class="col" style="width: 160px;"><div id="ureqlink'.$i.'" style="display:inline-block">'.$this->formatUsername($user_id).'</div></div>
				<div class="col" style="width: 100px;">'.date("Y-m-d", $date).'</div>
			</div>
			';
			$this->indScripts($id, $i, $name);
			
			echo'<div id="reqdetail'.$i.'" class = "reqdetail" style="background-color: '.$background_color.'">';
			
			if($this->editmode){
				echo '<div class="ardelete"><a id = "ardeletelink'.$i.'" href = "javascript:;">Delete entry</a></div>';
				$extras = " id='uploadsentry".$id."' onchange='changeuploadsentry(this)'";
				echo $this->uploadsEntrySelect($uid, $extras)."<br>";
			}if($this->UserArray[2]==1||$this->UserArray[2]==2||$this->UserArray[2]==5){
				$extra = "";
				if($this->editmode){
					$extra = "margin-top: -10px;";
				}
				if($status==1){
					echo '<div class="ardelete" style="'.$extra.'"><a id = "arclaimlink'.$i.'" href = "javascript:;">Claim request!</a></div>';
				}
			}
			
			$tid = $this->SingleVarQuery("SELECT tid FROM requests WHERE id=".$id, "tid");
			$replies = $this->SingleVarQuery("SELECT count(pid) FROM forums_post WHERE ptid=".$tid, "count(pid)");
			$replies--; //subtract one reply becuase the first post is not a reply
			
			echo $details.'<br><br>
			<a href="/forums/anime-requests/topic-'.$tid.'" id="commentslink'.$i.'">Comments('.$replies.')</a>
			</div>
			</div>';
			$i++;
		}
		if(isset($_GET['highlight']) && is_numeric($_GET["highlight"])){ 
			$newurl = "";
			if(!isset($_GET["page"])){
				$_GET["page"]=1;
			}
			if($_GET["page"]<=$this->max_pages){
				$_GET["page"] = $this->page+1;
			}
			if(!$this->foundhighlight){
				if($_GET["page"]<=$this->max_pages){
					header('Location: requests?'.http_build_query($_GET).'#reqinfo'.$_GET["highlight"]);
				}
			}
			
		}
		
		
		
		$this->PrintPages();

		echo "</div><br>";
		$messg = "";
		if($i==0&&!isset($_GET["search"])){
			$messg = 'Request an anime to display!';
		}else if($i==0&&isset($_GET["search"])&&$_GET["search"]=="Submit"){
			$messg = 'No anime was found!';
		}
		if(!empty($messg)){
			echo '<div class = "reqinfo" style="background-color: #fff;text-align: center;padding: 20px; width: 96%">'.$messg.'</div>';
		}
	}
	
	// The object of this script is to hide all internal functions behind one public function, it makes things cleaner
	// and we can centralize in this class file (modular yo!)
	public function initFunctions()
	{
		$status = null;
		if($this->UserArray[2]==0){
			echo 'Please login to request an anime.';
			return;
		}
		if(isset($_GET["id"]) && is_numeric($_GET["id"])){
			$status = $this->SingleVarQuery("SELECT status FROM requests WHERE id=".$_GET["id"], "status"); 
		}
		
		// We need to build the form for requesting an anime.
		if(isset($_GET['mode']) && $_GET['mode'] == 'request-anime-vote-form')
		{
			// This is that form.
			$this->animeRequestForm();
		}
		//On votes
		else if(isset($_GET['mode']) && isset($_GET["id"]) && $_GET['mode'] == 'request-anime-vote')
		{
			if($status==1){ //dumbproofing, voting shouldn't be possible for any series if it isn't pending.
				$this->vote($_GET["id"]);
			}
		}
		//This is when a manager updates the status.
		else if(isset($_GET['mode']) && $_GET['mode'] == 'manage' && (isset($_GET['status']) && is_numeric($_GET["status"])) && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->changeRequestValue($_GET["status"], $_GET["id"], "status");
		}
		//This is when a manager updates the type.
		else if(isset($_GET['mode']) && $_GET['mode'] == 'manage' && (isset($_GET['type']) && is_numeric($_GET["type"])) && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->changeRequestValue($_GET["type"], $_GET["id"], "type");
		}
		//This is when a manager edits the entry board.
		else if(isset($_GET['mode']) && $_GET['mode'] == 'manage' && (isset($_GET['uploadsentry']) && is_numeric($_GET["uploadsentry"])) && (isset($_GET["id"]) && is_numeric($_GET["id"])))
		{
			$this->changeRequestValue($_GET["uploadsentry"], $_GET["id"], "uid");
		}
		//This is when a mod deletes an entry.
		else if(isset($_GET["mode"]) && $_GET["mode"]=="delete" && (isset($_GET["id"]) && is_numeric($_GET["id"])) && isset($_GET["reason"]))
		{
			$this->deleteEntry($_GET["id"], $_GET["reason"]);
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
			if($status==1){ //dumbproofing, users shouldn't be able to subtract votes on series that aren't pending.
				$this->subtractVote($_GET["id"]);
			}
		}
		
		else if(isset($_GET["mode"]) && $_GET["mode"]=="add" && (isset($_GET["anidb"]) && is_numeric($_GET["anidb"])) && isset($_GET["details"])){
			
			include("includes/classes/anidb.class.php");
			$AniDB  = new AniDB();
			$AID = $_GET["anidb"];
			$name = $AniDB->getName("en",$AID);
			$episodes = $AniDB->getEpisodeCount($AID);
			$description = $AniDB->getDescription($AID);
			$type = $AniDB->getSeriesType($AID);
			if($type=="TV Series"||$type=="Web"){
				$type = 1;
			}else if($type=="OVA"||$type=="TV Special"){
				$type = 2;
			}else if($type=="Movie"){
				$type = 3;
			}else if($type=="Music Video"){
				echo "Error: Submitted request is a music video.";
				return;
			}
			$this->addRequest($name, $type, $episodes, $AID, $description, $_GET["details"]);
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
	private function PrintPages(){
		
		echo $this->max_pages.' pages ';
		$lvp = $this->page-4;//lowest visible page
		$hvp = $this->page+4;//highest visible page
		if($lvp<=0){
			$lvp = 1;
		}if($hvp>$this->max_pages){
			$hvp = $this->max_pages;
		}
		
		if($lvp>1){  //Puts the '<' to the paging
			$_GET['page'] = $this->page-1;
			echo '<a href="?'.http_build_query($_GET).'">&lt;</a>&nbsp;';
		}
		for($i = $lvp; $i<=$hvp;$i++){
			if($i==$this->page){
				echo '<span style="font-weight: bold">'.$i.'</span>';
			}else{
				$_GET['page'] = $i;
				echo '<a href="?'.http_build_query($_GET).'">'.$i.'</a>';
			}
			if($i<$hvp){
				echo ', ';
			}
		}
		if($hvp<$this->max_pages){ //Puts the '>' to the paging
			$_GET['page'] = $this->page+1;
			echo '&nbsp;<a href="?'.http_build_query($_GET).'">&gt;</a>';
		}
		
		//This can be implemented later on.
		/*echo '<span style="float: right;">Entries per page: <select id="entriesperpage">
				<option value="10">10</option>
				<option value="25" selected="selected">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select></span>';*/
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
			<script type="text/javascript">
			$("#anidbhelp").dialog({
				autoOpen: false,
				resizable: false,
				width: 300,
				height: 130
			});
			$("#requestanimedetails").redactor({
				minHeight: 200, 
				maxHeight: 200
			});
			$("#anidbhelplink").click(function() {
				$("#anidbhelp").dialog( "open" );
			});
			</script>
			<div style="height:15px;">
				<div class="micro_form_results" style="display:none"></div>
			</div>
			<form id="requestanimeform" method="get">
				<div class="table-row">
					<div class="col">AniDB ID:</div>
					<div class="col"><input type="number" name="requestanimeanidb" id="requestanimeanidb" /> <a href="javascript:;" id="anidbhelplink">?</a></div>
					<div id="anidbhelp" title="AniDB Help">Go to <a href="http://www.anidb.net" target="_blank">AniDB</a> and search for the anime you\'re trying to request. Look for the AniDB ID, which is usually next to the name in
					a paranthesis like (<a href="http://www.anidb.net/a4575" target="_blank">a4575</a>). Write <span style="text-decoration: underline">ONLY</span> the numbers.</div>
				</div>
				<div class="table-row">
					<div class="col" style="vertical-align:top;">Details:</div>
					<div class="col"><textarea rows="10" cols="50" name="requestanimedetails" id="requestanimedetails" style="resize: none;"></textarea></div>
				</div>
				<br>
			</form>';
	}
	
	private function style() //Should be put in css file?
	{
		echo '
		<style>
		.reqinfo{
			font-size: 11px;
			color: #777;
			float: left;
			width: 100%;
			padding: 6px 0px;
			border-top: 1px solid rgb(168, 168, 168);
			border-bottom: 1px solid rgb(168, 168, 168);
			margin-top: -1px;
			font-size: 1.2em;
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
			padding: 6px 0px;
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
		 }.ui-state-default .ui-icon {
			background-image: url("/css/images/ui-icons_888888_256x240.png");
		}.ui-widget-content a{
			color: #007fc8;
			text-decoration: none;
		}.request-filter{
			padding: 20px;
			width: 600px;
			margin: 0 auto;
			margin-bottom: 30px;
		}.request-filter input{
			width: 470px;
		}.request-filter .col{
			padding-right: 10px;
		}
		
		</style>';
	}
	
	private function scripts()
	{
		
		echo '
		
		<script>';
		$this->editScripts(array("status", "type", "uploadsentry"));
		echo '
		$(document).ready(function(){
			//$("#requestlink").click(function(){
			//	$("#request-anime").slideToggle("fast");
			//});
			$("#statushelp-content").dialog({
				autoOpen: false,
				resizable: false,
				width: 500,
				height: 300
			});
			$("#statushelp").click(function() {
				$("#statushelp-content").dialog( "open" );
			});
			$("#dialog-form").dialog({
				autoOpen: false,
				resizable: false,
				width: 570,
				height: 450,
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
						var details = $("textarea#requestanimedetails").val().replace(/(?:\r\n|\r|\n)/g, \'<br />\'); //replace new lines to <br>
						$.ajax({
							type: "POST",
							url: "scripts.php?view=anime-requests&mode=add&anidb="+AniDB+"&details="+details,
							data: $(\'#requestanimeform\').serialize(),
							success: function(html) {
								var first = html.substr(0, 7);
								var second = html.substr(8);
								if(first == \'Success\'){
									// We need to close the dialog box
									$(\'.micro_form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#00FF00;padding:2px;\'>Success!</div>");
									
									window.location.href = "?highlight="+second;
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
		$extras = ',#commentslink'.$i.', #status'.$i.', #ardeletelink'.$i.', #arclaimlink'.$i.', #uploadsentry'.$id.'';
		echo '
		<script>
		$(document).ready(function(){
			$("#reqinfo'.$id.'").click(function(){
				$("#reqdetail'.$i.'").slideToggle("fast");
				$(".reqdetail:not(#reqdetail'.$i.')").slideUp("fast");
			});
			$("#reqlink'.$i.', #areqlink'.$i.', #ureqlink'.$i.''.$extras.'").click(function(e){
				e.stopPropagation();
			});';
			if(isset($_GET["edit"])){ //If in edit mode
				if($this->canEdit()){ //If they have permission to delete
					echo '
						$("#ardeletelink'.$i.'").click(function(e){
							var reason = prompt("Why do you want to delete '.$name.'?");
							if(reason!=null){
								$.ajax({
									url: "scripts.php?view=anime-requests&mode=delete&id='.$id.'&reason="+reason,
									success: function(data){
										location.reload();
									 }
								});
							}
							else
							{
								e.preventDefault();
							}
						});';
				}
			}
			
			if($this->canEdit()){
				//Rediraction to management is not possible in it's current state.
				echo '
				$("#arclaimlink'.$i.'").click(function(e){
					$.ajax({
						url: "scripts.php?view=anime-requests&mode=claim&id='.$id.'",
						success: function(data){
							location.reload();
						 }
					});
				});';
			}
		echo '
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
	private function getStatusNum($status){
		$msg = array("Pending", "Claimed", "Encoding", "Uploading", "Ongoing", "Stalled", "Done", "Live", "Denied");
		$index = array_search($status, $msg);
		
		return $index+1; //+1 because "Pending" is status 1 in this class.
	}
	public function getStatus($status){
		$msg = array("Pending", "Claimed", "Encoding", "Uploading", "Ongoing", "Stalled", "Done", "Live", "Denied");
		return $msg[$status-1];
	}
	
	//This function is kind of a mess atm
	//$msg: Array list of option field.
	//$status: id of default selected option.
	//$id: gives the select field a unique id, if there's more than one with the $option name.
	//$option: name of id
	//$jsfunction: function name only
	private function getSelect($msg, $status, $id, $option, $jsfunction){
		$selected = array_fill(0, count($msg), "");
		$selstring = "selected = 'selected'";
		$selected[$status-1]=$selstring;
		$jsstring = "";
		if($jsfunction){
			$jsstring = 'onchange="'.$jsfunction.'(this)"';
		}
		echo '<select id = "'.$option.''.$id.'" name="'.$option.'" '.$jsstring.'>';
		for($i=0;$i<count($msg);$i++){
			echo '<option value="'.($i+1).'" '.$selected[$i].'>'.$msg[$i].'</option>';
		}
			  
		echo '</select>';
	}
	private function selectField($msg, $status, $id, $option, $jsfunction){
		if(!$this->editmode)
		{
			echo $msg[$status-1];
		}
		else
		{
			$this->getSelect($msg, $status, $id, $option, $jsfunction);
		}
	}
	private function editScripts($values)
	{
		if($this->editmode){
			foreach($values as $i){
			echo 'function change'.$i.'(selected){
					var val = selected.value;
					var id = selected.id.replace("'.$i.'", "");
					$.ajax({
						url: "scripts.php?view=anime-requests&mode=manage&'.$i.'="+val+"&id="+id,
						success: function(data){
							location.reload();
						 }
					});
				}';
			}
				
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
	
	private function changeRequestValue($value, $arid, $option)
	{
		if($this->UserArray[2]==1 || $this->UserArray[2]==2 || $this->UserArray[2]==6)
		{
			$query = "UPDATE `requests` SET `".$option."` = '" . mysql_real_escape_string($value) . "' WHERE `requests`.`id` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$this->ModRecord("Updated an Anime Request ".$option." (ID: ".$arid.")."); // Make sure you log the action, to ensure if someone breaks everything we know who to blame.
		}
	}
	
	private function deleteEntry($arid, $reason)
	{
		if($this->UserArray[2]==1 || $this->UserArray[2]==2 || $this->UserArray[2]==6)
		{
			//Forum posting
			$userIp = @$_SERVER['REMOTE_ADDR'];
			$date = time();
			$tid = $this->SingleVarQuery("SELECT tid FROM requests WHERE id=".$arid, "tid");
			$query2 = mysql_query("SELECT pid FROM forums_post WHERE ptid='$tid'"); 
			$total_thread_posts = mysql_num_rows($query2) or die("Error: ". mysql_error(). " with query ". $query2);
			$new_post_id = $total_thread_posts+1;
			$query = sprintf("INSERT INTO forums_post (ptid, puid, pfid, ptitle, pdate, pbody, ptispost, pip) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($tid),
				mysql_real_escape_string($this->UserArray[1]),
				mysql_real_escape_string($this->fid),
				mysql_real_escape_string(null),
				mysql_real_escape_string($date),
				mysql_real_escape_string("Request has been deleted: ".$reason),
				mysql_real_escape_string($new_post_id),
				mysql_real_escape_string($userIp));
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			$query = 'UPDATE forums_threads SET tupdated=\'' . mysql_escape_string($date) . '\'WHERE tid=' . $tid . '';
			mysql_query($query) or die('Error : ' . mysql_error());
			$query = 'UPDATE forums_threads SET tclosed=\'1\' WHERE tid=' . $tid . '';
			mysql_query($query) or die('Error : ' . mysql_error());
			
			//Request deletion
			$query = "DELETE FROM `requests` WHERE `requests`.`id` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$query = "DELETE FROM `request_votes` WHERE `voted_to` = " . mysql_real_escape_string($arid);
			mysql_query($query) or die('Error : ' . mysql_error());
			$this->ModRecord("Deleted an Anime Request (ID: ".$arid.", reason: ".$reason.")."); // Make sure you log the action, to ensure if someone breaks everything we know who to blame.
			
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
	
	private function addRequest($name, $type, $episodes, $anidb, $description, $details){
		if(empty($name))
		{
			echo "The field 'Name' is empty";
			return;
		}
		if(intval($episodes)<0&&!is_numeric($episodes))
		{
			echo "The field 'Episodes' is empty";
			return;
		}
		if(empty($anidb))
		{
			echo "The field 'AniDB ID' is empty";
			return;
		}if(!is_numeric($anidb)){
			echo "The field 'AniDB ID' must be numeric";
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
			$submittitle = "Anime Request: ".$name;
			$date = time();
			$query = sprintf("INSERT INTO forums_threads (ttitle, tpid, tfid, tdate, tupdated) VALUES ('%s', '%s', '%s', '%s', '%s')",
			mysql_real_escape_string($submittitle),
			mysql_real_escape_string($this->UserArray[1]),
			mysql_real_escape_string($this->fid),
			mysql_real_escape_string($date),
			mysql_real_escape_string($date));
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			
			$query006 = "SELECT tid FROM forums_threads WHERE tdate='$date'";
			$result006 = mysql_query($query006) or die('Error : ' . mysql_error());
			$row006 = mysql_fetch_array($result006);
			$ptid3 = $row006['tid'];
			$pistopic = 1;
			$userIp = @$_SERVER['REMOTE_ADDR'];
			$query = sprintf("INSERT INTO `requests` (`name`, `status`, `type`, `episodes`, `anidb`, `user_id`, `date`, `description`,`details`, `tid`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($name),
				mysql_real_escape_string("1"),
				mysql_real_escape_string($type),
				mysql_real_escape_string($episodes),
				mysql_real_escape_string($anidb),
				mysql_real_escape_string($this->UserArray[1]),
				mysql_real_escape_string(time()),
				mysql_real_escape_string($description),
				mysql_real_escape_string($details),
				mysql_real_escape_string($ptid3));
			mysql_query($query) or die('Error : ' . mysql_error());
			$reqid = mysql_insert_id();
			echo "Success ".$reqid;
			$details = "[animerequest]".$reqid."[/animerequest]"; //We change the details on the forum to the dynamic version
			$query2 = sprintf("INSERT INTO forums_post (ptid, puid, pfid, ptitle, pdate, pbody, pistopic, pip) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				mysql_real_escape_string($ptid3),
				mysql_real_escape_string($this->UserArray[1]),
				mysql_real_escape_string($this->fid),
				mysql_real_escape_string($submittitle),
				mysql_real_escape_string($date),
				mysql_real_escape_string($details),
				mysql_real_escape_string($pistopic),
				mysql_real_escape_string($userIp));
			mysql_query($query2) or die('Could not connect, way to go retard:' . mysql_error());
		}
		else
		{
			echo "Request already exist";
		}
	}
	
	
}
?>