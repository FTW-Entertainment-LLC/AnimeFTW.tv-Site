<?php
/****************************************************************\
## FileName: config.class.php
## Author: Brad Riemann
## Usage: Configuration Class and Functions
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com' || $_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com')
{
    $rootdirectory = $_SERVER['DOCUMENT_ROOT'];
}
else
{
    $rootdirectory = '/home/mainaftw/public_html';
}

include_once($rootdirectory . "/includes/config_site.php");

class Config {
    public $UserArray, $PermArray, $SettingsArray, $DefaultSettingsArray, $Host, $MainDB, $StatsDB, $RecentEps=array(), $ThisDomain, $ValidatePermission;

    public function __construct($autoBuildUser = FALSE){
        // Declare the main database
        $this->MainDB = 'mainaftw_anime';
        if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com' || $_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com')
        {
            $this->MainDB = 'devsftw9_anime'; // Main DB for everything else
        }
        // Set the domain..
        $this->ThisDomain = ".animeftw.tv";
        if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com' || $_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com')
        {
            $this->ThisDomain = ".ftwdevs.com";
        }
        // Check if the User has acknowledged the cookie for the read only site.
        if (!isset($_COOKIE['eolack']) && !isset($_POST['eolackset'])){
            //print_r($_POST);
            //echo 'printr';
            header('location: /farewell.html');
            exit;
        } else if (!isset($_COOKIE['eolack']) && isset($_POST['eolackset'])){
            // they are attempting to acknowledge the eol of the site.
            setcookie("eolack", '1', time() + (60*60*24*365*10), "/", $this->ThisDomain, 0, 1);
        } else {
        }

        if(isset($_SERVER['HTTP_CF_VISITOR'])){
            $decoded = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
            if($decoded['scheme'] == 'http'){
                // http requests
                $port = 80;
            } else {
                $port = 443;
            }
        } else {
            $port = $_SERVER['SERVER_PORT'];
        }
        if($port == 443)
        {
            //$this->Host = 'https://d206m0dw9i4jjv.cloudfront.net';
            $this->Host = 'https://i.animeftw.tv';
        }
        else
        {
            //$this->Host = 'http://d206m0dw9i4jjv.cloudfront.net';
            $this->Host = 'https://i.animeftw.tv';
        }

        // build the site default settings..
        $this->array_buildDefaultSiteSettings();
        // we want to build the user info by default so it can be usable by all subclassess.. sometimes...
        if($autoBuildUser == TRUE){
            // build our user array
            $this->BuildUser();

            // construct the site settings for the user, if they are logged in..
            $this->array_buildSiteSettings();
        }
    }

    public function buildUserInformation($remote = FALSE)
    {
        // build our user array
        $this->BuildUser();

        // construct the site settings for the user, if they are logged in..
        $this->array_buildSiteSettings();
    }

    public function outputUserInformation()
    {
        return $this->UserArray;
    }

    private function BuildUser($remote = NULL) {
        // we need to check if the token and authentication are setup correctly.
        if(!isset($_COOKIE['0ii']) || !isset($_COOKIE['0au']) || !isset($_COOKIE['0st'])) {
            $count = 0;
        } else {
            // build out the cookies.
            $authorizationId = $_COOKIE['0au'];
            $sessionId = $_COOKIE['0st'];
            $userCookieId = $_COOKIE['0ii'];

            // initial count query
            $query = "SELECT COUNT(id) as `count` FROM `" . $this->MainDB . "`.`user_session` WHERE `id` = '" . mysqli_real_escape_string($sessionId) . "' AND `uid` = '" . mysqli_real_escape_string($userCookieId) . "'";
            $result = mysqli_query($query);
            $count = mysqli_result($result, 0);
        }
        // There is an active token for this user, lets proceed.
        if($count > 0)
        {
            // First thing we will do is validate the authorization token, there must be one prior to moving forward.
            $query = "SELECT * FROM `" . $this->MainDB . "`.`user_authorization` WHERE `id` = '" . mysqli_real_escape_string($authorizationId) . "' AND `uid` = '" . mysqli_real_escape_string($userCookieId) . "'";
            $result = mysqli_query($query);

            if(!$result) {
                echo "There was an error selecting the authorization token." . mysqli_error();
                exit;
            }
            $row = mysqli_fetch_assoc($result);

            // we need to perform a few items to make sure this is a clean session.
            // Ensure the auth settings match, if they do not, compare what the changes are.
            // If the changes are no substantial, then we will let them proceed while updating their profile.
            // This ensures that users can take laptops to different networks without too many issues.
            // it also helps us to avoid constantly changing auth hashes which cause issues down the line.

            // pull down the user's information.
            $userDetails = $this->detectUserAgent();

            // contant to default open.
            $continue = FALSE;
            $changed = 0;
            if($row['ip'] != $_SERVER['REMOTE_ADDR'] && $row['browser'] == $userDetails['browser'] && $row['platform'] == $userDetails['platform'] && $row['version'] == $userDetails['version']) {
                // First check is if the IP changed, but everything else was the same.
                $changed = 1;
                $continue = TRUE;
            } else if($row['ip'] == $_SERVER['REMOTE_ADDR'] && $row['browser'] == $userDetails['browser'] && $row['platform'] == $userDetails['platform'] && $row['version'] != $userDetails['version']) {
                // If the only change is the version, then they can proceed.
                $changed = 2;
                $continue = TRUE;
            } else if($row['ip'] == $_SERVER['REMOTE_ADDR'] && $row['browser'] == $userDetails['browser'] && $row['platform'] == $userDetails['platform'] && $row['version'] == $userDetails['version']) {
                // No changes!
                $continue = TRUE;
            } else {
                // We do not allow any other security changes to be made, so they will be kicked out.
            }

            // Check if the continue option has been changed to true.
            $email = 'null@null';
            if($continue == TRUE) {
                // They have access, first, update the authorization token so we don't keep having to see the same changes.
                if($changed == 1) {
                    // The ip changed.
                    $query = "UPDATE `" . $this->MainDB . "`.`user_authorization` SET `ip` = '" . mysqli_real_escape_string($_SERVER['REMOTE_ADDR']) . "' WHERE `id` = '" . mysqli_real_escape_string($authorizationId) . "' AND `uid` = '" . mysqli_real_escape_string($userCookieId) . "'";
                    $result = mysqli_query($query);
                } else if($changed == 2) {
                    // The version of the browser changed.
                    $query = "UPDATE `" . $this->MainDB . "`.`user_authorization` SET `version` = '" . mysqli_real_escape_string($userDetails['version']) . "' WHERE `id` = '" . mysqli_real_escape_string($authorizationId) . "' AND `uid` = '" . mysqli_real_escape_string($userCookieId) . "'";
                    $result = mysqli_query($query);
                } else {
                    // no other changes are to be made.
                }

                // update the token and user profile, so that the user knows the last time this session was used.
                $query = "UPDATE `" . $this->MainDB . "`.`user_session` INNER JOIN `" . $this->MainDB . "`.`users` ON (`users`.`ID`=`user_session`.`uid`) SET `user_session`.`updated` = '" . time() . "', `users`.`lastActivity`='" . time() . "' WHERE `user_session`.`id` = '" . mysqli_real_escape_string($sessionId) . "' AND `user_session`.`uid` = '" . mysqli_real_escape_string($userCookieId) . "'";
                $result = mysqli_query($query);
                unset($query);
                unset($result);

                // start building the user details
                $query = "SELECT `Email`, `Level_access`, `timeZone`, `Active`, `Username`, `canDownload`, `postBan`, `theme`, `forumBan`, `messageBan`, `viewNotifications`, `advanceActive`, `UploadsVisit` FROM users WHERE ID='" . mysqli_real_escape_string($userCookieId) . "'";
                $result = mysqli_query($query) or die('Error : ' . mysqli_error());
                $row = mysqli_fetch_array($result);
                $Logged = 1;
                $UserID = mysqli_real_escape_string($userCookieId);
                // build the list of information necessary for user interactions.
                $PermissionLevelAdvanced = $row['Level_access'];
                $timeZone = $row['timeZone'];
                $bannedornot = $row['Active'];
                $name = $row['Username'];
                $canDownload = $row['canDownload'];
                $postBan = $row['postBan'];
                $siteTheme = $row['theme'];
                $forumBan = $row['forumBan'];
                $messageBan = $row['messageBan'];
                $viewNotifications = $row['viewNotifications'];
                $AdvanceActive = $row['advanceActive'];
                $UploadsVisit = $row['UploadsVisit'];
                $email = $row['Email'];
            } else {
                // The session is not valid, they will see no session data.
                $Logged = 0;
                $PermissionLevelAdvanced = 0;
                $timeZone = '-6';
                $canDownload = 0;
                $siteTheme = (date('m') == 12) ? 1 : 0;
                $postBan = 0;
                $name = '';
                $bannedornot = 0;
                $UserID = 0;
                $forumBan = 0;
                $messageBan = 0;
                $viewNotifications = 0;
            }
        } else {
            // The user is considered not logged in now.
            $Logged = 0;
            $PermissionLevelAdvanced = 0;
            $timeZone = '-6';
            $canDownload = 0;
            $siteTheme = (date('m') == 12) ? 1 : 0;
            $postBan = 0;
            $name = '';
            $bannedornot = 0;
            $UserID = 0;
            $forumBan = 0;
            $messageBan = 0;
            $viewNotifications = 0;
        }
        $array = array($Logged,$UserID,$PermissionLevelAdvanced,$timeZone,$bannedornot,$name,$canDownload,$postBan,$siteTheme,$forumBan,$messageBan,0,$viewNotifications,$AdvanceActive,$UploadsVisit,$email);
        $this->UserArray = $array;
    }

    public function array_buildSiteSettings()
    {
        $this->SettingsArray = array();

        if($this->UserArray[0] == 1)
        {
            // the user is logged in, book em dan-o
            $query = "SELECT * FROM `user_setting` WHERE `uid` = " . $this->UserArray[1];
            $result = mysqli_query($query);

            $count = mysqli_num_rows($result);
            if($count > 0)
            {
                while($row = mysqli_fetch_assoc($result))
                {
                    $this->SettingsArray[$row['option_id']] = $row;
                }
            }
        }
        else
        {
            $this->SettingsArray['0'] = 0;
        }
    }

    private function array_buildDefaultSiteSettings()
    {
        $this->DefaultSettingsArray = array();

        $query = "SELECT * FROM `user_setting_option`";
        $result = mysqli_query($query);

        while($row = mysqli_fetch_assoc($result))
        {
            $this->DefaultSettingsArray[$row['id']] = $row;
        }
    }

    public function ValidatePermission($permission)
    {
        if(is_numeric($permission))
        {
            /*
            # OID of 1, means it is a Group request
            # OID of 2, means it is a single user Request
            */
            $query = "SELECT deny FROM permissions_objects WHERE permission_id = " . $permission . " AND ((type = 1 AND oid = ".$this->UserArray[2].") OR (type = 2 AND oid = ".$this->UserArray[1]."))";
            $results = mysqli_query($query);
            $count = @mysqli_num_rows($results);
            if($count > 0)
            {
                $Deny = 0;
                while($row = mysqli_fetch_array($results))
                {
                    if($row['deny'] == 1)
                    {
                        $Deny = 1;
                    }
                }
                if($Deny == 1)
                {
                    // if it finds a 1 in the array, its because there is a deny somewhere..
                    return FALSE;
                }
                else
                {
                    // a deny option was not found in the system.. go ahead..
                    return TRUE;
                }
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    // takes a query and a var and retunrs
    public function SingleVarQuery($query,$var)
    {
        $result = mysqli_query($query) or die('Error : ' . mysqli_error());
        $row = mysqli_fetch_array($result);
        return $row[$var];
    }

    // records the mod function right into the database.
    public function ModRecord($type)
    {
        mysqli_query("INSERT INTO modlogs (uid, ip, agent, date, script, request_url) VALUES ('" . $this->UserArray[1] . "', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".time()."', '".$type."', '".mysqli_real_escape_string($_SERVER['REQUEST_URI'])."')");
    }

    // we dont know what it does.. it just looks cool.
    public function Build($var1,$var2){
        $sarray = array(
            'a' => '$2a$10$m5eebjaxijtnafbhqt863n$',
            'b' => '$2a$10$1rdche03z0y65yuirbx9j2$',
            'c' => '$2a$10$w58kxl7rgj4h47rujjkgw2$',
            'd' => '$2a$10$1mwo8ykqm89s4mgbq6eftg$',
            'e' => '$2a$10$7opxsns435g60bitirv5g2$',
            'f' => '$2a$10$i6qmrb5bd2j2y2evs8v4xr$',
            'g' => '$2a$10$tbzoqkirdj267u7lw6t64m$',
            'h' => '$2a$10$dy40suy5eeg7rforo8b4bg$',
            'i' => '$2a$10$6fwfgsg30neqin81jzbs4a$',
            'j' => '$2a$10$ac0y5ebdgt82v0hwzomdyr$',
            'k' => '$2a$10$dn7xhvqunhv89wtxhfucpp$',
            'l' => '$2a$10$2yaocsfe83lhva9hq132zp$',
            'm' => '$2a$10$u2uxxmb0vujcd0w04dgyrv$',
            'n' => '$2a$10$j7dh66ex6a2cu4v34jtdv7$',
            'o' => '$2a$10$809qcxw7df2ror8355hwby$',
            'p' => '$2a$10$wowii9akv7q5pee3eqtsiq$',
            'q' => '$2a$10$aqhfns3hvo94hdsd6rd8xb$',
            'r' => '$2a$10$chjsfo8w0k3pahal5jjukl$',
            's' => '$2a$10$xhromb9gw55u84mew26iqm$',
            't' => '$2a$10$zend8794gsmihxnvn4hr89$',
            'u' => '$2a$10$83q8psnll2orz8gjibphqy$',
            'v' => '$2a$10$8exwykcd97v3fbp26gqe3b$',
            'w' => '$2a$10$cmueo47hk4rdpdozx6sb3r$',
            'x' => '$2a$10$wqjavr92fq7kn1kh8tb27x$',
            'y' => '$2a$10$6rzgtbmuxpodbnfmgs3gk9$',
            'z' => '$2a$10$1kvphqm78zdqoeqmfuf6g3$',
            '0' => '$2a$10$xtjha3kw75l05y53kli9rc$',
            '1' => '$2a$10$iloqaoeqpu4o47nmvv4cj6$',
            '2' => '$2a$10$ngiv7kq9nbro9xxqdwedup$',
            '3' => '$2a$10$5ikgle3duc5su9jk78j108$',
            '4' => '$2a$10$254b10z996dviqliffkng0$',
            '5' => '$2a$10$e04cbsiin8lwc8n20qw3id$',
            '6' => '$2a$10$u1vodloj7l2xtuy3c9hq4x$',
            '7' => '$2a$10$dypumh5ep81ndi3qkf41u2$',
            '8' => '$2a$10$bj939q6rjvgzfqzfct0tfq$',
            '9' => '$2a$10$yfy65x2fucihnce0722m9s$'
        );
        $var2 = substr(strtolower($var2), 0, 1);
        $final = crypt($var1, $sarray[$var2]);
        return $final;
    }

    //Paging function for the management pages, version two
    public function pagingV1($DivID,$count,$perpage,$start,$link)
    {
        $num = $count;
        $per_page = $perpage; // Number of items to show per page
        $showeachside = 4; //  Number of items to show either side of selected page
        if(empty($start)){$start = 0;}  // Current start position
        else{$start = $start;}
        $max_pages = ceil($num / $per_page); // Number of pages
        $cur = ceil($start / $per_page)+1; // Current page number

        // ADDED: 8/21/14 by Robotman321
        // Used to make the pages "nicer"
        if($max_pages == 1)
        {
            $front = "<span style=\"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;\">$max_pages Page</span>&nbsp;";
        }
        else
        {
            $front = "<span style=\"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;\">$max_pages Pages</span>&nbsp;";
        }

        if(($start-$per_page) >= 0)
        {
            $next = $start-$per_page;
            $startpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($next>0?("&page=").$next:"") . '\');return false;" style="padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;">&lt;</a>';
        }
        else
        {
            $startpage = '';
        }
        if($start+$per_page<$num){
            $endpage = '<a href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.'&page='.max(0,$start+1) . '\');return false;" style="padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;">&gt;</a>';
        }
        else {
            $endpage = '';
        }
        $eitherside = ($showeachside * $per_page);
        if($start+1 > $eitherside){
            $frontdots = " ...";
        }
        else {$frontdots = '';}
        $pg = 1;
        $middlepage = '';
        for($y=0;$y<$num;$y+=$per_page)
        {
            $style=($y==$start)?"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;font-weight:bold;":"padding:1px 3px 1px 3px;margin:1px;border:1px solid gray;background-color:#99e6ff;";
            if(($y > ($start - $eitherside)) && ($y < ($start + $eitherside)))
            {
                $middlepage .= '<a style="'.$style.'" href="#" onClick="$(\'#' . $DivID . '\').load(\'' . $link.($y>0?("&page=").$y:"") . '\');return false;">'.$pg.'</a>&nbsp;';
            }
            $pg++;
        }
        if(($start+$eitherside)<$num){
            $enddots = "... ";
        }
        else {$enddots = '';}

        echo '<div class="fontcolor">'.$front.$startpage.$frontdots.$middlepage.$enddots.$endpage.'</div>';
    }

    public function formatUsername($ID,$target = 'self',$lastActivity = NULL)
    {
        $query = "SELECT `Username`, `display_name`, `Level_access`, `advancePreffix`, `advanceImage`, `Active` FROM `users` WHERE `ID`='" . mysqli_real_escape_string($ID) . "'";
        $result = mysqli_query($query) or die('Error : ' . mysqli_error());
        $row = mysqli_fetch_assoc($result);
        $Username = $row['Username'];
        $display_name = $row['display_name'];
        $Level_access = $row['Level_access'];
        $Active = $row['Active'];
        $advanceImage = $row['advanceImage'];
        $advancePreffix = $row['advancePreffix'];

        // Added 8/10/2014 - robotman321
        // If the user has a custom Display_name, we make that the primary username
        if($display_name != $Username && $display_name != NULL)
        {
            // The display name has been setup, lets use that
        }
        else
        {
            $display_name = $Username;
        }

        if($target == 'blank')
        {
            $linklocation = ' target="_blank"';
        }
        else
        {
            $linklocation = '';
        }
        if($Active == 1)
        {
            if($lastActivity != NULL)
            {
                $title = ' title="last click on ' . date("l, F jS, Y, h:i a",$lastActivity) . '"';
            }
            else
            {
                $title = '';
            }
            $link = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $Username . '"' . $title . '>';
            if($advancePreffix != NULL || $advancePreffix != '')
            {
                $spanbefore = '<span style="">';
                $spanafter = '</span>';
            }
            else
            {
                $spanbefore = '';
                $spanafter = '';
            }
            if($Level_access == 1)
            {
                $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/admin-icon.png" alt="Admin of AnimeFTW.tv" title="AnimeFTW.tv Administrator" style="vertical-align:middle;width:16px;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
            }
            else if($Level_access == 2)
            {
                $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/manager-icon.png" alt="Group manager of AnimeFTW.tv" title="AnimeFTW.tv Staff Manager" style="vertical-align:middle;width:16px;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
            }
            else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
            {
                // //i.animeftw.tv/staff-icon.png
                $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/staff-icon.png" alt="Staff Member of AnimeFTW.tv" title="AnimeFTW.tv Staff Member" style="vertical-align:middle;width:16px;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
            }
            else if($Level_access == 7)
            {
                $fixedUsername = '<img src="//i.animeftw.tv/advancedimages/'.$advanceImage.'.png" title="AnimeFTW.tv Advanced Member" alt="Advanced User Title" style="vertical-align:middle;" border="0" /><a href="/user/'.$Username.'">'.$display_name.'</a>';
            }
            else
            {
                $fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"' . $linklocation . '>'.$display_name.'</a>';
            }
        }
        else {
            $fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/'.$Username.'"' . $linklocation . '><s>'.$display_name.'</s></a>';
        }
        return $fixedUsername;
    }

    public function string_fancyUsername($ID,$Username = NULL,$Active = NULL, $Level_access = NULL, $advancePreffix = NULL,$advanceImage = NULL,$UsernameOnly = NULL,$ArrayOutput = FALSE)
    {
        if($ID == 0)
        {
            // if the ID is 0, we need to let them use the supplied credentials
        }
        else
        {
            // ID is supplied, we need to give them the goods.
            $query = 'SELECT `Username`, `display_name`, `Active`, `Level_access`, `avatarActivate`, `avatarExtension`, `advancePreffix`, `advanceImage` FROM `' . $this->MainDB . '`.`users` WHERE `ID` = \'' . mysqli_real_escape_string($ID) . '\'';
            $results = mysqli_query($query);
            $row = mysqli_fetch_assoc($results);
            $Username = $row['Username'];
            $display_name = $row['display_name'];
            $Active = $row['Active'];
            $Level_access = $row['Level_access'];
            $advancePreffix = $row['advancePreffix'];
            $advanceImage = $row['advanceImage'];
            if($row['avatarActivate'] == 'yes')
            {
                // The avatar is considered active in the system.
                $avatar = $this->Host . '/avatars/user' . $ID . '.' . $row['avatarExtension'];
            }
            else
            {
                // it's not active..
                $avatar = $this->Host . '/avatars/default.jpg';
            }
        }

        // Added 8/10/2014 - robotman321
        // If the user has a custom Display_name, we make that the primary username
        if($display_name != $Username && $display_name != NULL)
        {
            // The display name has been setup, lets use that
        }
        else
        {
            $display_name = $Username;
        }

        // Added 8/5/2014 - robotman321
        // Enables the use of non link username construction.
        if($UsernameOnly != NULL)
        {
            $fixedUsername = $display_name;
        }
        else
        {
            // ADDON:
            // Built so that users built within the Android App do not get redirected away from the app and stay in the app.
            if(stristr($_SERVER['HTTP_USER_AGENT'],'tv.animeftw.android/3.0') || stristr($_SERVER['REQUEST_URI'],'/m/'))
            {
                $link = '<a href="#" onClick="$(\'#content\').load(\'ajax.php?page=profile&username=' . $Username . '\'); return false;">';
            }
            else
            {
                $link = '<a href="/user/' . $Username . '">';
            }
            if($Active == 1)
            {
                if ($Level_access != 3)
                {
                    if($advancePreffix != NULL || $advancePreffix != '')
                    {
                        $spanbefore = '<span style="">';
                        $spanafter = '</span>';
                    }
                    else
                    {
                        $spanbefore = '';
                        $spanafter = '';
                    }
                    if($Level_access == 1)
                    {
                        $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/admin-icon.png" alt="Admin of AnimeFTW.tv" title="AnimeFTW.tv Administrator" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
                    }
                    else if($Level_access == 2)
                    {
                        $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/manager-icon.png" alt="Group manager of AnimeFTW.tv" title="AnimeFTW.tv Staff Manager" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
                    }
                    else if($Level_access == 4 || $Level_access == 5 || $Level_access == 6)
                    {
                        // //i.animeftw.tv/staff-icon.png
                        $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/staff-icon.png" alt="Staff Member of AnimeFTW.tv" title="AnimeFTW.tv Staff Member" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
                    }
                    else if($Level_access == 7)
                    {
                        $fixedUsername = $spanbefore . '<img src="//i.animeftw.tv/advancedimages/' . $advanceImage . '.png" title="AnimeFTW.tv Advanced Member" alt="Advanced User Title" style="vertical-align:middle;" border="0" />' . $link . $display_name . '</a>' . $spanafter;
                    }
                    else
                    {
                        $fixedUsername = $spanbefore . $link . $display_name . '</a>' . $spanafter;
                    }
                }
                else
                {
                    $fixedUsername = $link . $display_name . '</a>';
                }
            }
            else
            {
                $fixedUsername = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $Username . '"><s>' . $display_name . '</s></a>';
            }
        }
        if($ArrayOutput == TRUE)
        {
            $fixedUsername = array($fixedUsername,$avatar);
        }
        return $fixedUsername;
    }

    public function formatAvatar($ID,$target = 'self',$link = TRUE,$height = NULL)
    {
        $query = "SELECT `ID`, `Username`, `avatarActivate`, `avatarExtension` FROM `users` WHERE `ID`='" . mysqli_real_escape_string($ID) . "'";
        $result = mysqli_query($query) or die('Error : ' . mysqli_error());
        $row = mysqli_fetch_assoc($result);
        if($height != NULL)
        {
            // we are overriding the styles.
            $style = ' style="height:50px;width:50px;border:0;padding-right:5px;padding-top:3px;"';
        }
        else {
            // just kidding..
            $style = '';
        }
        if($row['avatarActivate'] == 'no')
        {
            $avatar = '<img src="' . $this->Host . '/avatars/default.gif" alt="avatar" border="0"' . $style . ' />';
        }
        else
        {
            $avatar = '<img src="' . $this->Host . '/avatars/user' . $row['ID'] . '.' . $row['avatarExtension'] . '" alt="User avatar" border="0"' . $style . ' />';
        }
        if($target == 'blank')
        {
            $linklocation = ' target="_blank"';
        }
        else
        {
            $linklocation = '';
        }
        if($link != FALSE)
        {
            $fixedAvatar = '<a href="https://' . $_SERVER['HTTP_HOST'] . '/user/' . $row['Username'] . '"' . $linklocation . '>' . $avatar . '</a>';
        }
        else {
            $fixedAvatar = $avatar;
        }
        return $fixedAvatar;
    }

    public function validateAPIUser($username,$password)
    {
        $query = "SELECT ID FROM `users` WHERE Username = '" . mysqli_real_escape_string($username) . "' AND Password = '" . md5($password) . "'";
        $results = mysqli_query($query);

        $count = mysqli_num_rows($results);

        if($count > 0)
        {
            // we found a row
            $row = mysqli_fetch_array($results);
            $returnArray = array(TRUE,$row['ID']);
        }
        else
        {
            $returnArray = array(FALSE,"0");
        }
        return $returnArray;
    }

    public function array_buildRecentlyWatchedEpisodes()
    {
        // let's only load this when it's a video page..
        if($_SERVER['PHP_SELF'] == '/videos.php')
        {
            $query = "SELECT `eid`, `time`, `updated`, `max` FROM `episode_timer` WHERE `uid` = " . $this->UserArray[1];
            $result = mysqli_query($query);

            if(!$result)
            {
                echo 'There was an issue with the communications.';
            }
            else
            {
                $count = mysqli_num_rows($result);
                if($count > 0)
                {
                    $i = 0;
                    while($row = mysqli_fetch_assoc($result))
                    {
                        $this->RecentEps[$row['eid']]['time'] = $row['time'];
                        $this->RecentEps[$row['eid']]['updated'] = $row['updated'];
                        $this->RecentEps[$row['eid']]['max'] = $row['max'];
                        $i++;
                    }
                }
                else
                {
                    $this->RecentEps[] = 0;
                }
            }
        }
    }
    public function uploadsEntrySelect($upload_id, $extra)
    {
        $query = "SELECT ID, series FROM uestatus ORDER BY series ASC";
        $results = mysqli_query($query);

        if(!$results)
        {
            echo 'There was an error with the MySQL Query: ' . mysqli_error();
            exit;
        }
        $Data = '<select '.$extra.'name="uploadsEntry" style="color: #000000;width:570px;" class="text-input"><option value="0"> Select an Entry </option>';
        while($row = mysqli_fetch_assoc($results))
        {
            // make sure to check if it is numeric, if it is, we can push it to the actual good stuff
            if($upload_id == $row['ID'])
            {
                $Data .= '<option value="' . $row['ID'] . '" selected="selected">' . $row['series'] . '</option>';
            }
            else
            {
                $Data .= '<option value="' . $row['ID'] . '">' . $row['series'] . '</option>';
            }
        }
        $Data .= '</select>';
        return $Data;
    }
    public function buildCategories()
    {
        $query = "SELECT * FROM `categories`";
        $result = mysqli_query($query);
        while($row = mysqli_fetch_assoc($result))
        {
            $this->Categories[$row['id']]['id'] = $row['id'];
            $this->Categories[$row['id']]['name'] = $row['name'];
            $this->Categories[$row['id']]['description'] = $row['description'];
        }
    }

    public function generateRandomString($length = 10)
    {
        $randomString = substr(str_shuffle(MD5(microtime())), 0, $length);
        return $randomString;
    }

    public function detectUserAgent() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

        // What version?
        if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) {
            $version = $matches[1];
        } else {
            $version = 'unknown';
        }

        $browser = $this->getBrowser($userAgent);
        $platform = $this->getOS($userAgent);

        return array (
            'browser'   => $browser,
            'version'   => $version,
            'platform'  => $platform,
            'userAgent' => $userAgent
        );
    }

    public function getOS($agent)
    {
        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/windows phone 8.1/i'  =>  'Windows Phone 8.1',
            '/windows phone 8/i'    =>  'Windows Phone 8',
            '/windows phone 7.5/i'  =>  'Windows Phone 7.5',
            '/windows phone 7/i'    =>  'Windows Phone 7',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile',
            '/cros/i'               =>  'ChromeOS',
            '/playstation vita/i'   =>  'PlayStation Vita',
        );

        foreach($os_array as $regex => $value)
        {
            if(preg_match($regex, $agent))
            {
                $os_platform    =   $value;
                break;
            }
        }

        return $os_platform;
    }

    public function getBrowser($agent)
    {
        $browser        =   "Unknown Browser";
        $browser_array  =   array(
            '/iemobile/i'   =>  'Internet Explorer Mobile',
            '/msie/i'       =>  'Internet Explorer',
            '/trident/i'    =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser',
            '/palemoon/i'   =>  'Palemoon',
            '/silk/i'       =>  'Silk',
        );

        foreach($browser_array as $regex => $value)
        {
            if(preg_match($regex,  $agent))
            {
                $browser    =   $value;
                break;
            }
        }

        return $browser;
    }

    public function timeZoneChange($date,$timezone)
    {
        $timezone = (60*60)*($timezone+6);
        $revisedDate = $date+($timezone);
        return $revisedDate;
    }


    #-------------------------------------------------------------
    # Function checkFailedLogins
    # Checks failed logins against the server,
    # once it hits 5 then it blocks the user for 15 minutes by
    # setting a cookie that expires in 15 min.
    #-------------------------------------------------------------

    public function checkFailedLogins($ip) {
        $fivebefore = time()-300;
        $query1 = "SELECT ip FROM `failed_logins` where date>='".$fivebefore."' AND ip='".$ip."'";
        $result1 = mysqli_query($query1);
        $total_fails = mysqli_num_rows($result1);
        if($total_fails == 1){
            $statement = '1 of 5 Failed Login attempts Used.';
        }
        else if ($total_fails == 2){
            $statement = '2 of 5 Failed Login attempts Used.';
        }
        else if ($total_fails == 3){
            $statement = '3 of 5 Failed Login attempts Used.';
        }
        else if ($total_fails == 4){
            $statement = '4 of 5 Failed Login attempts Used.';
        }
        else {
            $statement = '5 of 5 Failed Login attempts Used.<br /> You will be forbidden from logging in for the next 15 minutes.';
            $this->setFailedLoginCookie();
        }
        return $statement;
    }

    #-------------------------------------------------------------
    # Function setFailedLoginCookie
    # sets a cookie saying a user cannot login for 15 min
    #-------------------------------------------------------------

    private function setFailedLoginCookie(){
        setcookie ( "__flc", time() + 900, time() + 900, '/' );
    }

    # Function designed to limit what a user can do if their PM abilities have been revoked.
    public function checkPMAbilities(){
        if($this->UserArray[10] == 2){
            # it means they are banned from pming other non staff
            $returnArray = array('canpmstaff' => 1, 'message' => 'Your message sending abilities have been limited to communications with Site Admin and Managers.');
        }
        else if($this->UserArray[10] == 1) {
            # it means that they are not allowed to use the pm system, period.
            $returnArray = array('canpmstaff' => 2, 'message' => 'Your access to the messaging system has been removed fully.');
        }
        else {
            # They are not banned, no worries.
            $returnArray = array('canpmstaff' => 0, 'message' => '');
        }
        return $returnArray;
    }

    # Function to post alerts to Slack.
    public function postToSlack($sendingText,$type=null){
        if($type == null || $type == 0) {
            // post to #alerts
            $url = 'https://discordapp.com/api/webhooks/245573855249301504/1lRP0UaoEflbNwrebqKxEf2FP64EPnV4v0P9K4KF8m1QUWXxdfjYym_9lzp_TBIWum3I';
        } else {
            // Post to #managers
            $url = 'https://discordapp.com/api/webhooks/278872024724537344/jsvA94duEkd0Sor3zZbzQ0zfRPwhFeX74Mg41cS6n3XRuGqbjQV2XOlHYj5pi3k3DxtT';
        }

        $data = array(
            "content" => $sendingText,
        );

        $data_string = json_encode($data);
        //echo $data_string;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
    }

    # Function replaces dashes in array keys IF we are required to do so.
    public function replaceArrayKeyDashes($array) {
        $returnData = '';
        foreach($array as $key => $value) {
            // We check to see if the value is an array, if it is, we loop through everything.
            if(is_array($value)) {
                $returnData[$key] = $this->replaceArrayKeyDashes($value);
            } else {
                $returnData[str_replace('-', '_', $key)] = $value;
            }
        }
        return $returnData;
    }

    public static function generateUUIDV4($data) {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
