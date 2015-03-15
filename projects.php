<?php
include('init.php');

if (isset($_COOKIE["LastVisit"])) {
    If (isset($_COOKIE["CurrentVisit"])) {
        setcookie("LastVisit",$_COOKIE["CurrentVisit"],time()+60*60*24*365,"/");
	setcookie("CurrentVisit", time(),time()+60*60*24*365,"/");
    } else {
	setcookie("CurrentVisit", time(),time()+60*60*24*365,"/");
    }
} else {
    setcookie("LastVisit", time(),time()+60*60*24*365,"/");
    setcookie("CurrentVisit", time(),time()+60*60*24*365,"/");
}
$lv = $_COOKIE["LastVisit"];
$cv = $_COOKIE["CurrentVisit"];

$PageTitle = 'Uploader Projects - AnimeFTW.TV';
error_reporting(E_ALL & ~E_NOTICE);
include('header.php');
include('header-nav.php');
//$index_global_message = "Welcome to the new index.php page!";
	// Start Main BG
    echo "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."'>\n<tr>\n";
	echo "<td width='".THEME_WIDTH."' class='main-bg'>\n";
	// End Main BG
   if(isset($index_global_message)){
    echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
	echo "<td class='note-message' align='center'>".$index_global_message."</td>\n";
	echo "</tr>\n</table>\n";
	echo "<br />\n<br />\n";
	}
	?>
    <div align="center">
