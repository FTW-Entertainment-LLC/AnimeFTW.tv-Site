<?php
include('includes/siteroot.php');
$PageTitle = 'AnimeFTW.tv Blogs | '.$siteroot.' | Your DivX Anime streaming Source!';

include('header.php');
include('header-nav.php');
$p = new AFTWpage();
if(!isset($_GET['view'])){
	$p->get_width(THEME_WIDTH);
	$p->get_message('Welcome to the new Blogs!');
	echo $p->mainTop();
	echo $p->pageMessage();
	echo $p->middleTop();
	$header = 'Welcome to AnimeFTW.tv Blogs!';
	$body = 'The Blogging Module on AnimeFTW.tv has multiple options that enhance the experience to move from just a traditional blogging software. Somce of the Features include but are not limited to:<br />
	<ul>
	<li>Post new Blogs</li>
	<li>Categorize Blog Postings</li>
	<li>Comment on Blog Postings</li>
	<li>FTW Subscriber Perks:
		<ul>
		<li>Limit Viewing of Blog postings to Friends and/or Specific users</li>
		<li>Customizations of the View of your Blog layout</li>
		</ul>
	</li>
	</ul>';
	echo $p->blankMiddleBox($header,$subline,$body);
	echo $p->middleBottom();
	echo $p->rightAd($profileArray[2]);
	$RightTitle = 'About the AFTWBlogger.';
	$RightBody = 'The Blogging system was created with you the user in mind, allow yourself to express your thoughts and opinions. <br /><br />Thank you for using another one of AnimeFTW.tv\'s Great services!<br />-AnimeFTW.tv Staff';
	echo $p->blankRightBox($RightTitle,$RightBody);
}
else {
	$u = new AFTWUser();
	$b = new AFTWBlog();
	$u->get_username($_GET['view']);
	if(isset($_GET['eid'])){
		$p->get_width(THEME_WIDTH);
		$p->get_message('You are Viewing a members single Blog post.');
		echo $p->mainTop();
		echo $p->pageMessage();
		echo $p->middleTop();
		$b->get_vars($u->returnVarName('ID'),'5','1','1','-6',$u->returnVarName('Username'));
		$b->get_bid($_GET['eid']);
		echo $b->SingleBlog();
		echo $p->middleBottom();
		echo $p->rightAd($profileArray[2]);
		echo $p->blankRightBox('Brad\'s Page','I am some random Text and I will repeat.');
	}
	else {
		$p->get_width(THEME_WIDTH);
		$p->get_message('You are Viewing a members Blog.');
		echo $p->mainTop();
		echo $p->pageMessage();
		echo $p->middleTop();
		$b->get_vars($u->returnVarName('ID'),'5','1','1','-6',$u->returnVarName('Username'));	
		echo $b->LatestBlogs();
		echo $p->middleBottom();
		echo $p->rightAd($profileArray[2]);
		echo $p->blankRightBox('Brad\'s Page','I am some random Text and I will repeat.');
	}
	
	
	/*Changable options:
	-A Blog message at the top?
	-Page title?
	-Background update?
	-Move conent around.
	-comments
	-Info about the user
	*/
}
echo $p->rightBottom();
echo $p->mainBottom();
		
include('footer.php')
?>