<?php
include($_SERVER['DOCUMENT_ROOT'] . "/init.php");
?>
<!doctype html>  
<html>  
<head>  
	<title><?=$config['site'];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index, follow" />
	<meta name="keywords" content="<?=$config['keywords'];?>" /> 
	<meta name="description" content="AnimeFTW.tv has free online HQ Anime Videos including Bleach, Gintama, Fairy Tail, Kiss X Sis, Bodacious Space Pirates, Sket Dance, Hunter x Hunter (2011), KenIchi: The Mightiest Disciple (2012), Space Brothers, Kore wa Zombie Desuka? Of the Dead, Sengoku Collection, Mysterious Girlfriend X, Accelerated World, Eureka Seven Ao, and Fairy Tail. All in High Quality." />
    <meta name="application-name" content="AnimeFTW.tv" />
    <meta name="msapplication-TileColor" content="#5bdaff" />
    <meta name="msapplication-TileImage" content="/images/aa92a33a-f2fa-4ace-b5b7-5a7a11b89770.png" />
    <link rel="stylesheet" href="/css/master.css" type="text/css" media="screen" /> 
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
</head>  
<body style="background:#333333;">  
	<header>
		<h1>AnimeFTW.tv</h1>
	</header>
	<nav>
		<ul class="primary-nav">
        	<li class="selected"><a href="#">Home</a></li>
        	<li><a href="#">Anime</a></li>
        	<li><a href="#">Forum</a></li>
        	<li><a href="#">Store</a></li>
        </ul>
		<ul class="usernav">
        	<li><a href="#">Prof</a></li>
        	<li><a href="#">MGMT</a></li>
        	<li><a href="#">Noti</a></li>
        	<li><a href="#">logot</a></li>
        </ul>
	</nav>
	<section id="intro">
		<header>
        	<h2>Welcome to AnimeFTW.tv</h2>
        </header>
        <p>This is text that will be setup to do nothing more than sit here and look good.</p>
	</section>
	<div id="content">
		<div id="mainContent">
			<section>
            <?php
			//include($_SERVER['DOCUMENT_ROOT'] . "/classes/db.class.php");
			$db = new db($config);
			$db->query("SELECT forums_threads.tid, forums_threads.ttitle, forums_threads.tpid, forums_threads.tdate, forums_post.pbody FROM forums_threads, forums_post WHERE forums_post.ptid = forums_threads.tid AND (forums_threads.tfid='1' OR forums_threads.tfid='2' OR forums_threads.tfid='9') AND forums_post.pistopic ORDER BY forums_threads.tid DESC LIMIT 0, 3");
			$results = $db->get();
			foreach($results as $row)
			{
				echo '<article class="blogPost">
					<header>
						<h2>' . $row['ttitle'] . '</h2>
						<p>Posted on <time datetime="' . date('c',$row['tdate']) . '">' . date('F jS y',$row['tdate']) . '</time> by ' . $row['tdate'] . ' - <a href="/forums/topic-' . $row['tid'] . '">3 replies</a></p>
					</header>
					<div>' . $row['pbody'] . '</div>
				</article>';
			}
			?>
			</section>
			<section id="comments">
				<h3>Comments</h3>
				<article>
					<header>
						<a href="#">George Washington</a> on <time datetime="2009-06-29T23:35:20+01:00">June 29th 2009 at 23:35</time>
					</header>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut.</p>
				</article>
				<article>
					<header>
						<a href="#">Benjamin Franklin</a> on <time datetime="2009-06-29T23:40:09+01:00">June 29th 2009 at 23:40</time>
					</header>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut.</p>
				</article>
				<article>
					<header>
						<a href="#">Barack Obama</a> on <time datetime="2009-06-29T23:59:00+01:00">June 29th 2009 at 23:59</time>
					</header>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut.</p>
				</article>
			</section>
			<form action="#" method="post">
				<h3>Post a comment</h3>
				<p>
					<label for="name">Name</label>
					<input name="name" id="name" type="text" required />
				</p>
				<p>
					<label for="email">E-mail</label>
					<input name="email" id="email" type="email" required />
				</p>
				<p>
					<label for="website">Website</label>
					<input name="website" id="website" type="url" />
				</p>
				<p>
					<label for="comment">Comment</label>
					<textarea name="comment" id="comment" required></textarea>
				</p>
				<p><input type="submit" value="Post comment" /></p>
			</form>
		</div>
		<aside>
			<section>
				<header>
					<h3>Categories</h3>
				</header>
				<ul>
					<li><a href="#">Lorem ipsum dolor</a></li>
					<li><a href="#">Sit amet consectetur</a></li>
					<li><a href="#">Adipisicing elit sed</a></li>
					<li><a href="#">Do eiusmod tempor</a></li>
					<li><a href="#">Incididunt ut labore</a></li>
				</ul>
			</section>
			<section>
				<header>
					<h3>Archives</h3>
				</header>
				<ul>
					<li><a href="#">December 2008</a></li>
					<li><a href="#">January 2009</a></li>
					<li><a href="#">February 2009</a></li>
					<li><a href="#">March 2009</a></li>
					<li><a href="#">April 2009</a></li>
					<li><a href="#">May 2009</a></li>
					<li><a href="#">June 2009</a></li>
				</ul>
			</section>
		</aside>
	</div>
	<footer>
		<div>
			<section id="about">
				<header>
					<h3>About</h3>
				</header>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco <a href="#">laboris nisi ut aliquip</a> ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</section>
			<section id="blogroll">
				<header>
					<h3>Blogroll</h3>
				</header>
				<ul>
					<li><a href="#">NETTUTS+</a></li>
					<li><a href="#">FreelanceSwitch</a></li>
					<li><a href="#">In The Woods</a></li>
					<li><a href="#">Netsetter</a></li>
					<li><a href="#">PSDTUTS+</a></li>
				</ul>
			</section>
			<section id="popular">
				<header>
					<h3>Popular</h3>
				</header>
				<ul>
					<li><a href="#">This is the title of a blog post</a></li>
					<li><a href="#">Lorem ipsum dolor sit amet</a></li>
					<li><a href="#">Consectetur adipisicing elit, sed do eiusmod</a></li>
					<li><a href="#">Duis aute irure dolor</a></li>
					<li><a href="#">Excepteur sint occaecat cupidatat</a></li>
					<li><a href="#">Reprehenderit in voluptate velit</a></li>
					<li><a href="#">Officia deserunt mollit anim id est laborum</a></li>
					<li><a href="#">Lorem ipsum dolor sit amet</a></li>
				</ul>
			</section>
		</div>
	</footer>
<script>
$(document).ready(function(){
	$(window).scroll(function(){
		var scrollTop = 90;
		if($(window).scrollTop() >= scrollTop){
			$('nav').css({
				position : 'fixed',
				top : '0'
			});
		}
		if($(window).scrollTop() < scrollTop){
			$('nav').removeAttr('style');	
		}
	})
})
</script>    
</body>  
</html>  