<? if($profileArray[2] == 0) {
	echo '<h2>Please log in using the button to the top and you will be re-directed back here, and allowed to add your series as well as edit them.</h2>';
}
else {}
if($profileArray[2] == 0 || $profileArray[2] == 3){
	echo '<h2>This is a restricted Staff Zone, please click back and continue to watch your anime.</h2>';
}
else {
?>
<div class="side-body-bg">
<span class="scapmain">Uploads Board</span>
</div>
<br />
<div>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td colspan="8"><br />* = Denotes a status that a given User can only have five(5) series going before they need to finish their work on a given series in order to gain more claims/encodes/uploads.<br /><br />
        <div align="center"><strong>***** Update: A new option has been added, from now on, we ask that all uploaders please find the anidb ID number for their perspective series. If you look at the AniDB URL it is usually at the end after aid=.... by having this inputted into our system it makes everything run faster!******</strong></div></td>
    </tr>
    <tr>
      <td colspan="8" class="sectiontitle"><div align="left">Claimed*</div></td>
      <td class="sectiontitle"><div align="right"><? if($profileArray[2] != 0 && $profileArray[2] != 3 && $profileArray[2] != 7){echo "<a onClick=\" document.addseries.uestatus.value = 'claimed'; document.getElementById('Layer1').style.visibility = 'visible';\" style='cursor:pointer;'>Add</a>";} else {echo "Add";}?></div></td>
    </tr>
<tr>
      <td width="20%" height="34" class="sectionheaderleft"><a href="?sort=series">Series</a></td>
      <td width="20%" height="34" class="sectionheadercenter"><a href="?sort=prefix">Prefix</a></td>
      <td width="6%" height="34" class="sectionheadercenter"><a href="?sort=episodes">Eps</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=resolution">Res</a></td>
      <td width="12%" height="34" class="sectionheadercenter"><a href="?sort=type">Type</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=updated">Updated</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=user">User</a></td>
      <td width="8%" height="34" class="sectionheadercenter">ADB Link</td>
      <td width="4%" height="34" class="sectionheaderright">Edit</td>
    </tr>
<?php
while($row=mysql_fetch_array($claimedresult)){
if ($actioned == $row[ID]) { echo"<tr bgcolor='#3C476F'>";} else { echo"<tr>"; }
      echo"<td width='20%' class='sectioncontentsleft'>$row[series]</td>";
      echo"<td width='20%' class='sectioncontentscenter'>$row[prefix]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[episodes]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[resolution]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[type]</td>";
      if (strtotime($row[updated]) > $lv) { echo"<td width='12%' bgcolor=\"#10AEE1\" style=\"color:#fff;\" class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>"; } else { echo"<td width='12%' class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>";}
      echo"<td width='20%' class='sectioncontentscenter'>";
	  if($profileArray[2] ==1 || $profileArray[2] ==2)
	  {
		  echo '<a title="'.checkServer($row[user]).'">'.checkUserNameNumberNoLink($row[user]).'</a>';
	  }
	  else {
		  echo checkUserNameNumberNoLink($row[user]);
	  }
	  echo "</td>\n";
      echo"<td width='12%' class='sectioncontentscenter2'>".showAniDBLink($row[anidbsid])."</td>";
      if($profileArray[1] == $row[user] || $profileArray[2] ==1 || $profileArray[2] ==2){
      echo"<td width='4%' class='sectioncontentsright'><div class='editcol' align='center'><a onclick=\" frames['edit'].location.href='/projects_edit.php?id=$row[ID]';document.getElementById('Layer2').style.visibility = 'visible';\" style='cursor:pointer;'>&nbsp;&nbsp;X&nbsp;&nbsp;</a></div></td>";
	  }
	  else {
		  echo "<td width='4%' class='sectioncontentsright'>&nbsp;</td>";
	  }
    echo"</tr>";
$updated="";
}
?>
    <tr>
      <td colspan="9"  height="0" class="sectionbottom">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="8" class="sectiontitle"><div align="left">Encoding*</div></td>
      <td class="sectiontitle"><div align="right"><? if($profileArray[2] != 0 && $profileArray[2] != 3 && $profileArray[2] != 7){echo "<a onClick=\" document.addseries.uestatus.value = 'encoding'; document.getElementById('Layer1').style.visibility = 'visible';\" style='cursor:pointer;'>Add</a>";} else {echo "Add";}?></div></td>
    </tr>


<tr>
      <td width="20%" height="34" class="sectionheaderleft"><a href="?sort=series">Series</a></td>
      <td width="20%" height="34" class="sectionheadercenter"><a href="?sort=prefix">Prefix</a></td>
      <td width="6%" height="34" class="sectionheadercenter"><a href="?sort=episodes">Eps</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=resolution">Res</a></td>
      <td width="12%" height="34" class="sectionheadercenter"><a href="?sort=type">Type</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=updated">Updated</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=user">User</a></td>
      <td width="8%" height="34" class="sectionheadercenter">ADB Link</td>
      <td width="4%" height="34" class="sectionheaderright">Edit</td>
    </tr>
<?php
while($row=mysql_fetch_array($enresult)){
if ($actioned == $row[ID]) { echo"<tr bgcolor='#3C476F'>";} else { echo"<tr>"; }
      echo"<td width='20%' class='sectioncontentsleft'>$row[series]</td>";
      echo"<td width='20%' class='sectioncontentscenter'>$row[prefix]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[episodes]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[resolution]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[type]</td>";
      if (strtotime($row[updated]) > $lv) { echo"<td width='12%' bgcolor=\"#10AEE1\" style=\"color:#fff;\" class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>"; } else { echo"<td width='12%' class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>";}
      echo"<td width='20%' class='sectioncontentscenter'>";
	  if($profileArray[2] ==1 || $profileArray[2] ==2)
	  {
		  echo '<a title="'.checkServer($row[user]).'">'.checkUserNameNumberNoLink($row[user]).'</a>';
	  }
	  else {
		  echo checkUserNameNumberNoLink($row[user]);
	  }
	  echo "</td>";
      echo"<td width='12%' class='sectioncontentscenter2'>".showAniDBLink($row[anidbsid])."</td>";
      if($profileArray[1] == $row[user] || $profileArray[2] ==1 || $profileArray[2] ==2){
      echo"<td width='4%' class='sectioncontentsright'><div class='editcol' align='center'><a onclick=\" frames['edit'].location.href='/projects_edit.php?id=$row[ID]';document.getElementById('Layer2').style.visibility = 'visible';\" style='cursor:pointer;'>&nbsp;&nbsp;X&nbsp;&nbsp;</a></div></td>";
	  }
	  else {
		  echo "<td width='4%' class='sectioncontentsright'>&nbsp;</td>";
	  }
    echo"</tr>";
$updated="";
}
?>
    <tr>
      <td colspan="9"  height="0" class="sectionbottom">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="8" class="sectiontitle"><div align="left">Uploading*</div></td>
      <td class="sectiontitle"><div align="right"><? if($profileArray[2] != 0 && $profileArray[2] != 3 && $profileArray[2] != 7){echo "<a onClick=\" document.addseries.uestatus.value = 'uploading'; document.getElementById('Layer1').style.visibility = 'visible';\" style='cursor:pointer;'>Add</a>";} else {echo "Add";}?></div></td>
    </tr>
    <tr>
      <td width="20%" height="34" class="sectionheaderleft"><a href="?sort=series">Series</a></td>
      <td width="20%" height="34" class="sectionheadercenter"><a href="?sort=prefix">Prefix</a></td>
      <td width="6%" height="34" class="sectionheadercenter"><a href="?sort=episodes">Eps</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=resolution">Res</a></td>
      <td width="12%" height="34" class="sectionheadercenter"><a href="?sort=type">Type</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=updated">Updated</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=user">User</a></td>
      <td width="8%" height="34" class="sectionheadercenter">ADB Link</td>
      <td width="4%" height="34" class="sectionheaderright">Edit</td>
    </tr>
<?php
while($row=mysql_fetch_array($upresult)){
if (strtotime($row[updated]) > $lastVisit){$updated = "<img src=\"updated.jpg\" height=\"20\" width=\"20\" />";}
if ($actioned == $row[ID]) { echo"<tr bgcolor='#3C476F'>";} else { echo"<tr>"; }
      echo"<td width='20%' class='sectioncontentsleft'>$row[series]</td>";
      echo"<td width='20%' class='sectioncontentscenter'>$row[prefix]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[episodes]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[resolution]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[type]</td>";
      if (strtotime($row[updated]) > $lv) { echo"<td width='12%' bgcolor=\"#10AEE1\" style=\"color:#fff;\" class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>"; } else { echo"<td width='12%' class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>";}
      echo"<td width='20%' class='sectioncontentscenter'>";
	  if($profileArray[2] ==1 || $profileArray[2] ==2)
	  {
		  echo '<a title="'.checkServer($row[user]).'">'.checkUserNameNumberNoLink($row[user]).'</a>';
	  }
	  else {
		  echo checkUserNameNumberNoLink($row[user]);
	  }
	  echo "</td>";
      echo"<td width='12%' class='sectioncontentscenter2'>".showAniDBLink($row[anidbsid])."</td>";
      if($profileArray[1] == $row[user] || $profileArray[2] ==1 || $profileArray[2] ==2){
      echo"<td width='4%' class='sectioncontentsright'><div class='editcol' align='center'><a onclick=\" frames['edit'].location.href='/projects_edit.php?id=$row[ID]';document.getElementById('Layer2').style.visibility = 'visible';\" style='cursor:pointer;'>&nbsp;&nbsp;X&nbsp;&nbsp;</a></div></td>";
	  }
	  else {
		  echo "<td width='4%' class='sectioncontentsright'>&nbsp;</td>";
	  }
    echo"</tr>";
$updated="";
}
?>
    <tr>
      <td colspan="9"  height="0" class="sectionbottom">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="8" class="sectiontitle"><div align="left">Ongoing</div></td>
      <td class="sectiontitle"><div align="right"><? if($profileArray[2] != 0 && $profileArray[2] != 3 && $profileArray[2] != 7){ echo "<a onClick=\" document.addseries.uestatus.value = 'ongoing'; document.getElementById('Layer1').style.visibility = 'visible';\" style='cursor:pointer;'>Add</a>";} else {echo "Add";}?></td>
    </tr>
    <tr>
      <td width="20%" height="34" class="sectionheaderleft"><a href="?sort=series">Series</a></td>
      <td width="20%" height="34" class="sectionheadercenter"><a href="?sort=prefix">Prefix</a></td>
      <td width="6%" height="34" class="sectionheadercenter"><a href="?sort=episodes">Eps</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=resolution">Res</a></td>
      <td width="12%" height="34" class="sectionheadercenter"><a href="?sort=type">Type</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=updated">Updated</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=user">User</a></td>
      <td width="8%" height="34" class="sectionheadercenter">ADB Link</td>
      <td width="4%" height="34" class="sectionheaderright">Edit</td>
      </tr>
    <tr>
<?php
while($row=mysql_fetch_array($onresult)){
if (strtotime($row[updated]) > $lastVisit){$updated = "<img src=\"updated.jpg\" height=\"20\" width=\"20\" />";}
if ($actioned == $row[ID]) { echo"<tr bgcolor='#3C476F'>";} else { echo"<tr>"; }
      echo"<td width='20%' class='sectioncontentsleft'>$row[series]</td>";
      echo"<td width='20%' class='sectioncontentscenter'>$row[prefix]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[episodes]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[resolution]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[type]</td>";
      if (strtotime($row[updated]) > $lv) { echo"<td width='12%' bgcolor=\"#10AEE1\" style=\"color:#fff;\" class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>"; } else { echo"<td width='12%' class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>";}
      echo"<td width='20%' class='sectioncontentscenter'>";
	  if($profileArray[2] ==1 || $profileArray[2] ==2)
	  {
		  echo '<a title="'.checkServer($row[user]).'">'.checkUserNameNumberNoLink($row[user]).'</a>';
	  }
	  else {
		  echo checkUserNameNumberNoLink($row[user]);
	  }
	  echo "</td>";
      echo"<td width='12%' class='sectioncontentscenter2'>".showAniDBLink($row[anidbsid])."</td>";
       if($profileArray[1] == $row[user] || $profileArray[2] ==1 || $profileArray[2] ==2){
      echo"<td width='4%' class='sectioncontentsright'><div class='editcol' align='center'><a onclick=\" frames['edit'].location.href='/projects_edit.php?id=$row[ID]';document.getElementById('Layer2').style.visibility = 'visible';\" style='cursor:pointer;'>&nbsp;&nbsp;X&nbsp;&nbsp;</a></div></td>";
	  }
	  else {
		  echo "<td width='4%' class='sectioncontentsright'>&nbsp;</td>";
	  }
$updated="";
}
?>
    </tr>
    <tr>
      <td colspan="8"  height="0" class="sectionbottom">.</td>
    </tr>
    <tr>
      <td colspan="8" class="sectiontitle"><div align="left">Done</div></td>
      <td class="sectiontitle"><div align="right"><? if($profileArray[2] != 0 && $profileArray[2] != 3 && $profileArray[2] != 7){ echo "<a onClick=\" document.addseries.uestatus.value = 'done'; document.getElementById('Layer1').style.visibility = 'visible';\" style='cursor:pointer;'>Add</a>";} else {echo "Add";}?></div></td>
    </tr>
    <tr>
      <td width="20%" height="34" class="sectionheaderleft"><a href="?sort=series">Series</a></td>
      <td width="20%" height="34" class="sectionheadercenter"><a href="?sort=prefix">Prefix</a></td>
      <td width="6%" height="34" class="sectionheadercenter"><a href="?sort=episodes">Eps</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=resolution">Res</a></td>
      <td width="12%" height="34" class="sectionheadercenter"><a href="?sort=type">Type</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=updated">Updated</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=user">User</a></td>
      <td width="8%" height="34" class="sectionheadercenter">ADB Link</td>
      <td width="4%" height="34" class="sectionheaderright">Edit</td>
    </tr>
<?php
while($row=mysql_fetch_array($dnresult)){
if (strtotime($row[updated]) > $lastVisit){$updated = "<img src=\"updated.jpg\" height=\"20\" width=\"20\" />";}
if ($actioned == $row[ID]) { echo"<tr bgcolor='#3C476F'>";} else { echo"<tr>"; }
      echo"<td width='20%' class='sectioncontentsleft'>$row[series]</td>";
      echo"<td width='20%' class='sectioncontentscenter'>$row[prefix]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[episodes]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[resolution]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[type]</td>";
      if (strtotime($row[updated]) > $lv) { echo"<td width='12%' bgcolor=\"#10AEE1\" style=\"color:#fff;\" class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>"; } else { echo"<td width='12%' class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>";}
      echo"<td width='20%' class='sectioncontentscenter'>";
	  if($profileArray[2] ==1 || $profileArray[2] ==2)
	  {
		  echo '<a title="'.checkServer($row[user]).'">'.checkUserNameNumberNoLink($row[user]).'</a>';
	  }
	  else {
		  echo checkUserNameNumberNoLink($row[user]);
	  }
	  echo "</td>";
      echo"<td width='12%' class='sectioncontentscenter2'>".showAniDBLink($row[anidbsid])."</td>";
       if($profileArray[1] == $row[user] || $profileArray[2] ==1 || $profileArray[2] ==2){
      echo"<td width='4%' class='sectioncontentsright'><div class='editcol' align='center'><a onclick=\" frames['edit'].location.href='/projects_edit.php?id=$row[ID]';document.getElementById('Layer2').style.visibility = 'visible';\" style='cursor:pointer;'>&nbsp;&nbsp;X&nbsp;&nbsp;</a></div></td>";
	  }
	  else {
		  echo "<td width='4%' class='sectioncontentsright'>&nbsp;</td>";
	  }
    echo"</tr>";
$updated="";
}
?>
    <tr>
      <td colspan="9"  height="0" class="sectionbottom">&nbsp;</td>
    </tr>
    <?
	if($profileArray[2] != 1 && $profileArray[2] != 2)
	{
		echo '<tr>
		<td colspan="8" class="sectiontitle"><div align="left">Site Live Series are Admin and Manager accessible only!</div></td>
		</tr>';
	}
	else {
	?>
     <tr>
      <td colspan="8" class="sectiontitle"><div align="left">Site Live</div></td>
      <td class="sectiontitle"><div align="right"><? if($profileArray[2] != 0 && $profileArray[2] != 3 && $profileArray[2] != 7){ echo "<a onClick=\" document.addseries.uestatus.value = 'live'; document.getElementById('Layer1').style.visibility = 'visible';\" style='cursor:pointer;'>Add</a>";} else {echo "Add";}?></div></td>
    </tr>
    <tr>
      <td width="20%" height="34" class="sectionheaderleft"><a href="?sort=series">Series</a></td>
      <td width="20%" height="34" class="sectionheadercenter"><a href="?sort=prefix">Prefix</a></td>
      <td width="6%" height="34" class="sectionheadercenter"><a href="?sort=episodes">Eps</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=resolution">Res</a></td>
      <td width="12%" height="34" class="sectionheadercenter"><a href="?sort=type">Type</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=updated">Updated</a></td>
      <td width="10%" height="34" class="sectionheadercenter"><a href="?sort=user">User</a></td>
      <td width="8%" height="34" class="sectionheadercenter">ADB Link</td>
      <td width="4%" height="34" class="sectionheaderright">Edit</td>
    </tr>
<?php
while($row=mysql_fetch_array($lvresult)){
if (strtotime($row[updated]) > $lastVisit){$updated = "<img src=\"updated.jpg\" height=\"20\" width=\"20\" />";}
if ($actioned == $row[ID]) { echo"<tr bgcolor='#3C476F'>";} else { echo"<tr>"; }
      echo"<td width='20%' class='sectioncontentsleft'>$row[series]</td>";
      echo"<td width='20%' class='sectioncontentscenter'>$row[prefix]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[episodes]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[resolution]</td>";
      echo"<td width='12%' class='sectioncontentscenter'>$row[type]</td>";
      if (strtotime($row[updated]) > $lv) { echo"<td width='12%' bgcolor=\"#10AEE1\" style=\"color:#fff;\" class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>"; } else { echo"<td width='12%' class='sectioncontentscenter'>".date("Y-m-d",strtotime($row[updated]))."</td>";}
      echo"<td width='20%' class='sectioncontentscenter'>";
	  if($profileArray[2] ==1 || $profileArray[2] ==2)
	  {
		  echo '<a title="'.checkServer($row[user]).'">'.checkUserNameNumberNoLink($row[user]).'</a>';
	  }
	  else {
		  echo checkUserNameNumberNoLink($row[user]);
	  }
	  echo "</td>";
      echo"<td width='12%' class='sectioncontentscenter2'>".showAniDBLink($row[anidbsid])."</td>";
	  if($profileArray[1] == $row[user] || $profileArray[2] ==1 || $profileArray[2] ==2){
      echo"<td width='4%' class='sectioncontentsright'><div class='editcol' align='center'><a onclick=\" frames['edit'].location.href='/projects_edit.php?id=$row[ID]';document.getElementById('Layer2').style.visibility = 'visible';\" style='cursor:pointer;'>&nbsp;&nbsp;X&nbsp;&nbsp;</a></div></td>";
	  }
	  else {
		  echo "<td width='4%' class='sectioncontentsright'>&nbsp;</td>";
	  }
    echo"</tr>";
$updated="";
}
?>
    <tr>
      <td colspan="9"  height="0" class="sectionbottom"></td>
    </tr>   
    <?
	}
	?>
  </table>
	<div id="Layer1" name="Layer1" align="center">
      <blockquote>
      <?	
	  	$userquery = mysql_query("SELECT * FROM uestatus WHERE user='".$profileArray[1]."' AND ( status='encoding' OR status='claimed' OR status='uploading' )");
		$countedSeries = mysql_num_rows($userquery);
		if($countedSeries >= 5 && ($profileArray[2] != 1 && $profileArray[2] != 2))
		{
			echo '<form action="http://'.$_SERVER['HTTP_HOST'].'/projects/insert" method="post" name="addseries" id="addseries">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table2">
              <tr>
                <th height="24" colspan="6" background="/images/img03.gif" class="pagetitle" scope="col">WARNING!</td>
            </tr>
              <tr>
                <td width="1%" class="sectioncontentsleft">&nbsp;</td>
              <td width="1%" class="sectioncontentscenter"><span class="style8">&nbsp;</span></td>
              <td width="1%" class="sectioncontentscenter"><span class="style8">&nbsp;</span></td>
              <td width="1%" class="sectioncontentscenter"><span class="style8">&nbsp;</span></td>
              <td width="1%" class="sectioncontentscenter"><span class="style8">&nbsp;</span></td>
              <td width="95%" bgcolor="#000000" class="sectionformright"><span class="style8">&nbsp;</span></td>
            </tr>
              <tr>
                <td width="1%" class="sectioncontentsleft">&nbsp;</td>
              <td class="sectioncontentscenter">&nbsp;</td>
              <td width="1%" class="sectioncontentscenter">&nbsp;</td>
              <td width="1%" class="sectioncontentscenter">&nbsp;</td>
                <td width="1%" class="sectioncontentscenter">&nbsp;</td>
              <td width="95%" bgcolor="#000000" class="sectionformright">You already have 5 Series/Movies/OVAs that you are working on and/or taken, finish them before you move onto the next one.</td>
            </tr>
              <tr>
                <td colspan="6" class="style1"><div align="right">
                  <input name="uestatus" type="hidden" id="uestatus" value="" />
                  <input name="user" type="hidden" id="user" value="'.$profileArray[1].'" />
                  <a href="javascript: document.addseries.reset()" onClick="document.getElementById(\'Layer1\').style.visibility = \'hidden\';">CANCEL</a> </div></td>
            </tr>
            </table>
        </form>';
		}
		else {
	  ?>
          <form action="projects_insert.php" method="post" name="addseries" id="addseries">
            <table width="960" border="0" cellpadding="0" cellspacing="0" class="table2">
              <tr>
                <th height="24" colspan="7" background="/images/img03.gif" class="pagetitle" scope="col">Add Uploading/Encoding Information </td>
            </tr>
              <tr>
                <td width="10%" class="sectioncontentsleft">Series</td>
                <td width="10%" class="sectioncontentscenter"><span class="style8">Prefix</span></td>
                <td width="15%" class="sectioncontentscenter"><span class="style8">Episodes</span></td>
                <td width="15%" class="sectioncontentscenter"><span class="style8">Resolution</span></td>
                <td width="7%" class="sectioncontentscenter">Type</td>
                <td width="7%" class="sectioncontentscenter"><span class="style8">User</span></td>
                <td width="15%" bgcolor="#000000" class="sectionformright"><span class="style8">AniDB ID</span></td>
            </tr>
              <tr>
                <td width="24%" class="sectioncontentsleft"><input name="series" type="text" tabindex="1" size="20" /></td>
              <td class="sectioncontentscenter"><input name="prefix" type="text" tabindex="2" size="15" /></td>
              <td width="13%" class="sectioncontentscenter"><input name="episodesdoing" type="text" size="3" />
                <span class="style9">/
                  <input name="episodestotal" type="text" size="3" />
                </span></td>
              <td width="13%" class="sectioncontentscenter"><input name="resolutionx" type="text" size="4" />
                <span class="style9">x</span>
                <input name="resolutiony" type="text" size="4" /></td>
                <td width="13%" class="sectioncontentscenter"><select tabindex="7" name="type">
                  <option value="series">Series</option>
                  <option value="movie">Movie</option>
                  <option value="ova">Ova</option>
                  </select>                  </td>
              <td width="13%" bgcolor="#000000" class="sectionformcenter"><input name="unknown" type="text" size="19" value="<?=checkUserNameNumberNoLink($profileArray[1]);?>" disabled="disabled" /></td>
                <td width="14%" bgcolor="#000000" class="sectionformright"><input name="anidbsid" id="anidbsid" tabindex="8" type="text" size="5" /></td>
            </tr>
              <tr>
                <td colspan="6" class="style1"><div align="right">
                  <input name="uestatus" type="hidden" id="uestatus" value="" />
                  <input name="user" type="hidden" id="user" value="<?=$profileArray[1];?>" />
                  <a href="javascript: validate();">SUBMIT</a> | <a href="javascript: document.addseries.reset()" onClick="document.getElementById('Layer1').style.visibility = 'hidden';">CANCEL</a> </div></td>
                  <td colspan="1" class="style1">&nbsp;</td>
            </tr>
            </table>
        </form>
        <?
		}
		?>
      </blockquote>
  </div>
</div>
<?php
}
?>
   
  <div id="Layer2" name="Layer2" align="center">
        <blockquote>
		<iframe name="edit" id="edit" scrolling="no" src="projects_edit.php" height="112" width="960" frameborder="0" hspace="0"  align="top" vspace="0" marginheight="0" marginwidth="0" ></iframe>
		</blockquote>
</div>
</div>
    <?

	// Start Main BG
    echo "</td>\n";
	echo "</tr>\n</table>\n";
	// End Main BG
		
include('footer.php')
?>