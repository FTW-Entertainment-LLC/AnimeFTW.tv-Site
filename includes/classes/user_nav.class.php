<?php
/****************************************************************\
## FileName: user_nav.class.php
## Author: Brad Riemann
## Usage: User Nav UI Class
## Copywrite 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWUserNav extends Config {
    /****************************\
    # Variables and constructors #
    \****************************/
    var $UserArray;

    public function __construct($UserArray){
        parent::__construct();
        // Because this is not taking the information as it is declared outside of the config class.. we need to manually bridge the two here.
        $this->UserArray = $UserArray;
    }

    public function Output(){
        $this->BuildUI();
    }

    /*******************\
    # Private Functions #
    \*******************/

    private function BuildUI(){
        if($this->UserArray[0] == 1) {
            echo '<div class="newnavbar">';
            echo '<div id="user-bar-username" class="aftwU" title="Click to Toggle your hotbar!">'.$this->formatUsername($this->UserArray[1]);
            echo '<span id="user-bar-down-arrow" class="transparent-arrow-down">&nbsp;&nbsp;</span><span id="user-bar-up-arrow" class="transparent-arrow-up" style="display:none;">&nbsp;&nbsp;</span>';
            if (!isset($_COOKIE['enahnced-bar']) || (isset($_COOKIE['enahnced-bar']) && $_COOKIE['enahnced-bar'] != 1)) {
                echo '
                <div id="dot-container">
                    <div class="dot"></div>
                    <div class="pulse"></div>
                </div>';
            }
            echo '
            </div>';
            if($this->Messages($this->UserArray[1]) > 0){
                $display = " disBlock";
            }
            else {
                $display = "";
            }
            if($this->UserArray[1] == 7){
                echo '<div class="aftwNot">' . $this->CheckAdvancedStatus() . '</div>';
            }
            echo '<div class="aftwNot"><a href="https://' . $_SERVER['HTTP_HOST'] . '/pm"><img src="//i.animeftw.tv/new-icons/pm_new.png" alt="" title="View your Personal Messages" /><span class="JewelNotif'.$display.'" id="requestJewelNotif">'.$this->Messages($this->UserArray[1]).'</span></a>
            </div>';
            echo '<div class="dropdown">';

            include('notifications.class.php');
            $N = new AFTWNotifications();
            $N->connectProfile($this->UserArray);
            echo '
                       <a href="#" id="linkglobal"><img src="//i.animeftw.tv/new-icons/notifications_new.png" alt="" title="View your Site Notifications" height="18px" /><span id="notesprite">' . $N->ShowSprite() . '</span></a>
                            <ul style="display: none;" id="ulglobal">
                                <img src="//i.animeftw.tv/new-icons/trangle-sprite.png" alt="" style="float:right;margin:-20px 123px 0 0;" />
                                <div class="usernotes" id="usernotes"><div style="white-space:nowrap;padding:2px;">Loading your Notifications...</div></div>
                          </ul>';
            echo '</div>';
            if($this->UserArray[2] != 0 && $this->UserArray[2] != 3 && $this->UserArray[2] != 7){
                if($this->UserArray[2] == 1 || $this->UserArray[2] == 2){
                    $query = mysqli_query($conn, "SELECT ID FROM uestatus WHERE `change` = 1");
                    $CountUpdated = mysqli_num_rows($query);
                    if($CountUpdated > 0){
                        $display2 = " disBlock";
                    }
                    else {
                        $display2 = "";
                    }
                }
                else {$display2 = "";$CountUpdated = "0";}
                echo '<div class="aftwNot"><a href="https://' . $_SERVER['HTTP_HOST'] . '/manage/"><img src="//i.animeftw.tv/new-icons/uploads_new.png" alt="" /><span class="JewelNotif'.$display2.'" id="requestJewelNotif">'.$CountUpdated.'</span></a></div>';
            }
            echo '<div class="aftwNot"><a href="https://' . $_SERVER['HTTP_HOST'] . '/logout"><img src="//i.animeftw.tv/new-icons/logout_new.png" alt="" title="Log off your AnimeFTW.tv Account" /></a></div>';
            echo '
            <div id="user-enhanced-bar" style="display:none;">';
            if ($this->UserArray[2] == 7) {
                echo '
                <div align="center" style="padding:3px;border-bottom:1px solid #1f566f;">Your Advanced Membership is: <br />';
                if ($this->UserArray[13] == 'yes') {
                    // Advanced Membership is active
                    echo '<span style="font-size:18px;color:green;font-weight:bold;">Active</span>.<br />If you wish to cancel, please <br /> login to the <br /><a style="font-weight:bold;" href="https://ftwentertainment.com/supporters" target="blank">FTW Supporters portal</a>.';
                } else {
                    // Not active.
                    echo '<span style="font-size:18px;color:red;font-weight:bold;">In-Active</span>.<br /> Your account will be <br /> automatically moved back to a <br /> basic member at the conclusion of your stay.';
                }
                echo '
                    </div>';
            } else {
                echo '
                <div align="center" style="padding:3px;"><a href="/advanced-signup" title="Support the site in our conquests! Sign up for FTW Subscriber status today!"><span style="color:#FF0000;">Come to the Dark Side.<br /> Become Advanced.</span><br /><span style="color:white;font-weight:bold;">We have cookies.</span></a></div>';
            }
            $requestedUsername = $this->string_fancyUsername($this->UserArray[1],NULL,NULL,NULL,NULL,NULL,TRUE,FALSE);
            echo '
                <div align="center" style="padding:3px;">
                    <div style="padding-bottom:2px;">Account Options:</div>
                    <a href="/user/' . strtolower($requestedUsername) . '#settings" title="Click to go to your profile and edit your settings."><div class="enhanced-user-sprites user-sprite-gears"></div></a>
                    <a href="/user/' . strtolower($requestedUsername) . '#notifications"><div class="enhanced-user-sprites user-sprite-notification"></div></a>
                    <a href="/donate"><div class="enhanced-user-sprites user-sprite-user"></div></a>
                    <a href="#" onClick="alert(\'We were just asking what to do with this button, we still dont know, sorry, try again later (much later)!\'); return false;"><div class="enhanced-user-sprites user-sprite-lightbulb"></div>
                </div>';
            if (!isset($_COOKIE['enahnced-bar']) || (isset($_COOKIE['enahnced-bar']) && $_COOKIE['enahnced-bar'] != 1)) {
                echo '
                <div align="left" id="yellow-icon-remove-row" style="cursor: pointer;"><input type="checkbox" name="remove-yellow-icon" id="remove-yellow-icon" />&nbsp;Remove the notification dot.</div>';
            }
            echo '
            </div>
            <script>
                $("#yellow-icon-remove-row").click(function() {
                    $.post("/scripts.php?view=utilities&mode=hide-dot&value=notification-dot", function(data) {
                    });
                    $("#remove-yellow-icon").prop( "checked", true );
                    $("#dot-container").fadeOut("slow");
                    $("#yellow-icon-remove-row").slideUp("slow");
                    return false;
                });
                $("#user-bar-username").click(function() {
                    $("#user-enhanced-bar").slideToggle("slow");
                    if($("#user-bar-up-arrow").css("display") == "none") {
                        $("#user-bar-down-arrow").hide();
                        $("#user-bar-up-arrow").show();
                    } else {
                        $("#user-bar-up-arrow").hide();
                        $("#user-bar-down-arrow").show();
                    }
                    return false;
                });
            </script>';
            echo '</div>';
        }
        else { //User is not logged in, give them the basics
            echo '<div class="newnavbar"><div class="aftwU"><a href="https://' . $_SERVER['HTTP_HOST'] . '/login">Sign In</a> | <a href="https://' . $_SERVER['HTTP_HOST'] . '/register">Register</a> | <a href="https://' . $_SERVER['HTTP_HOST'] . '/email-resend">Email Resend</a> | <a href="/forgot-password">Forgot Password</a></div></div>'."\n";
        }
    }

    #-----------------------------------------------------------
    # Function Messages
    # checks messages for a user and
    # returns if they have any or not.
    #-----------------------------------------------------------

    private function Messages($uid){
        $query   = "SELECT COUNT(id) AS unreadMsgs FROM messages WHERE rid='".$uid."' AND viewed='1' AND sent = '0'";
        $result  = mysqli_query($conn, $query) or die('Error, query failed:' . mysqli_error());
        $row     = mysqli_fetch_array($result, MYSQL_ASSOC);
        $unreadMsgs = $row['unreadMsgs'];
        return $unreadMsgs;
    }

    private function CheckAdvancedStatus(){
        if($this->UserArray[13] == 'yes')
        {
            return '<img src="//i.animeftw.tv/green_checkmark_rounded_40x40.png" alt="" title="Your Advanced Membership is Active!" />';
        }
        else
        {
            return '<img src="//i.animeftw.tv/red_x_rounded_40x40.png" alt="" title="Your Advanced Membership is Inactive!" />';
        }
    }

}
?>
