<?php
/****************************************************************\
## FileName: content.class.php								 
## Author: Brad Riemann								 
## Usage: Site Content sub class
## Copywrite 2013 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Content extends Config {

	public function __construct()
	{
		parent::__construct(TRUE);
		echo '<div  class="body-container srow">';
		$this->settingsOutput();
		echo '</div>';
	}
	
	private function settingsOutput()
	{
		if(!isset($_GET['subnode'])){
			echo '<br /><div align="center">Manage: <a href="#" onclick="$(\'#ContentStuff\').load(\'ajax.php?node=content&subnode=site-pages\'); return false;">Site Pages</a> 
			| <a href="#" onclick="$(\'#ContentStuff\').load(\'ajax.php?node=content&subnode=fansubs\'); return false;">Manage Fansubs</a> 
			| <a href="#" onclick="$(\'#ContentStuff\').load(\'ajax.php?node=content&subnode=site-groups\'); return false;">Groups</a> </div>
		<div id="ContentStuff" class="ContentStuff" style="display:hidden;"><div align="center">Choose From the Above Options.</div></div>';
		}
		else
		{
			if($_GET['subnode'] == 'site-pages')
			{
				$this->displaySitePages();
			}
			else if($_GET['subnode'] == 'fansubs')
			{
				$this->displayFansubs();
			}
			else if($_GET['subnode'] == 'site-groups')
			{
				$this->displaySiteGroups();
			}
			else
			{
				echo 'Nothing..';
			}
		}
	}
	
	private function displaySitePages()
	{
		$query = "SELECT `id`, `full_page_name` FROM `content` ORDER BY `full_page_name`";
		$results = mysql_query($query);
		
		echo '<div id="site-content-wrapper">';
		echo '<div style="margin:2px;font-size:16px;">Choose a page to Edit:</div>';
		echo '<div align="center">';
		while($row = mysql_fetch_array($results))
		{
			if(isset($_GET['page_id']) && $_GET['page_id'] == $row['id'])
			{
			}
			else
			{
			}
			echo '<a href="#" style="text-decoration:none;" class="content-link" id="' . $row['id'] . '"><div style="width:400px;display:inline-block;border:1px solid black;vertical-align:top;padding:5px;margin:2px;color:black;">' . $row['full_page_name'] . '</div></a>';
		}
		echo '</div>';
		if(isset($_GET['page_id']))
		{
			$this->pageContentForm("edit");
		}
		echo '</div>
			<script type="text/javascript">
				$(function()
				{
					$(\'#redactor\').redactor({
						focus: true
					});
					$(".content-link").on("click", function() {
						$(\'#ContentStuff\').load(\'ajax.php?node=content&subnode=site-pages&page_id=\' + $(this).attr("id"));
						return false;
					});
				});
			</script>';
	}
	
	private function displayFansubs()
	{
	}
	
	private function displaySiteGroups()
	{
	}
	
	private function pageContentForm($Type = NULL)
	{
		if($Type == "edit")
		{
			// We need to edit the page content
			$query = "SELECT `id`, `permissions`, `node`, `sub_node`, `full_page_name`, `body` FROM `content` WHERE id = " . mysql_real_escape_string($_GET['page_id']);
			$results = mysql_query($query);
			
			$row = mysql_fetch_array($results);
			$ExtraFormData = '<input type="hidden" name="method" value="EditSitePage" /><input type="hidden" name="page_id" value="' . $row['id'] . '" />';
			$FormHeader = '<div style="margin:10px 2px 2px 2px;font-size:16px;">Editing: <i>' . $row['full_page_name'] . '</i></div>';
			$permissions 		= $row['permissions'];
			$node 				= $row['node'];
			$sub_node 			= $row['sub_node'];
			$full_page_name 	= $row['full_page_name'];
			$body 				= $row['body'];
		}
		else 
		{
			$FormHeader 		= '<div style="margin:10px 2px 2px 2px;font-size:16px;">Adding a new Page to the Site.</div>';
			$permissions 		= '0,1,2,3,4,5,6,7';
			$node 				= '';
			$sub_node 			= '';
			$full_page_name 	= '';
			$body 				= '';
		}
		echo '
		<form id="site-content-form">
			' . $FormHeader . '
			<div style="width:45%;display:inline-block;margin:10px;">
				<div style="margin:5px;">
					<div style="font-size:14px;display:inline-block;">Page Name:</div>
					<div style="display:inline-block;"><input type="text" name="full_page_name" value="' . $full_page_name . '" class="text-input2" style="width:250px;" /></div>
				</div>
				<div style="margin:5px;">
					<div style="font-size:14px;display:inline-block;">Permissions:</div>
					<div style="display:inline-block;"><input type="text" name="permissions value="' . $permissions . '" class="text-input2" /></div>
				</div>
			</div>
			<div style="width:45%;display:inline-block;">
				<div style="margin:5px;">
					<div style="font-size:14px;display:inline-block;">Node:</div>
					<div style="display:inline-block;"><input type="text" name="node" value="' . $node . '" class="text-input2" /></div>
				</div>
				<div style="margin:5px;">
					<div style="font-size:14px;display:inline-block;">Sub Node:</div>
					<div style="display:inline-block;"><input type="text" name="sub_node" value="' . $sub_node . '" class="text-input2" /></div>
				</div>
			</div>
			<div style="margin:5px;">
				<div style="font-size:14px;">Body:</div>
				<textarea id="redactor" name="content">' . $body . '</textarea>
			</div>
		</form>';
	}
}