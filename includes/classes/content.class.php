<?php
/****************************************************************\
## FileName: Content.class.php                                     
## Author: Brad Riemann                                         
## Usage: Content Class and Functions
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

//include('/home/mainaftw/public_html/includes/classes/config.class.php'); //include the config class for good measure.

class Content extends Config {
    
    /******************\
    # Vars               #
    \******************/
    
    var $uid, $UserArray, $SettingsArray, $DefaultSettingsArray;
    
    /******************\
    # Public Functions #
    \******************/
    
    public function __construct(){
        parent::__construct();
    }
    
    public function Output(){
        // The following is populated with the understanding that we are going to to dynamically drive all the content, via a web form or wysiwig editory..
        if(isset($_GET['node'])){
            $this->BuildContent();
        }
        else {
            echo 'Error 404, that page was not found.';
        }
    }
    
    public function connectProfile($input)
    {
        $this->UserArray = $input;
    }
    
    /******************\
    # Private Functions#
    \******************/
    
    private function BuildContent(){
        // we are hijacking this function so we can do an "eblast" subscription page without logging in function.
        // Sorry.
        if (isset($_GET['node']) && $_GET['node'] == 'eblast') {
            $this->displayEblastSettings();
        } else {
            $query = "SELECT id, full_page_name, body FROM content WHERE node = '".mysql_real_escape_string($_GET['node'])."' AND permissions LIKE '%".$this->UserArray[2]."%'";
            if($this->UserArray[2] == 1){
                //echo $query;
            }
            $results = mysql_query($query); // or die($query."<br/><br/>".mysql_error())
            $count = mysql_num_rows($results);
            
            if($count == 0){ // zero count means they cant see it.. or it doesnt exist.. which means we need to give them the 404 page..
                $this->ShowErrorMessage('404');
            }
            else if($count == 1){
                $results = mysql_query($query);
                $row = mysql_fetch_array($results); //there should be one result but lets give it to the array..
                echo "<div class='side-body-bg'>\n";
                echo "<span class='scapmain'>".stripslashes($row['full_page_name'])."</span>\n";
                echo "</div>\n";
                echo "<div class='side-body'>\n";
                echo stripslashes($row['body']);
                echo "</div>";
            }
            else if($count > 1){ //the count is greater than one, so we need to dig deeper (as there are now subnodes!..
                if(isset($_GET['subnode'])){
                    $query .= " AND sub_node = '".mysql_real_escape_string($_GET['subnode'])."'";
                    $results = mysql_query($query);
                    $row = mysql_fetch_array($results); //there should be one result but lets give it to the array..
                    
                    echo "<div class='side-body-bg'>\n";
                    echo "<span class='scapmain'>".stripslashes($row['full_page_name'])."</span>\n";
                    echo "</div>\n";
                    echo "<div class='side-body' align=\"center\">\n";
                    $body = stripslashes($row['body']);
                    if($_GET['subnode'] == 404 && $_GET['node'] == 'error'){
                        //$this->RandomAnime();
                        echo str_replace('<randomanime>',$this->RandomAnime(),$body);
                    }
                    else {
                        echo $body;
                    }
                    echo "</div>";
                }
                else { //if there is more than one page, then we need to narrow some stuff down, but since we cant, they get this..
                    echo 'There were more than one results found, this means that there was a misconfiguration with the page you were attempting to access, please contact the staff on the forums and let them know what page you were attempting to view.';
                }
            }
        }
    }
    
    private function ShowErrorMessage($errornum){
        $query = "SELECT * FROM content WHERE node = 'error' AND sub_node = '".$errornum."'";
        $results = mysql_query($query);
        $row = mysql_fetch_array($results); //there should be one result but lets give it to the array..
        echo "<div class='side-body-bg'>\n";
        echo "<span class='scapmain'>".stripslashes($row['full_page_name'])."</span>\n";
        echo "</div>\n";
        echo "<div class='side-body'>\n";
        echo stripslashes($row['body']);
        echo "</div>";
        
    }
    
    private function RandomAnime(){
        $query = "SELECT id, seoname, fullSeriesName FROM series WHERE aonly = 0 ORDER BY RAND() LIMIT 0, 1";
        $results = mysql_query($query);
        $row = mysql_fetch_array($results);
        return '<br /><span style="font-size:24px;"><a href="/anime/'.$row[1].'/" onmouseover="ajax_showTooltip(window.event,\'/scripts.php?view=profiles&show=tooltips&id='.$row[0].'\',this);return false" onmouseout="ajax_hideTooltip()">'.stripslashes($row[2]).'</a></span>';
    }
    
    private function displayEblastSettings()
    {
        // Ensure the Base64 email and uid were supplied
        if (isset($_GET['email']) && isset($_GET['id'])) {
            $userId = substr($this->base64url_decode($_GET['id']),4);
            $email = $this->base64url_decode($_GET['email']);
            // Validate that the base64 of the email and the uid checks out.
            $query = "SELECT ID FROM `users` WHERE `Email` = '" . mysql_real_escape_string($email) . "' AND `ID` = " . mysql_real_escape_string($userId);
            $result = mysql_query($query);
            $count = mysql_num_rows($result);
            echo "<div class='side-body-bg'>\n";
            echo "<span class='scapmain'>Manage your AnimeFTW.tv email setttings!<br></span><br>\n";
            echo "<div class='side-body' align=\"center\">\n";
            if ($count < 1) {
                echo '<h4>ERROR: There was an issue performing the request, please try again later.</h4>';
            } else {
                // Populate the various settings that are available for Email notifications.
                include_once('settings.class.php');
                $Settings = new Settings(true);
                
                $Settings->array_userSiteSettings($userId); //builds the list of user specific settings.
                $Settings->array_availableSiteSettings(); //builds the list of options for each option.
                echo '
                <form id="SiteSettings">
                <input type="hidden" name="method" value="EditEblastSettings" />
                <input type="hidden" name="data" value="$aSDxAAs3SSa/' . $_GET['id'] . '/' . $_GET['email'] . '/0dAS8dE$dPOSoq" />
                <br />
                <div>
                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:16px;border-bottom:solid 1px #D1D1D1">Please use this form to update your notification settings for the AnimeFTW.tv Website.</div>
                </div>
                <div>' . $Settings->returnSiteSettings(1) . '</div>
                <br>
                <div align="right">
                    <div style="min-height:16px;">
                        <div class="form_results" style="display:none;"></div>
                    </div>
                <input type="submit" value=" Update " name="settings-submit" id="settings-submit" /></div>
                </form>
                <script>
                    $("#settings-submit").click(function(){
                        $.ajax({
                            type: "POST",
                            url: "/scripts.php?view=settings&go=notifications",
                            data: $("#SiteSettings").serialize(),
                            success: function(html)
                            {
                                if(html.indexOf("Success") >= 0)
                                {
                                    $(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#14C400;padding:2px;\'>Notification Settings Updated Successfully.</div>");											
                                    $(".form_results").delay(8000).slideUp();
                                }
                                else{
                                    $(".form_results").slideDown().html("<div align=\'center\' style=\'color:#FFFFFF;font-weight:bold;background-color:#FF0000;padding:2px;\'>Error Updating Notification Settings: " + html + "</div>");
                                }
                            }
                        });
                        return false;
                    });
                </script>';
                
                
            }
            $query = "";
            echo "</div>\n";
            echo "</div>\n";
        }
    }
}

?>