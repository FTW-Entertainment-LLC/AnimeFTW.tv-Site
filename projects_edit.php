<?php
include('includes/siteroot.php');
$PageTitle = 'AnimeFTW.tv Series Projects | '.$siteroot.' | Your DivX Anime streaming Source!';

include('header.php');

$id = @$_GET[id];


$edit = mysql_query("SELECT * FROM uestatus WHERE uestatus.ID='$id';");
$row=mysql_fetch_array($edit);


$series = $row['series'];
$prefix = $row['prefix'];
$status = $row['status'];
$type = $row['type'];
$episodes = split("/",$row['episodes']);
$resolution = split("x",$row['resolution']);
$user = $row['user'];
$anidbsid = $row['anidbsid'];
?>
<script language="javascript" type="text/javascript">
function validate() 
{
var $alert
if (document.addseries.series.value == "") {$alert = "true"}
if (document.addseries.prefix.value == "") {$alert = "true"}
if (document.addseries.episodestotal.value == "") {$alert = "true"}
if (document.addseries.episodesdoing.value == "" ) {$alert = "true"}
if (document.addseries.resolutionx.value == "") {$alert = "true"}
if (document.addseries.resolutiony.value == "") {$alert = "true"}
if (document.addseries.user.value == "") {$alert = "true"}
if (document.addseries.anidbsid.value == "") {$alert = "true"}
if (document.addseries.uestatus.value == "") {$alert = "true"}
if ($alert == "true") {
alert("Please complete all fields");
} 
else {document.addseries.submit()}
}
</script>

          <div align="center" style="margin:0 0 -40px 0;background-color:#000;height:135px">
            <form name="addseries" method="post" action="/projects_editinsert.php" id="addseries">
              <table width="950" border="0" cellpadding="0" cellspacing="0" class="table2">
                <tr height="24px">
                  <th colspan="8" background="/images/img03.gif" class="pagetitle" scope="col">Edit Uploading/Encoding Information</th>
              </tr>
                <tr>
                  <td width="17%" class="sectioncontentsleft">Series</td>
                <td width="10%" class="sectioncontentscenter"><span class="style8">Prefix</span></td>
                <td width="20%" class="sectioncontentscenter"><span class="style8">Episodes</span></td>
                <td width="19%" class="sectioncontentscenter"><span class="style8">Resolution</span></td>
                <td width="10%" class="sectioncontentscenter">Status</td>
                <td width="7%" class="sectioncontentscenter">Type</td>
                <td width="7%" class="sectioncontentscenter"><span class="style8">User</span></td>
                <td width="10%" bgcolor="#000000" class="sectionformright"><span class="style8">AniDB ID</span></td>
              </tr>
                <tr>
                  <td width="22%" class="sectioncontentsleft"><input name="series" type="text" tabindex="1" size="16" value="<?php echo"$series"; ?>" /></td>
                <td width="21%" class="sectioncontentscenter"><input name="prefix" type="text" tabindex="2" size="10" value="<?php echo"$prefix"; ?>" /></td>
                <td width="16%" class="sectioncontentscenter"><input name="episodesdoing" type="text" tabindex="3" id="episodesdoing" size="3" value="<?php echo"$episodes[0]"; ?>"/>
                  <span class="style9">/
                    <input name="episodestotal" type="text" tabindex="4" id="episodestotal" size="3" value="<?php echo"$episodes[1]"; ?>"/>
                  </span></td>
                <td width="16%" class="sectioncontentscenter"><input name="resolutionx" type="text" tabindex="5" size="4" value="<?php echo"$resolution[0]"; ?>" />
                  <span class="style9">x</span>
                  <input name="resolutiony" type="text" tabindex="6" size="4" value="<?php echo"$resolution[1]"; ?>" /></td>
                <td width="10%" class="sectioncontentscenter"><select tabindex="7" name="uestatus">
                  <?php if(($profileArray[2] == 1 || $profileArray[2] ==2) || ($user == $profileArray[1] && $status == 'claimed')){ ?><option value="claimed" <?php if ($status == 'claimed' ) { echo "selected='selected'"; } ?>>Claimed</option><? }?>
                  <?php if($profileArray[2] == 1 || $profileArray[2] ==2){ ?><option value="live" <?php if ($status == 'live' ) { echo "selected='selected'"; } ?>>Live</option><? }?>
                  <option value="done" <?php if ($status == 'done' ) { echo "selected='selected'"; } ?>>Done</option>
                  <option value="encoding" <?php if ($status == 'encoding' ) { echo "selected='selected'"; } ?>>Encoding</option>
                  <option value="ongoing"<?php if ($status == 'ongoing' ) { echo "selected='selected'"; } ?>>Ongoing</option>
                  <option value="uploading"<?php if ($status == 'uploading' ) { echo "selected='selected'"; } ?>>Uploading</option>
                  </select>                  </td>
                  <td width="10%" class="sectioncontentscenter"><select tabindex="7" name="type">
                  <option value="series"<?php if ($type == 'series' ) { echo " selected='selected'"; } ?>>Series</option>
                  <option value="movie"<?php if ($type == 'movie' ) { echo " selected='selected'"; } ?>>Movie</option>
                  <option value="ova"<?php if ($type == 'ova' ) { echo " selected='selected'"; } ?>>Ova</option>
                  </select>                  </td>
                <td width="14%" bgcolor="#000000" class="sectioncontentscenter"><input name="random" tabindex="8" type="text" size="12" value="<?php echo checkUserNameNumberNoLink($user); ?>" disabled="disabled" /></td>
                <td width="14%" bgcolor="#000000" class="sectionformright"><input name="anidbsid" id="anidbsid" tabindex="8" type="text" size="5" value="<?php echo $anidbsid; ?>" /></td>
              </tr>
                <tr>
                  <td colspan="4" class="style1">
                  <?php if($profileArray[2] == 1 || $profileArray[2] ==2){ ?>
                  <div align="left">
                    <input name="delete" type="checkbox" tabindex="9" id="delete" value="checkbox" />
                    Delete Entry </div>
                    <? } ?></td>
                  <td class="style1"><a href="javascript: frames['edit'].document.addseries.submit()" target="_parent" >
                  <input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>
                  <input type="hidden" name="user" id="user" value="<?=$user;?>"/>
                  </a></td>
                  <td colspan="3" class="style1"><div align="center"><a href="javascript:validate();">SUBMIT</a> | <a href="javascript: document.addseries.reset();" target="_parent" onclick="parent.document.getElementById('Layer2').style.visibility = 'hidden';">CANCEL</a> </div></td>
                </tr>
              </table>
            </form>
          </div>
          </body>
</html>
