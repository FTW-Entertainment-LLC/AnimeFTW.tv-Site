<?php
/****************************************************************\
## FileName: settings.class.php			 
## Author: Brad Riemann				 
## Usage: Settings Class implementation system. (Version 1)
## Copywrite 2017 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Settings Extends Config {
    
    public $SiteSettings, $SiteUserSettings, $UserArray;
    private $externalAccess;
    
	public function __construct($externalAccess = false)
	{
		parent::__construct();
        $this->externalAccess = $externalAccess;
	}
    
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
    
	public function returnSiteSettings($SettingCatId = NULL)
	{
		if ($SettingCatId == NULL) {
			// null so we want to just dump all of the settings
			$SQLAddon = "";
			$returndata = array();
		} else {
			// not null, we need to proceed as though this is individual..
			$SQLAddon = " WHERE `parent` = $SettingCatId";
			$returndata = '';
		}
		$query = "SELECT * FROM `user_setting_option`$SQLAddon";
		$result = mysqli_query($conn, $query);
		
		$count = mysqli_num_rows($result);
		
		if ($count == 0) {
			$returndata = 'There are no entries for this option yet.';
		} else {
			$i = 0;
			while ($row = mysqli_fetch_assoc($result)) {				
				if ($SettingCatId == NULL) {
					$returndata[$row['id']] = $row;
				} else {
					$groups = explode(":", $row['group']);
					if ($i%2) {
						$Style = 'background:#4bccf6;';
					} else {
						$Style = 'background:#dadada;';
					}
                    
					if (in_array($this->UserArray[2],$groups) || $this->externalAccess == true) {
						// are they allowed to select the option.
						$AMOnly = '';
						$Disabled = FALSE;
					} else {
						// the user's array was not in the group, continue..
						$AMOnly = '<span style="font-size:10px;"><a href="/advanced-signup">Advanced Feature</a></span>';
						$Disabled = TRUE;
					}
					$returndata .= '
					<div style="padding: 5px 0 5px 4px;' . $Style . '">
						<div style="display:inline-block;width:70%;vertical-align:top;">
							<div style="font-size:14px;border-bottom:1px solid gray" align="center">' . stripslashes($row['name']) . '</div>
							<div>' . stripslashes($row['description']) . '</div>
						</div>
						<div style="display:inline-block;width:29%;vertical-align:top;">
							<div align="center">' . $AMOnly . '<br />' . $this->settingFormType($row['id'],$row['type'],$row['default_option'],$Disabled) . '</div>
						</div>
					</div>';
				}
				$i++;
			}
		}
		return $returndata;
	}
	
	private function settingFormType($id,$type,$default_option,$disabled)
	{
		$returndata = '';
		if ($disabled == TRUE) {
			$disabled = ' disabled="disabled"';
		} else {
			$disabled = '';
		}
		if ($type == 0) {
			// select form.. let's render the data.
			$returndata .= '<select id="setting-' . $id . '" name="setting-' . $id . '" class="loginForm"' . $disabled . '>';
			foreach ($this->SiteSettings[$id] AS $AvailableOptions) {
				$Disabled = FALSE; // we set this by default, useful later..
				// first check to see if this exists in the array
				if (array_key_exists($id,$this->SiteUserSettings)) {
					// option was selected by the user
					if ($this->SiteUserSettings[$id]['value'] == $AvailableOptions['id']) {
						$Selected = ' selected="selected"'; // this is obviously selected..
						// this option was selected by the user.. we need to make sure it's not disabled..
						if ($this->SiteUserSettings[$id]['disabled'] == 1) {
							// we use this option and disable the ability to select anything else..
							$Disabled = TRUE;					
						}
					} else {
						$Selected = '';
					}
				} else {
					if ($default_option == $AvailableOptions['id']) {
						$Selected = ' selected="selected"';
					} else {
						$Selected = '';
					}
				}
				$returndata .= '<option value="' . $AvailableOptions['id'] . '"' . $Selected . '>' . $AvailableOptions['name'] . '</option>';
			}
			$returndata .= '</select>';
		}
		return $returndata;
	}
    	
	public function processSiteSettingsUpdate($profileArray = null)
	{
		if (!isset($_POST['uid']) && $profileArray != null) {
			echo 'There were critical pieces missing for this submission.';
		} else {
			if ((($profileArray[2] != 1 && $profileArray[2] != 2) && ($_POST['uid'] != $profileArray[1])) || ($profileArray == null && (isset($_POST['method']) && $_POST['method'] == 'EditEblastSettings') && (stristr($_POST['data'], '$aSDxAAs3SSa/') === false || stristr($_POST['data'], '/0dAS8dE$dPOSoq') === false))) {
				print_r($_POST);
                echo 'You are not authorized for this function.';
			} else {
                if (isset($_POST['data'])) {
                    $dataExplosion = explode('/', $_POST['data']);
                    $userId = substr($this->base64url_decode($dataExplosion[1]),4);
                } else {
                    $userId = $_POST['uid'];
                }
				$SiteSettings = $this->returnSiteSettings();
				foreach ($_POST AS $key => &$value) {
					$option_id = substr($key,8);
					if (substr($key,0,7) == 'setting') {
						$query = "SELECT `id` FROM `user_setting` WHERE `uid` = " . mysqli_real_escape_String($userId) . " AND `option_id` = " . mysqli_real_escape_string($conn, $option_id);
						$result = mysqli_query($conn, $query);
						$count = mysqli_num_rows($result);
						
						// this is a setting.. check to see if its a default or not.
						if ($SiteSettings[$option_id]['default_option'] == $value) {
							if ($count < 1) {
								echo 'Nothing to remove for id ' . $option_id . '<br>';
							} else {
								// this setting is the same as what the default should be, we will delete any entries that may exist so the system will know to take defaults.
								$query = "DELETE FROM `user_setting` WHERE `uid` = " . mysqli_real_escape_String($userId) . " AND `option_id` = " . mysqli_real_escape_string($conn, $option_id);
								$result = mysqli_query($conn, $query);
								echo 'Deleted entry for id ' . $option_id . '<br>';
							}
						} else {
							if ($count >= 1) {
								echo 'Nothing to add for id ' . $option_id . '<br>';
							} else {
								// this setting is not the same as the default, so we need to add it to the database.
								$result = mysqli_query($conn, "INSERT INTO `user_setting` (`id`, `uid`, `date_added`, `date_updated`, `option_id`, `value`, `disabled`) VALUES (NULL, '" . mysqli_real_escape_String($userId) . "', " . time() . ", " . time() . ", " . $option_id . ", " . mysqli_real_escape_string($conn, $value) . ", 0)");
								echo 'Added entry for id ' . $option_id . '<br>';
							}
						}
					}
				}
				echo 'Success';
			}
		}
	}
	
	public function array_userSiteSettings($ruid)
	{
		//builds the list of user specific settings.
		$query = "SELECT * FROM `user_setting` WHERE `uid` = " . mysqli_real_escape_string($conn, $ruid);
        
		$result = mysqli_query($conn, $query);
		$this->SiteUserSettings = array();
		
		$count = mysqli_num_rows($result);
		if ($count > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$this->SiteUserSettings[$row['option_id']] = $row; 
			}
		}
	}
	public function array_availableSiteSettings()
	{
		//builds the list of options for each option.
		$query = "SELECT * FROM `user_setting_option_values`";
		// id 	name 	option_id 
		$result = mysqli_query($conn, $query);
		$this->SiteSettings = array();
		
		while ($row = mysqli_fetch_assoc($result)) {
			$this->SiteSettings[$row['option_id']][$row['id']]['id'] = $row['id'];
			$this->SiteSettings[$row['option_id']][$row['id']]['name'] = $row['name'];
			$this->SiteSettings[$row['option_id']][$row['id']]['option_id'] = $row['option_id'];
		} 
	}	

}