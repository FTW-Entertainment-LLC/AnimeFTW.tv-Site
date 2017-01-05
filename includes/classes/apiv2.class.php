<?php 
/****************************************************************\
## FileName: apiv2.class.php                                 
## Author: Brad Riemann                                     
## Usage: Central API management Class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
## Modified: 12/08/2013
## Version: 1.5.0
\****************************************************************/

Class api extends Config {
    
    protected $Method, $Style, $Data = array(), $UserID, $AccessLevel;
    var $username, $password, $DevArray, $MessageCodes;
    
    private $APIActions = array(
        'display-episodes' => array(
            'action' => 'display-episodes',
            'location' => 'episode.v2.class.php', // action location
            'classname' => 'Episode', // class name
            'method' => 'array_displayEpisodes', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Displays all of the episodes for a series with a given id, must have the id of the series to view all episodes.'
        ),
        'record-episode' => array(
            'action' => 'record-episode',
            'location' => 'episode.v2.class.php', // action location
            'classname' => 'Episode', // class name
            'method' => 'array_recordEpisodeTime', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Records the current time in seconds that an episode is currently at. Useful for starting a video at a certain point.'
        ),
        'display-single-episode' => array(
            'action' => 'display-single-episode',
            'location' => 'episode.v2.class.php', // action location
            'classname' => 'Episode', // class name
            'method' => 'array_displaySingleEpisode', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Display a single Episode, displays all of the details to a single episode. Requires an episode id to complete the action.'
        ),
        'display-series' => array(
            'action' => 'display-series',
            'location' => 'series.v2.class.php', // action location
            'classname' => 'Series', // class name
            'method' => 'array_displaySeries', // method name
            'disabled' => '0',
            'loginRequired' => '0',
            'description' => 'Displays X series in Alphabetic order, will start A-Z with the first 20 series.'
        ),
        'display-single-series' => array(
            'action' => 'display-single-series',
            'location' => 'series.v2.class.php', // action location
            'classname' => 'Series', // class name
            'method' => 'array_displaySingleSeries', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Displays a single series, you must have the id of the series in order to use it.'
        ),
        'display-movies' => array(
            'action' => 'display-movies',
            'location' => 'episode.v2.class.php', // action location
            'classname' => 'Episode', // class name
            'method' => 'array_displayMovies', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'Displays only Movies from the episode listing.'
        ),
        'display-tag-cloud' => array(
            'action' => 'display-tag-cloud',
            'location' => 'series.v2.class.php', // action location
            'classname' => 'Series', // class name
            'method' => 'array_displayTagCloud', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Display the tag cloud.'
        ),
        'search' => array(
            'action' => 'search',
            'location' => 'search.v2.class.php', // action location
            'classname' => 'Search', // class name
            'method' => 'array_siteSearch', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'Search the site for a series or a video.'
        ),
        'display-comments' => array(
            'action' => 'display-comments',
            'location' => 'comments.v2.class.php', // action location
            'classname' => 'Comment', // class name
            'method' => 'array_displayComments', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Display comments for a video or profile.'
        ),
        'add-comment' => array(
            'action' => 'add-comment',
            'location' => 'comments.v2.class.php', // action location
            'classname' => 'Comment', // class name
            'method' => 'array_addComment', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Add a comment to a video'
        ),
        'logout' => array(
            'action' => 'logout',
            'location' => 'user.v2.class.php', // action location
            'classname' => 'User', // class name
            'method' => 'logoutUser', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'Logs out the user, destroys the Token'
        ),
        'register' => array(
            'action' => 'register',
            'location' => 'register.v2.class.php', // action location
            'classname' => 'Register', // class name
            'method' => 'registerUser', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'Register an account with AnimeFTW.tv'
        ),
        'display-reviews' => array(
            'action' => 'display-reviews',
            'location' => 'review.v2.class.php', // action location
            'classname' => 'Review', // class name
            'method' => 'array_displayReviews', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Display reviews for a specific Series.'
        ),
        'add-review' => array(
            'action' => 'add-review',
            'location' => 'review.v2.class.php', // action location
            'classname' => 'Review', // class name
            'method' => 'array_addReview', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Add a Review for a specific Series.'
        ),
        'random-series' => array(
            'action' => 'random-series',
            'location' => 'series.v2.class.php', // action location
            'classname' => 'Series', // class name
            'method' => 'array_displaySeries', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Display X Random Series.'
        ),
        'latest-news' => array(
            'action' => 'latest-news',
            'location' => 'news.v2.class.php', // action location
            'classname' => 'News', // class name
            'method' => 'array_showLatestNews', // method name
            'disabled' => '0',
            'loginRequired' => '0',
            'description' => 'Shows the Latest News for the site.'
        ),
        'top-series' => array(
            'action' => 'top-series',
            'location' => 'toplist.v2.class.php', // action location
            'classname' => 'toplist', // class name
            'method' => 'array_showTopAnime', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Shows the top X series in the listing'
        ),
        'rate-episode' => array(
            'action' => 'rate-episode',
            'location' => 'rating.v2.class.php', // action location
            'classname' => 'Rating', // class name
            'method' => 'bool_submitEpisodeRating', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'Rate an episode 1-5 stars.'
        ),
        'display-profile' => array(
            'action' => 'display-profile',
            'location' => 'user.v2.class.php', // action location
            'classname' => 'User', // class name
            'method' => 'array_dispayUserProfile', // method name
            'disabled' => '0',
            'loginRequired' => '1',
            'description' => 'View the full profile of a user.'
        ),
        'edit-profile' => array(
            'action' => 'edit-profile',
            'location' => 'user.v2.class.php', // action location
            'classname' => 'User', // class name
            'method' => 'array_editUserProfile', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Submit Edits to a Users profile.'
        ),
        'display-categories' => array(
            'action' => 'display-categories',
            'location' => 'series.v2.class.php', // action location
            'classname' => 'Series', // class name
            'method' => 'array_displayCategories', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Displays available Categories.'
        ),
        'display-watchlist' => array(
            'action' => 'display-watchlist',
            'location' => 'watchlist.v2.class.php', // action location
            'classname' => 'Watchlist', // class name
            'method' => 'array_displayWatchList', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Displays available My WatchList Entries.'
        ),
        'add-watchlist' => array(
            'action' => 'add-watchlist',
            'location' => 'watchlist.v2.class.php', // action location
            'classname' => 'Watchlist', // class name
            'method' => 'array_addWatchListEntry', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Adds a watchlist entry to a users account.'
        ),
        'delete-watchlist' => array(
            'action' => 'delete-watchlist',
            'location' => 'watchlist.v2.class.php', // action location
            'classname' => 'Watchlist', // class name
            'method' => 'array_deleteWatchListEntry', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Delete a watchlist entry to a users account.'
        ),
        'edit-watchlist' => array(
            'action' => 'edit-watchlist',
            'location' => 'watchlist.v2.class.php', // action location
            'classname' => 'Watchlist', // class name
            'method' => 'array_editWatchListEntry', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Edit a watchlist entry to a users account.'
        ),
        'app-settings' => array(
            'action' => 'app-settings',
            'location' => 'settings.v2.class.php', // action location
            'classname' => 'Settings', // class name
            'method' => 'array_displayAppSettings', // method name
            'disabled' => '1',
            'loginRequired' => '1',
            'description' => 'Shows Application level settings.'
        ),
        'validate-device' => array(
            'action' => 'validate-device',
            'location' => 'device.v2.class.php', // action location
            'classname' => 'Device', // class name
            'method' => 'validateDevice', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'Validates a device with a given input'
        ),
        'generate-device-key' => array(
            'action' => 'generate-device-key',
            'location' => 'device.v2.class.php', // action location
            'classname' => 'Device', // class name
            'method' => 'generateDeviceKey', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'Generates a Device Key for use on non-account applications.'
        ),
        'loginRequired' => array(
            'action' => 'loginRequired',
            'location' => 'register.v2.class.php', // action location
            'classname' => 'Device', // class name
            'method' => 'registerUser', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'No login attempt, so we assume this was a registration.'
        ),
        'login' => array(
            'action' => 'login',
            'location' => 'register.v2.class.php', // action location
            'classname' => 'Device', // class name
            'method' => 'registerUser', // method name
            'disabled' => '1',
            'loginRequired' => '0',
            'description' => 'No login attempt, so we assume this was a registration.'
        ),
    );

    // class constructor method
    public function __construct()
    {
        // import the functions from the parent class.
        parent::__construct();
        
        // Scripts..
        $this->array_buildAPICodes(); // establish the status codes to be returned to the api.
        $this->determineInput(); // this will figure out if they are using POST or GET requests
        $this->determineStyle(); // This will figure out if it will be JSON or XML output
        $this->launchAPI(); // This is the main method for the API, after we have done everything else we need to `initialize` the script.
        $this->recordGoogleAnalytics(); // sends a post request to google analytics for the requested page.
    }
    
    // function will determine what style it is, AND will also validate to make sure an application key is given
    private function determineInput()
    {
        $input = Array();
        // loop through the POST data, adding it to the input array.
        foreach($_POST as $key => $value)
        {
            $input[$key] = $value;
        }
        // Look through the GET data, adding it to the input array.
        foreach($_GET as $key => $value)
        {
            $input[$key] = $value;
        }
        // This array will contain both POST and GET data.
        // TODO: A way to handle duplicates, possibly responses back to client 
        // Letting them know they can only submit one variable per request.
        $this->Data = $input;
    }
    
    // function will determine if the developer is requesting JSON or XML output. (We can expand later.)
    private function determineStyle()
    {
        // we check, is there an output format request, is it json?
        if(isset($this->Data['output']) && strtolower($this->Data['output'] == 'json'))
        {
            $this->Style = 'json';
        }
        // since there was nothing for Json, we cannot assume the dev asked for it, we need to see if they want xml
        elseif(isset($this->Data['output']) && strtolower($this->Data['output'] == 'xml'))
        {
            $this->Style = 'xml';
        }
        // for everything else we default to json, cause it looks nicer..
        else
        {
            $this->Style = 'json';
        }
    }
    
    // Part of the API includes error reporting to the developers, this will need to output formats that 
    // are known by the Devs.
    private function reportResult($ResultCode = 401,$Message = NULL)
    {
        if($Message == NULL)
        {
            // Message is null, which means we can take it from the array
            $Message = $this->MessageCodes["Result Codes"][$ResultCode]["Message"];
        }
        else
        {
            // Message was given, so we need to use THAT.
            $Message = $Message;
        }
        $Result = array('status' => $this->MessageCodes["Result Codes"][$ResultCode]["Status"], 'message' => $Message); // we put the error and the message together for the output..
        $this->formatData($Result); // format the data how the client is looking for..
        exit; // we exit the script so it doesnt keep trying to go forward.
    }    
    
    // function will take an array of data and output to the format requested by the developer
    private function formatData($data)
    {
        // we have to check the data to make sure its an array
        if(is_array($data))
        {
            // Check if the dev is supposed to have dashes in his keys or not..
            if($this->DevArray['dashes'] == 1){
                $data = $this->replaceArrayKeyDashes($data);
            }
            if($this->Style == 'xml')
            {
                foreach($data as $column => $value)
                {
                    echo '<' . $column . '><![CDATA[' . stripslashes($value) . ']]></' . $column . '>';
                }
            }
            // JSON output
            else
            {
                echo json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            }
            
        }
        else
        {
            // the aw crap option.. the array wasnt an array so we have to halt everything!
            $this->reportResult(400);
        }
    }
    
    // Function to validate the requested action to ensure if it is something a non-logged-in person can view.
    private function validateAction() {
        // This function validates an action.
        if (isset($this->APIActions[$this->Data['action']]) || array_key_exists($this->Data['action'], $this->APIActions)) {
            // Action exists that was requested, now we check to see if the function is allowed for their current state.
            if (isset($this->Data['token']) && $this->tokenAuthorization('validate') == TRUE) {
                // Token is supplied and is active.
                return TRUE;
            } else {
                // The token may be supplied, or not, but it is not a valid login.
                // We check if te action can be used by non-logged in persons.
                if ($this->APIActions[$this->Data['action']]['loginRequired'] == 0) {
                    // The action has validity for non-logged-in persons, so let them proceed.
                    return TRUE;
                } else {
                    // This function is a login only function
                    return FALSE;
                }
            }
        } else {
            // Function does not exist, however the end user won't know that.
            return FALSE;
        }
    }
    
    // the `final frontier` for the API script, this will get things rolling.
    private function launchAPI()
    {
        // 1) check devkey, verify that its valid.
        // 2) validate the token for the user
        // 3) launch into the sub routines of the API.
        
        // we validate the developer key, if they pass go, they collect 200 bits
        if($this->validateDevKey() == TRUE) {
            // UPDATED: 04/01/2017
            // We change from a login only system to allow ALL of the functions to work, we will then authenticate AFTER the action is requested (or not)
            if (isset($this->Data['action'])) {
                // There is an action defined, so we treate it as a valid first response that we will attempt to make sure the system can work with.
                if ($this->validateAction() == TRUE) {
                    // Action is valid, or they can reach it.
                    if ($this->Data['action'] == 'login' || $this->Data['action'] == 'loginRequired') {
                        // It is theoretically possible that someone enters in the action of login, as such we need to ensure that they CAN login.
                        $this->tokenAuthorization('create');
                    } else {
                        // With the token approved, we start the api fully.
                        $this->launchAPISubFunctions();
                    }
                } else {
                    // Action supplied is either not valid or requires a login to access, return a 405 error, indicates they need authentication.
                    $this->reportResult(405);
                }
            } else {
                // The action is not set, so we assume they want to login since there are no other valid options without an action.
                $this->tokenAuthorization('create'); // we need to create a token for this user
            }
        } else {
            // wrong, good bye..
            $this->reportResult(403);
        }
        // Record ALL hits in the api logs.
        $this->RecordDevLogs();
    }
    
    // function will validate the developer key
    private function validateDevKey()
    {
        $DevKey = $this->Data['devkey']; // declare the developer key from the dataset
        $query = "SELECT `id`, `devkey`, `name`, `info`, `frequency`, `uid`, `ads`, `license`, `deviceauth`, `dashes` FROM `" . $this->MainDB . "`.`" . $this->DevTable . "` WHERE `devkey` = '" . $this->mysqli->real_escape_string($DevKey) . "'";
        
        $result = $this->mysqli->query($query);
        
        $count = mysqli_num_rows($result);
        
        // if there are no results, throw an error.
        if($count < 1)
        {
            return FALSE; // There was no match.. return a false..
        }
        else
        {
            $row = $result->fetch_assoc();
            $this->DevArray = array(
                'id' => $row['id'], 
                'devkey' => $row['devkey'], 
                'name' => $row['name'], 
                'info' => $row['info'], 
                'frequency' => $row['frequency'], 
                'uid' => $row['uid'], 
                'ads' => $row['ads'], 
                'license' => $row['license'],
                'deviceauth' => $row['deviceauth'],
                'dashes' => $row['dashes'],
            );
            return TRUE; // Return TRUE to let it continue on.
        }
    }
    
    // Token validation and authorization function, options, `create` and `validate`
    private function tokenAuthorization($Type = 'create')
    {
        if($Type == 'validate')
        {
            // We need to validate the token given to us
            if(isset($this->Data['token']))
            {
                // this will need some sanitization...
                $query = "SELECT `" . $this->TokenTable . "`.`id`, `" . $this->TokenTable . "`.`session_hash`, `" . $this->TokenTable . "`.`date`, `" . $this->TokenTable . "`.`did`, `" . $this->TokenTable . "`.`uid`, `users`.`Level_access` FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "`, `" . $this->MainDB . "`.`users` WHERE `" . $this->TokenTable . "`.`session_hash` = '" . $this->Data['token'] . "' AND `" . $this->TokenTable . "`.`did` = '" . $this->DevArray['id'] . "' AND `users`.`ID`=`" . $this->TokenTable . "`.`uid`";
                $results = $this->mysqli->query($query);
                $count = mysqli_num_rows($results);
                if($count < 1)
                {
                    // no rows found, they need to go back and try again.
                    return FALSE;
                }
                else
                {
                    $row = $results->fetch_assoc();
                    $this->UserID         = $row['uid'];             // Userid of the user needed later in life
                    $this->AccessLevel    = $row['Level_access'];    // Level access of the logged in user.
                    // w00t, this is a success, update the date on the row, to make sure.
                    $results = $this->mysqli->query("UPDATE `" . $this->MainDB . "`.`" . $this->TokenTable . "` SET `date` = '" . time() . "' WHERE `session_hash` = '" . $this->mysqli->real_escape_string($this->Data['token']) . "' AND did = '" . $this->DevArray['id'] . "'");
                    // return true to the system so that it can continue on.
                    return TRUE;
                }
            }
            else
            {
                // token was not set, how they got here I dont know..
                return FALSE;
            }
        }
        else
        {
            // Since there are only two functions we, we assume they must be wanting to create a session.
            $validateLogin = $this->validateUser();
            if($validateLogin[0] == TRUE && $validateLogin[1] == 200)
            {
                // the authentication was correct, which means the user can log in, we need to create the token,
                // and hand it back to the developer.
                //$this->createToken();
                $this->formatData($this->createToken($this->Data,$this->DevArray,$this->UserID));
            }
            else if($validateLogin[0] == FALSE && $validateLogin[1] == 403){
                # User is there, but not active.
                $this->reportResult(403,"The User account is not active, please activate before logging in.");
            }
            else
            {
                // credentials were NOT correct, they need to try again.
                $this->reportResult(406);
            }
        }
    }
    
    // validates the user so as to verify that the login credentials are indeed correct.
    private function validateUser()
    {
        // the following were needed for portability to the config class..
        $this->username = $this->Data['username'];
        $this->password = $this->Data['password'];
        
        // We need to validate the user, see if it works for us.
        $UserValidation = $this->array_validateAPIUser($this->Data['username'],$this->Data['password']);
        
        // validate the user logins given to us.
        if($UserValidation[0] == TRUE && $UserValidation[2] == 1){
            $this->UserID = $UserValidation[1]; // we need to make the userid be a global for later usage..
            return array(TRUE,200,'Login successful.');
        }
        else if($UserValidation[0] == TRUE && $UserValidation[2] == 0) {
            return array(FALSE,403,'The User Account is not Active, please activate the account to login.');
        }
        else if($UserValidation[0] == TRUE && $UserValidation[2] == 2) {
            return array(FALSE,402,'The User account has been suspended.');
        }
        else
        {
            // no users turned up anywhere.
            return array(FALSE,404,'The user is unknown.');
        }
    }
    
    // destroys the token of the currently logged in user.
    private function destroyToken()
    {
        // build the query to delete the table.
        $query = "DELETE FROM `" . $this->MainDB . "`.`" . $this->TokenTable . "` WHERE `session_hash` = '" . $this->Data['token'] . "'";
        $result = $this->mysqli->query($query);
        // let them know it was a success!
        $this->formatData(array('status' => '200', 'message' => 'User logged out Successfully.'));
    }
    
    // after the user has been authenticated via the token. We need to parse through the available options
    // that the dev could push through the app, this function jumps to all of those.
    private function launchAPISubFunctions()
    {
        if(isset($this->Data['action']) && $this->Data['action'] == 'result-codes')
        {
            $this->formatData($this->MessageCodes);
        }
        else if(isset($this->Data['action']) && $this->Data['action'] == 'available-actions')
        {
            //$this->formatData($this->APIActions);
            //print_r($this->APIActions);
            foreach($this->APIActions AS $Action)
            {
                if($Action['disabled'] == 0)
                {
                    $array = array();
                    $array[$Action['action']]['action'] = $Action['action'];
                    $array[$Action['action']]['description'] = $Action['description'];
                    print_r($array);
                    unset($array);
                }
            }
        }
        else if(isset($this->Data['action']) && $this->Data['action'] == 'logout')
        {
            // we will now destroy the token, causing the user to log out.
            $this->destroyToken();
        }
        else if(isset($this->Data['action']) && $this->Data['action'] == $this->APIActions[$this->Data['action']]["action"] && $this->Data['action'] != '')
        {
            include("includes/classes/" . $this->APIActions[$this->Data['action']]["location"]);
            
            $C = new $this->APIActions[$this->Data['action']]["classname"]($this->Data,$this->UserID,$this->DevArray,$this->AccessLevel);
            $Method = $this->APIActions[$this->Data['action']]["method"];
            $this->formatData($C->$Method());
        }
        else
        {
            // The default action/no action will check the token and give a simple response back stating that it is still active.
            $this->reportResult(500,"The action was unknown.");
        }
    }
    
    // record the functions accessed as well as the details of the request
    private function RecordDevLogs()
    {
        $Data = array();
        // we need to loop through the data, if there is a password string we replace it with stars.
        foreach($this->Data as $key => $value) {
            if($key == 'password') {
                $len = strlen($value);
                $value = substr($value, 0,1). str_repeat('*',$len - 2) . substr($value, $len - 1 ,1);
            }
            $Data[$key] = $value;
        }
        $Data = json_encode($Data);
        
        if(array_key_exists("id",$this->DevArray)){
            $devid = $this->DevArray['id'];
        }
        else {
            $devid = 0;
        }
        
        if(isset($this->UserID)){
            // if the user id is set, then they are logged in with a valid token.
            // update the last activity.
            $query = 'UPDATE users SET lastActivity=\''.time().'\' WHERE ID=\'' . $this->UserID . '\'';
            $this->mysqli->query($query);
            // build the query to insert into the dev logs.
            $query = "INSERT INTO developers_logs (`date`, `did`, `uid`, `agent`, `ip`, `url`)
VALUES ('".time()."', '" . $devid . "', '" . $this->UserID . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . json_encode($Data) . "')";
        }
        else {
            $query = "INSERT INTO developers_logs (`date`, `did`, `uid`, `agent`, `ip`, `url`)
VALUES ('".time()."', '" . $devid . "', '0', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $Data . "')";
        }
        // execute the query, adding the request to the dev logs.
        $this->mysqli->query($query);
    }
    
    private function recordGoogleAnalytics(){
        // This function was designed just to throw a post notification over to google's analytics servers. 
        // It allows us to keep track of traffic hitting the api.
        
        // first we parse through the data, we don't want to send username or passwords to google.
        $dp = '/api/v2';
        // first we add the developer key..
        if(isset($this->Data['devkey'])){
            $dp .= '/' . $this->Data['devkey'];
        }
        // then add the action
        if(isset($this->Data['action'])){
            $dp .= '/' . $this->Data['action'];
        }
        else if(!isset($this->Data['action']) && (isset($this->Data['username']) && isset($this->Data['password']) && !isset($this->Data['email']))){
            // there is no action but they are logging in.
            $dp .= '/login';
        }
        // if we defined an ID, let's allow it to be passed through.
        if(isset($this->Data['id'])){
            $dp .= '/' . $this->Data['id'];
        }
        // More will be added later, but for now this gives us even more incite.
        /*foreach($this->Data as $key => &$value){
            if(strtolower($key) == 'password'){
            }
            else {
                $dp .= '/' . $key;
            }
        }*/
        $url = 'https://www.google-analytics.com/collect';
        $myvars = array(
            'v' => 1,
            'tid' => 'UA-6243691-1',
            'cid' => $this->Data['token'],
            't' => 'pageview',
            'dh' => 'www.animeftw.tv',
            'dp' => $dp,
            'an' => $this->DevArray['name'],
            'uip' => $_SERVER['REMOTE_ADDR'],
            'ua' => $_SERVER['HTTP_USER_AGENT'],
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST,TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($myvars));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        
        curl_close($ch);
    }
}
