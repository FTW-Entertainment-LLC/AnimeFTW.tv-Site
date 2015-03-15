<?php

class Pagination {
    
	var $limit = 30;                                // how many per page
	var $select_what = '*';                         // what to select
	var $add_query = '';
	var $otherParams = '';
	/* customize links */
	var $first_r = 'FIRST';
	var $next_r  = '&gt;';
	var $previous_r = '&lt;';
	var $last_r = 'LAST';
    
	function getQuery ( $return_q = FALSE )
	{
		global $db;

		$sql = "SELECT SQL_CALC_FOUND_ROWS " . $this->select_what . " FROM " . $this->the_table . " " . $this->add_query . " LIMIT " . $this->start . ", " . $this->limit;
		
		$query = $db->query ( $sql );

		$nbItems = $db->Mresult ( 'SELECT FOUND_ROWS() AS nbr', 0, 'nbr' );

		if ( $nbItems > ( $this->start + $this->limit ) )
		{
			$final = $this->start + $this->limit;
		}
		else {
			$final = $nbItems;
		}
		
		if ( $return_q == FALSE )
		{
			return $nbItems;
		}
		else {
			return $sql;
		}
	}
    
    
    function paginate() {

            $nbItems = Pagination::getQuery(FALSE);
            
            if($nbItems<=$this->limit)
            {
                return;
            }
            else {
            
                $allPages = ceil($nbItems/$this->limit);
            
                $currentPage = floor($this->start/$this->limit) + 1;
            
                $pagination = "";
                if ($allPages>10) {
                    $maxPages = ($allPages>9) ? 9 : $allPages;
            
                    if ($allPages>9) {
                        if ($currentPage>=1&&$currentPage<=$allPages) {
                            $pagination .= ($currentPage>4) ? " ... " : " ";
            
                            $minPages = ($currentPage>4) ? $currentPage : 5;
                            $maxPages = ($currentPage<$allPages-4) ? $currentPage : $allPages - 4;
            
                            for($i=$minPages-4; $i<$maxPages+5; $i++) {
                                $pagination .= ($i == $currentPage) ? "<a href=\"#\" class=\"current\">".$i."</a> " : "<a href=\"?start=".(($i-1)*$this->limit).$this->otherParams."\">".$i."</a> ";
                            }
                            $pagination .= ($currentPage<$allPages-4) ? " ... " : " ";
                        } else {
                            $pagination .= " ... ";
                        }
                    }
                } else {
                    for($i=1; $i<$allPages+1; $i++) {
                        $pagination .= ($i==$currentPage) ? "<a href=\"#\" class=\"current\">".$i."</a> " : "<a href=\"?start=".(($i-1)*$this->limit).$this->otherParams."\">".$i."</a> ";
                    }
                }
            
                if ($currentPage>1)
                {
                    $pagination = "<a href=\"?start=".$this->otherParams."\">".$this->first_r."</a>"
                                . " <a href=\"?start=".(($currentPage-2)*$this->limit).$this->otherParams."\">"
                                . "".$this->previous_r."</a> ".$pagination;
                }
                if ($currentPage<$allPages)
                {
                    $pagination .= "<a href=\"?start=".($currentPage*$this->limit).$this->otherParams."\">"
                                 . "".$this->next_r."</a> <a href=\"?start=".(($allPages-1)*$this->limit).$this->otherParams."\">"
                                 . "".$this->last_r."</a>";
                }
        
            return '<div class="pages">' . $pagination . '</div>';
        }
    }
    
}
?>
