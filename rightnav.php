<div id="right">
			<div>
            <p><b>AnimeFTW.tv Site Statistics</b></p>
                <div>
                <hr width="95%" color="#CCCCCC" />
                <?
                $query = mysql_query("SELECT * FROM users WHERE Active='1'"); 
					$total_users = mysql_num_rows($query) or die("Error: ". mysql_error(). " with query ". $query);
					
					$query2 = mysql_query("SELECT * FROM episode"); 
					$total_episodes = mysql_num_rows($query2) or die("Error: ". mysql_error(). " with query ". $query2);
					$full_total_episodes = $total_episodes;
					
					if($PermissionLevelAdvanced == 0)
					{
						$aonly = "AND aonly='0'";
					}
					else if ($PermissionLevelAdvanced == 3)
					{
						$aonly = "AND aonly<='1'";
					}
					else
					{
						$aonly = '';
					}
					$query3 = mysql_query("SELECT * FROM series WHERE active='yes' AND moviesOnly='0' ".$aonly.""); 
					$total_series = mysql_num_rows($query3) or die("Error: ". mysql_error(). " with query ". $query3);
					
					$query4 = mysql_query("SELECT * FROM page_comments"); 
					$total_comments = mysql_num_rows($query4) or die("Error: ". mysql_error(). " with query ". $query4);
					
					$query5 = mysql_query("SELECT * FROM episode_tracker"); 
					$total_tracked_eps = mysql_num_rows($query5) or die("Error: ". mysql_error(). " with query ". $query5);
					
					$minutes_of_total_eps = $full_total_episodes*24;
					$length_of_total_eps = ($full_total_episodes*24)/60;
					$size_of_videos = substr((($full_total_episodes*115)/1024), 0, 5);
					echo '
					<ul>
					<li>'.$total_series.' Series.</li>
					<li>'.$full_total_episodes.' Episodes Online.</li>
					<li>'.$total_users.' Registered users.</li>
					<li>'.$total_tracked_eps.' Episodes Tracked.</li>
					<li>'.$total_comments.' Comments.</li>
					<li>'.$minutes_of_total_eps.' minutes of video.</li>
					<li>'.$length_of_total_eps.' hours of videos.</li>
					<li>'.$size_of_videos.' GB of video.</li>
					</ul>';
					?>
                </div>
            </div>
			<div>
            <p><b>AnimeFTW.tv Site Statistics</b></p>
                <div>
                <hr width="95%" color="#CCCCCC" />
                <a href="#" onclick="document.getElementsByName('darkBackgroundLayer')[0].style.display='';document.getElementById('videolayer').style.display='';return false;"> Click here to activate the dark side</a>
                Test #2 - Row1<br />
                Test #2 - Row2<br />
                Test #2 - Row3<br />
                Test #2 - Row4<br />
                Test #2 - Row5<br />
                Test #2 - Row6<br />
                Test #2 - Row7<br />
                Test #2 - Row8<br />
                </div>
            </div>
            <?
			if($profileArray[2] == 0 || $profileArray[2] == 3)
			{
				?>
			<div class="boxtop"></div>
			<div class="box">
				<p><b>Advertisement/Games</b><br />
				<!-- Insticator API Embed Code -->
				<div id="insticator-container">
				<link rel="stylesheet" href="https://embed.insticator.com/embedstylesettings/getembedstyle?embedUUID=693d677f-f905-4a76-8223-3ed59a38842d">
				<div id="div-insticator-ad-1"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-1");</script></div>
				<div id="insticator-embed">
				<div id='insticator-api-iframe-div'><script>(function (d) {var id='693d677f-f905-4a76-8223-3ed59a38842d',cof = 1000 * 60 * 10,cbt = new Date(Math.floor(new Date().getTime() / cof) * cof).getTime(),js = 'https://embed.insticator.com/assets/javascripts/embed/insticator-api.js?cbt='+cbt, f = d.getElementById("insticator-api-iframe-div").appendChild(d.createElement('iframe')),doc = f.contentWindow.document;f.setAttribute("id","insticatorIframe"); f.setAttribute("frameborder","0"); doc.open().write('<script>var insticator_embedUUID = \''+id+'\'; var insticatorAsync = true;<\/script><body onload="var d = document;d.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + js + '\'" >');doc.close(); })(document);</script><noscript><a href="https://embed.insticator.com">Please enable JavaScript.</a></noscript></div>
				</div>
				<div id="div-insticator-ad-2"><script type="text/javascript">Insticator.ad.loadAd("div-insticator-ad-2");</script></div>
				</div>
				<!-- End Insticator API Embed Code -->
             </div>
             <?
			}
			else {
				echo "Dont know what happened here..";
			}
             ?>
           <div class="boxtop"></div>
			<div class="box">
				<p><b>Site Statistics</b><br /><div align="center">
                </div></p></div>
			</div>
			<?
			/*<div class="boxtop"></div>
			<div class="box">
				<p><img src="images/image.gif" alt="Image" title="Image" class="image" /><b>Lorem ipsum dolor sit amet</b><br />consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis.<br /></p>
				<div class="buttons"><p><a href="#" class="bluebtn">Read</a> <a href="#" class="greenbtn">Mark</a></p></div>
			</div>*/ 
			?>
		</div>