<?php
/****************************************************************\
## FileName: stats.class.php								 
## Author: Brad Riemann								 
## Usage: Stat management class for the site.
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Stats extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		echo '<div class="body-container srow">';
		$this->statsAdminInterface();
		echo '</div>';
	}
	
	private function statsAdminInterface()
	{
		echo '
		This is the data:
		 <div id="chartdiv" style="margin-top:20px; margin-left:20px; width:850px; height:400px;"></div>
		<script type="text/javascript" src="assets/jqplot.dataAxisRenderer.min.js"></script>
		<script type="text/javascript" src="assets/jqplot.barRenderer.min.js"></script>
		<script type="text/javascript" src="assets/jqplot.categoryAxisRenderer.min.js"></script>
		<script type="text/javascript" src="assets/jqplot.pointLabels.min.js"></script>';
		
		$thirtydaysago = time()-(30*86400);
		
		$query = "SELECT `id`, `var1`, `var2` FROM  `mainaftw_stats`.`user_stats` WHERE `type` = 1 AND `var1` >= " . $thirtydaysago;
		$result = mysql_query($query);
		$data = '';
		$count = mysql_num_rows($result);
		$i = 1;
		while($row = mysql_fetch_assoc($result))
		{
			$data .= '[\'' . date("Y-m-d",$row['var1']) . ' 8:00AM\',' . $row['var2'] . ']';
			if($i < $count)
			{
				$data .= ', ';
			}
			$i++;
		}
		echo "
		<script class=\"code\" type=\"text/javascript\">
			
			$(document).ready(function(){
			  var line1=[" . $data . "];
			  var plot2 = $.jqplot('chartdiv', [line1], {
				  title:'Daily Account Signups',
				  gridPadding:{right:35},
				  axes:{
					xaxis:{
					  renderer:$.jqplot.DateAxisRenderer,
					  tickOptions:{formatString:'%b %#d, %y'},
					  min:'" . date("F d, Y", $thirtydaysago) . "',
					  tickInterval:'1 day'
					}
				  },
				  seriesDefaults: {
					showMarker:false,
					pointLabels: { show:true }
				  }
			  });
			});
			
		</script>";
	}
}