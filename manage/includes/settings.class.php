<?php
/****************************************************************\
## FileName: settings.class.php								 
## Author: Brad Riemann								 
## Usage: Settings class for the management interface.
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Settings extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		$this->outputDisplay();
	}
	
	public function outputDisplay()
	{
		if(!isset($_GET['subnode'])){
			echo '<br /><div align="center">Manage: <a href="#" onclick="$(\'#ContentStuff\').load(\'ajax.php?node=management&u='.$this->uid.'&node=settings&subnode=permissions\'); return false;">Permissions</a> | <a href="#" onclick="$(\'#ContentStuff\').load(\'ajax.php?node=management&u='.$this->uid.'&node=settings&subnode=site-settings\'); return false;">Site Settings</a> | <a href="#" onclick="$(\'#ContentStuff\').load(\'ajax.php?node=management&u='.$this->uid.'&node=settings&subnode=site-groups\'); return false;">Groups</a> </div>
		<div id="ContentStuff" class="ContentStuff" style="display:hidden;"><div align="center">Choose From the Above Options.</div></div>';
		}
		if(isset($_GET['subnode']) && $_GET['subnode'] == 'permissions'){
			$query = mysql_query("SELECT * FROM permissions");
			$count = mysql_num_rows($query); 
			echo '<div id="form_results" class="form_results">&nbsp;</div>';
			echo '<form method="POST" action="#" id="SettingsForm"><input type="hidden" name="uid" value="'.$this->uid.'" />';
			echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';	
			echo '<table>';
			$i = 1;
			while($row = mysql_fetch_assoc($query)){
				if($row['parent'] == ''){
					if($i > 1){
						echo '</tr>'."\n";
					}
					if($i != ($count-1)){
						echo '<tr>'."\n";
					}
					$pluslink = '+';
				}
				else {
					echo '</tr><tr>'."\n";
					$pluslink = '&nbsp;';
				}
				echo '<td width="2%"><!-- parent: '.$i.' -->'.$pluslink.'</td>'."\n";
				echo '<td><div align="left">Name: '.$row['name'].'</div></td>'."\n";
				echo '</tr>'."\n";
				echo '<tr>'."\n";
				echo '<td colspan="2">Permissions:<br /> '.$this->BuildGroupPermissionObjects($row['id']).'</td>'."\n";
				if($i == $count){
					echo '</tr>'."\n";
				}
				$i++;
			}
			echo '</table>';
			echo '</div>';	
			echo '
			<input type="hidden" id="method" class="method" value="SettingsSubmit" name="method" /><input type="submit" class="SubmitForm" id="submit" name="submit" value="Submit">';
			echo '</form>';
		}
		else if(isset($_GET['subnode']) && $_GET['subnode'] == 'site-settings'){
			$query = mysql_query("SELECT * FROM settings");
			$count = mysql_num_rows($query); 
			echo '<div id="form_results" class="form_results">&nbsp;</div>';
			echo '<form method="POST" action="#" id="SettingsForm"><input type="hidden" name="uid" value="'.$this->uid.'" />';
			echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';	
			echo '<table>';
			echo '<tr><td><b>Setting</b></td><td><b>Value</b></td></tr>';
			while($row = mysql_fetch_assoc($query)){
				echo '<tr><td width="50%" valign="top"><div align="left">'.$row['display_name'].'</div></td>'."\n";
				echo '<td><input type="text" name="'.$row['name'].'" id="'.$row['name'].'" value="'.$row['value'].'" /></td>'."\n";
				echo '</tr>'."\n";
			}
			echo '</table>';
			echo '</div>';
			echo '
			<input type="hidden" id="method" class="method" value="SiteSettingsSubmit" name="method" /><input type="submit" class="SubmitForm" id="submit" name="submit" value="Submit">';
			echo '</form>';
		}
		else if(isset($_GET['subnode']) && $_GET['subnode'] == 'site-groups'){
			$query = mysql_query("SELECT * FROM site_groups");
			$count = mysql_num_rows($query); 
			echo '<div id="form_results" class="form_results">&nbsp;</div>';
			echo '<form method="POST" action="#" id="SettingsForm"><input type="hidden" name="uid" value="'.$this->uid.'" />';
			echo '<div style="height:400px;overflow-y:scroll;overflow-x:none;">';	
			echo '<table>';
			echo '<tr><td><b>ID Number</b></td><td><b>Group ID#</b></td></tr>';
			while($row = mysql_fetch_assoc($query)){
				echo '<tr><td width="25%">'.$row['groupID'].'</td>'."\n";
				echo '<td valign="top"><div align="left"><input type="text" name="'.$row['groupName'].'" id="'.$row['name'].'" value="'.$row['groupName'].'" /></div></td>'."\n";
				echo '</tr>'."\n";
			}
			echo '</table>';
			echo '</div>';
			echo '
			<input type="hidden" id="method" class="method" value="GroupSettingsSubmit" name="method" /><input type="submit" class="SubmitForm" id="submit" name="submit" value="Submit">';
			echo '</form>';
		}
		else {
		}
			echo '<script>
						$(function() {
							$(\'.form_results\').hide();
							
							$(".SubmitForm").click(function() {
								$.ajax({
									type: "POST",
									url: "/scripts.php",
									data: $(\'#SettingsForm\').serialize(),
									success: function(html) {
										if(html == \'Success\'){
											$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Update Successful</div>");
											$(\'.form_result\').delay(8000).slideUp();
										}
										else{
											$(\'.form_results\').slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Errror: " + html + "</div>");
										}
									}
								});
								return false;
							});
							return false;
						});
						</script>';
	}
}