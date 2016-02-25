<?php
/****************************************************************\
## FileName: device.v2.class.php                                     
## Author: Brad Riemann                                         
## Usage: Device Class
## Copywrite 2016 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Device extends Config {

    public $Data, $UserID, $DevArray;

    public function __construct($Data = NULL,$UserID = NULL,$DevArray = NULL)
    {
        parent::__construct();
        $this->Data = $Data;
        $this->UserID = $UserID;
        $this->DevArray = $DevArray;
    }
    
    public function validateDevice()
    {
        // This function will validate a key given by the requestor,
        // if the key is valid, has been validated through the animeftw.tv/connect
        // portal, this will change the status to requestor claimed (2).
        
        // The statuses of a device key are:
        // - 0 - pending, seen when the key is just generated
        // - 1 - pending assignment to a device, while the device requested the key, we will not consider it done until the device has picked up the key.
        // - 2 - the device has confirmed that the key has been picked up, the requester was given the new token to use so the key is no longer in a pending state.
        if(isset($this->Data['key']) && $this->DevArray['deviceauth'] == 1) {
            // If the key is set and they are device capable let them through.
            
            // Query the database for information on the key.
            $query = "SELECT * FROM `developers_devices` WHERE `key` = '" . $this->mysqli->real_escape_string($this->Data['key']) . "'";
        } else {
        }
    }
    
    public function generateDeviceKey()
    {
        // This function will generate a device key that gets sent back to the requester.
        // the key will be all upper case hexadecimal character.
        
        // validate that the Dev can indeed work with key authentication
        if($this->DevArray['deviceauth'] == 1) {
            // the dev account is authorized to access this function.
            
            // Generate the key
            $randomKey = $this->keyGenerator();
            
            // Check to make sure the key is not in use.
            $query = "SELECT COUNT(id) FROM `developers_devices` WHERE `key` = '" . $this->mysqli->real_escape_string($randomKey) . "'";
            
            $result = $this->mysqli->query($query);
            
            $row = $result->fetch_assoc();
            
            if($row[0] < 1) {
                // There were no rows, so this key is safe to use.
                
                // insert into the database.
                $query = "INSERT INTO `" . $this->MainDB . "`.`developers_devices` (`id`, `key`, `date`, `uid`, `did`, `status`) VALUES (NULL, '" . $this->mysqli->real_escape_string($randomKey) . "', " . time() . ", 0, '" . $this->DevArray['id'] . "', 0)";
                
                $result = $this->mysqli->query($query);
                
                // return the data to the requestor.
                return array('status' => '200', 'message' => 'Request completed successfully.', 'key' => $randomKey);
                
            } else {
                // A Key existed, so let's re-run the function.
                $this->generateDeviceKey();
            }
        } else {
            return array('status' => '403', 'message' => 'Access denied to this function.');
        }
    }
    
    private function keyGenerator($length = 6)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
   
        for ($p = 0; $p < $length; $p++) {
            $result .= ($p%2) ? $chars[mt_rand(19, 23)] : $chars[mt_rand(0, 18)];
        }
    
        return $result;
    }
}