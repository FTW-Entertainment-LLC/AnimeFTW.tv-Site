<?php
/****************************************************************\
## FileName: news.v2.class.php                                     
## Author: Brad Riemann                                         
## Usage: News Class
## Copywrite 2015 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class News extends Config {

    public $Data, $UserArray, $DevArray, $permissionArray, $MessageCodes;
    private $AdvanceRestrictions;

    public function __construct($Data = NULL,$UserArray = NULL,$DevArray = NULL,$permissionArray = NULL)
    {
        parent::__construct();
        $this->Data = $Data;
        $this->UserArray = $UserArray;
        $this->DevArray = $DevArray;
        $this->permissionArray = $permissionArray;
        $this->array_buildAPICodes(); // establish the status codes to be returned to the api.
    }
    
    public function array_showLatestNews()
    {
        if(isset($this->Data['count']) && is_numeric($this->Data['count'])) {
            $count = $this->Data['count'];
        }
        else {
            $count = 8;
        }
        $query = "SELECT t.tid, t.ttitle, t.tpid, t.tfid, t.tdate, p.pbody, f.ftitle, f.fseo FROM forums_threads as t, forums_post as p, forums_forum as f 
        WHERE (t.tfid='1' OR t.tfid='2' OR t.tfid='9') AND p.pistopic='1' AND p.puid=t.tpid AND p.ptid=t.tid AND f.fid=t.tfid ORDER BY t.tid DESC LIMIT 0, " . $this->mysqli->real_escape_string($count);
        $result = $this->mysqli->query($query);
        
        $returndata = array('status' => $this->MessageCodes["Result Codes"]["200"]["Status"], 'message' => 'Request Successful.', 'count' => $count);
        $count = $result->num_rows;
        if($count > 0)
        {
            $i = 0;
            while($row = $result->fetch_assoc())
            {
                $FancyUsername = $this->string_fancyUsername($row['tpid'],NULL,NULL,NULL,NULL,NULL,TRUE,TRUE);
                $returndata['results'][$i]['poster'] = $FancyUsername[0];
                $returndata['results'][$i]['posterId'] = $row['tpid'];
                $returndata['results'][$i]['poster-avatar'] = $FancyUsername[1];
                $returndata['results'][$i]['topic-id'] = $row['tid'];
                $returndata['results'][$i]['topic-title'] = $row['ttitle'];
                $returndata['results'][$i]['forum-id'] = $row['tfid'];
                $returndata['results'][$i]['date'] = $row['tdate'];
                $returndata['results'][$i]['body'] = $row['pbody'];
                $returndata['results'][$i]['topic-link'] = 'https://www.animeftw.tv/forums/' . $row['fseo'] . '/topic-' . $row['tid'] . '/s-0';
                $i++;
            }
        }
        else
        {
        }
        return $returndata;
    }
}
