<?php
/****************************************************************\
## FileName: donate.class.php
## Author: Brad Riemann
## Usage: Donate page class
## Copyright 2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class AFTWDonate
{

    /* The premise of this script is to create a central place where we can push and show users the data they need
    #  To gain information regarding current donation runs for AnimeFTW.tv
    #  Due to our past issue with paypal, this page MUST be secured through the header for members only.
    #  Goals: Create a page, that relies on the counts from the paypal_donations table, since everytime a user donates it gets
    #          added to this table.
    #  Notes: This is to be like kickstarter, two column setup, with possiblity for tabbed content, lets think small and
    #            go with one page, right nav starts with the goal, then the progress bar, then the levels for donors, that are associated
    #          with that specific donation drive. Variables will be taken from the global settings table, then parsed out from the
    #          donation table and the table that handles the donation levels, brought together in this script.
    */

    //#- Vars -#\\
    var $profileArray, $donation_round, $donation_name, $donation_active, $donation_goal, $donation_description, $firstDay, $lastDay;

    //#- Contruct -#\\
    public function __construct()
    {
        $this->BuildGlobalVars(); // we offload the queries to make the construct less messy.. yay

        $todayDate = date('y-m-d');
        $this->firstDay = strtotime(date('Y-m-01', strtotime($todayDate)));
        $this->lastDay = strtotime(date('Y-m-t', strtotime($todayDate)));
    }

    //#- Public Functions -#\\
    public function Build($profileArray)
    {
        $this->profileArray = $profileArray;
    }

    public function Output()
    {
        $this->LeftColumn();
        echo "</td>\n";
        echo "<td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>\n";
        $this->RightColumn(); // Right column data..
        $this->UpdateViews();
    }

    public function ScriptsOutput()
    {
        $this->BuildDonatePage();
        $this->DonateJumpPage();
    }

    private function LeftColumn()
    {
        echo '<span class="scapmain">Donate to AnimeFTW.tv!</span>
            <br />
            <span class="poster">The website and all our services are completely self funded, we rely on <i>viewers like you</i> to maintain our Anime Library and keep us going strong!</span>
            </div>
            <div class="tbl">';
        echo $this->donation_description;
        echo '</div>';
    }

    private function RightColumn()
    {
        $this->ProgressBox();
        $this->DonationTiers();
        $this->donors();
    }

    private function ProgressBox()
    {
        $goal = $this->donation_goal;
        $query = mysql_query("SELECT mc_gross FROM donation_paypal WHERE `date` >= " . $this->firstDay . " AND `date` <= " . $this->lastDay);
        $total = 0;
        while (list($mc_gross) = mysql_fetch_array($query)) {
            $total = $total+$mc_gross;
        }
        $current = $total;
        $whatsleft = $goal-$current;
        if ($current == 0) {
            $percentage = 0;
        } else {
            $percentage = ($current/$goal)*100;
        }
        if ($percentage < 100) {
            $percentage = substr($percentage, 0, 2);
        } else {
            $percentage = substr($percentage, 0, 3);
        }
        //$percentage = $percentage+8;
        echo "<div class='side-body-bg'>";
        echo "<div class='scapmain'>Current Progress</div>\n";
        echo "<div class='side-body floatfix'>\n";
        echo '<div align="left">';
        $data = "<div id=\"progress-bar\" class=\"all-rounded\">\n<div id=\"progress-bar-percentage\" class=\"all-rounded\" style=\"width: $percentage%\">";
        if ($percentage > 5) { $data .= "&nbsp;$percentage%";} else {$data .= "<div class=\"spacer\">&nbsp;$percentage%</div>";}
        $data .= "</div></div>";
        echo '<span class="goal" style="margin-left:60px;font-size:22px;">Goal: </span><span class="goalamount" style="font-size:22px;font-weight:bold;color:#00a232;">$'.$goal.'</span><br />';
        echo $data;
        echo '<span class="current" style="margin-left:28px;font-size:22px;">Current: </span><span class="currentamount" style="font-size:20px;font-weight:bold;">$'.$current.'</span><br />';
        echo '<span class="current" style="font-size:22px;">Difference: </span><span class="currentamount" style="font-size:18px;font-weight:bold;">$'.$whatsleft.'</span><br />';
        echo "</div></div></div>\n";

    }

    private function DonationTiers()
    {
        if (1 ==2) {
            echo "<div class='side-body-bg'>";
            echo "<div class='scapmain'>Donation Tiers</div>\n";
            echo '<div style="font-size:10px;" align="center">(Click a box to proceed)</div>';
            $query = mysql_query("SELECT COUNT(id) FROM donation_tiers WHERE donation_round = ".$this->donation_round);
            $total = mysql_result($query, 0);
            if($total == 0){ // we need to be able to display nothing if an error occours.. gogo awesomesauce
                echo "<div class='side-body floatfix' align='center'>\n";
                echo "<h4>The donations are either closed or misconfigured.</h4>";
                echo "</div>";
            } else {
                $query = "SELECT id, name, donate, donate_limit, details FROM donation_tiers WHERE donation_round = ".$this->donation_round." ORDER BY level ASC";
                $results = mysql_query($query);
                while(list($id,$name,$donate,$donate_limit,$details) = mysql_fetch_array($results)){
                    echo '<div id="tieritem" class="floatfix">
                        <a href="#" rel="#donate" onClick="javascript:ajax_loadContent(\'donatediv\',\'/scripts.php?view=donate&id='.$id.'\');">
                        <div class="tiertitle">'.$name.'</div>
                        '.$this->CalculateDonors($donate,$donate_limit).'
                        <div class="tierdonation"><i>Donate '.$donate.' Dollars or more.</i></div>
                        <div>Prizes: <br />'.$details.'</div>
                        </a>
                    </div>';
                }
            }
            echo "</div>\n";
        } else {
            echo "<div class='side-body-bg'>";
            echo "<div class='scapmain'>Donate Today!</div>\n";
            echo "<div class='side-body floatfix'>\n";
            if ($this->profileArray[0] == 1) {
            echo '
                <div align="center" style="vertical-align:center">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <span>Donate using Paypal:</span><br />
                    <input type="hidden" name="cmd" value="_donations">
                    <input type="hidden" name="notify_url" value="https://www.ftwentertainment.com/paypal-gateway?action=ipn">
                    <input type="hidden" name="item_name" value="' . $this->donation_name . '">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="return" value="https://www.animeftw.tv/donate/thank-you">
                    <input type="hidden" name="business" value="weare@rayandfay.com">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"><br />
                    <input type="text" name="amount" value="" placeholder="Donation Amount" />
                    </form>
                </div>';
            echo '
                <div align="center" style="vertical-align:center">
                    <a href="https://www.patreon.com/bePatron?u=7723752" data-patreon-widget-type="become-patron-button">Become a Patron!</a><script async src="https://c6.patreon.com/becomePatronButton.bundle.js"></script>
                </div>
                ';
            }
            echo '
                <div align="center" style="padding-top:5px;">
                    <a href="https://cex.io/#/modal/donation/pid/DP100005408" target="_blank" ><img src="https://cex.io/img/donations/donations_button.svg" alt="Donations"></a>
                </div>
                <div>
                </div>
                ';
            echo "</div></div>\n";
        }
    }

    # function CalculateDonors
    private function CalculateDonors($donate,$dlimit = 0)
    {
        // This is basically going to check all the donation records and let us know if it falls under the specifications
        if($dlimit != 0){$limit = ' AND mc_gross <= '.$dlimit;}else{$limit = '';} //if the tier is the top tier theres nothing to compare it with, so don't give it the limit..
        $query = "SELECT `first_name` FROM `donation_paypal` WHERE `date` >= " . $this->firstDay . " AND `date` <= " . $this->lastDay;
        $result = mysql_query($query);
        $count = mysql_num_rows($result);
        if ($count > 0) {
            $i = 0; $first_name1 = '';
            while (list($first_name) = mysql_fetch_array($result)) {
                $first_name1 .= $first_name;
                if ($i < ($count-1)) {
                    $first_name1 .= ', ';
                }
                $i++;
            }
            echo '<div class="totaldonors"><b>'.$count.' Donors for this month.</b><div style="font-size:8px;">Thanks to: '.$first_name1.'</div></div>'; // return the variable so it doesnt screw everything up!
        } else {
            echo '<div class="totaldonors" align="center"><b>'.$count.' Donors for this month.</b></div>'; // return the variable so it doesnt screw everything up!
        }
    }

    # function BuildGlobalVars
    private function BuildGlobalVars()
    {
        // We check to see if donations are active.
        $query = "SELECT name, value FROM settings WHERE name = 'donation_active'";
        $result = mysql_query($query);
        $count = mysql_num_rows($result);
        if ($count == 1) {
            // We confirmed we have an active donation drive.
            $row = mysql_fetch_assoc($result);
            $query = "SELECT `goal`, `round_name`, `description` FROM `donation_settings` WHERE `active` = 1";
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);

            $this->donation_active = 1;
            $this->donation_name = $row['round_name'];
            $this->donation_goal = $row['goal'];
            $this->donation_description = $row['description'];
        } else {
            $this->donation_active = 0;
            $this->donation_name = 'None configured';
            $this->donation_goal = '0';
            $this->donation_description = 'There are currently no active donation rounds.';
        }
    }

    private function BuildDonatePage()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo 'invalid ID';
        } else {
            if (isset($_GET['step']) && $_GET['step'] == 'after') {
            } else {
                $tid = $_GET['id'];
                $query = mysql_query("SELECT name, donate, details FROM donation_tiers WHERE id = ".mysql_real_escape_string($tid));
                $row = mysql_fetch_array($query);
                echo '<form method="GET" name="fd4" id="fd4">
                <input type="hidden" name="rid" value="1">
                <br /><br /><div class="donatestep1" align="center">';
                echo '<label class="left" for="amount" style="margin: 0px 0px 0px 0px;color:#555555;">Donation Amount:</label>
                    <input name="amount" id="amount" type="text" class="donateForm" value="'.$row['donate'].'.00" /><br />
                    <div style="color:#555555;text-size:6px;">(or More!)</div><br />';
                echo '</div><br /><br />';
                echo '<div class="donatestep2" align="center">';
                echo '<label class="left" for="price" style="margin: 0px 0px 0px 0px;color:#555555;">Prizes for a Donation:</label>';
                echo '<div class="pricedetails">'.$row['details'].'</div>';
                echo '</div><br /><br />';
                echo '<div class="donatestep2">';
                echo '<div align="center" style="color:#5A5655;"><i>Just a friendly reminder<br /> AnimeFTW.tv is completely crowd funded, we rely on your donations to expand and exist. <br />Without our fans and our members, we wouldn\'t be able to share the greatness and the fun that anime is!</i></div>';
                echo '</div><br />';
                echo '<div align="center" class="donate_button"><br /><a href="#" onclick="ajax_loadContent(\'donatediv\',\'https://www.animeftw.tv/scripts.php?view=donate&id='.$tid.'&step=after&random=sometime\' + getFormElementValuesAsString(document.forms[\'fd4\'])); return false;">Continue to Choose the method of Donation!</a></div>
                    </form>';
            }
        }
    }

    private function DonateJumpPage()
    {
        if (!isset($_GET['step']) || $_GET['step'] != 'after') {
        } else {
            //echo $_SERVER['REQUEST_URI'];
            $tid = $_GET['id'];
            if (!isset($tid)) {
                echo 'error';
            } else {
                $query = mysql_query("SELECT name, details FROM donation_tiers WHERE id = ".mysql_real_escape_string($tid));
                $row = mysql_fetch_array($query);
                echo '<br /><div class="donatestep2" align="center"><label class="left" for="amount" style="margin: 0px 0px 0px 0px;color:#555555;">Confirmation:</label>';
                echo '<div class="pricedetails" align="left">You have chosen to donate $<b>'.$_GET['amount'].'</b>, the prize package is <b>'.$row['name'].'</b>, it entails the following:<br />'.$row['details'].'</div>';
                echo '</div><br />';
                echo '<div class="donatestep2" align="center">Below are the current supported methods for donations/transactions. At the time of creation, FTW Entertainment LLC only supports two methods, Paypal and Google Wallet. If you cannot donate due to location restrictions and/or unsupported CC\'s you CAN donate via snail mail, any interested parties are encouraged to pm <a href="https://www.animeftw.tv/pm/compose/1">robotman321</a></div>';
                echo '
                <table width="95%">
                <tr>
                <td width="50%">
                <div align="center">
                <h4>Donate using Google Wallet:</h4><form action="https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/456133132125502" id="BB_BuyButtonForm" method="post" name="BB_BuyButtonForm" target="_top">
                    <input name="item_name_1" type="hidden" value="Zeus 2.0 Server Drive"/>
                    <input name="item_description_1" type="hidden" value=""/>
                    <input name="item_quantity_1" type="hidden" value="1"/>
                    <input name="item_price_1" type="hidden" value="'.$_GET['amount'].'"/>
                    <input name="item_currency_1" type="hidden" value="USD"/>
                    <input name="shopping-cart.items.item-1.digital-content.key" type="hidden" value="wV6ez3FK/WUTuAhNsh5+NsU5txukR7oi4mGRLMH4OM4="/>
                    <input name="shopping-cart.items.item-1.digital-content.key.is-encrypted" type="hidden" value="true"/>
                    <input name="shopping-cart.items.item-1.digital-content.url" type="hidden" value="http://www.aniemftw.tv"/>
                    <input name="_charset_" type="hidden" value="utf-8"/>
                    <input alt="" src="//i.animeftw.tv/google-wallet.png" type="image"/>
                </form>
                </div>
                </td>
                <td>
                <div align="center">
                <h4>Donate using Paypal:</h4>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_donations">
                <input type="hidden" name="notify_url" value="https://ftwentertainment.com/members/paypal.php?action=ipn">
                <input type="hidden" name="amount" value="'.$_GET['amount'].'">
                <input type="hidden" name="item_name" value="Zeus 2.0 Server Drive">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="return" value="https://www.animeftw.tv/donate/thank-you">
                <input type="hidden" name="business" value="brad@ftwentertainment.com">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>
                </div>
                </td>
                </tr>
                </table>
                ';
            }
        }
    }

    private function UpdateViews()
    {
        mysql_query("UPDATE donation_settings SET views = views+1 WHERE round_id = ".$this->donation_round);
    }

    private function donors()
    {
        echo "<div class='side-body-bg'>";
        echo "<div class='scapmain'>This Month's Donors!</div>\n";
        echo "<div class='side-body floatfix'>\n";
        $this->CalculateDonors(0);
        echo '</div></div>';
    }
}

?>
