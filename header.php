<?php
//session_start();
if($_SERVER['PHP_SELF'] == '/request.php')
{
	ob_start(); //Needed so i can use header to change location, to find the correct page for a specific request.
}
	$profileArray = $Config->outputUserInformation();
	  // only set this if you wish to set a login only policy
	if($_SERVER['REQUEST_URI'] == '/user' || $_SERVER['REQUEST_URI'] == '/user/')
	{
		if($profileArray[0] == 0)
		{
			header('location: https://'.$_SERVER['HTTP_HOST'].'/login');
			exit;
		}
		else 
		{
			header('location: https://'.$_SERVER['HTTP_HOST'].'/user/'.$profileArray[5]);
			exit;
		}
	}
	if($_SERVER['SERVER_PORT'] == 80 && $profileArray[14] == 1 && $profileArray[2] != 3)
	{
		header('location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}
	if($_SERVER['PHP_SELF'] == '/videos.php' && $_SERVER['SERVER_PORT'] == 443 && ($profileArray[2] == 3 || $profileArray[13] == 0))
	{
		header('location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	}
	if($_SERVER['HTTP_HOST'] == 'animeftw.com' || $_SERVER['HTTP_HOST'] == 'www.animeftw.com' || $_SERVER['HTTP_HOST'] == 'www.animeftw.net' ||  $_SERVER['HTTP_HOST'] == 'animeftw.net'){
		header("location: http://www.animeftw.tv".$_SERVER['REQUEST_URI']);
		exit;
	}
	if($_SERVER['REMOTE_ADDR'] == '202.156.10.227'){
		header("location: http://www.google.com");
		exit;
	}
	if($_SERVER['REQUEST_URI'] == '/donate' || $_SERVER['REQUEST_URI'] == '/donate/'){
		if($profileArray[0] == 0){
			header('location: http://www.animeftw.tv/');
			exit;
		}
		else {
			$donate->Build($profileArray);
		}
	}
	if(($profileArray[0] == 0) && $_SERVER['HTTP_HOST'] == 'dev.animeftw.tv'){
		header('location: http://www.animeftw.tv/');
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="index, follow">
	<meta name="keywords" content="streaming anime, hd anime, anime, free, hd, high definition, hq, high quality, online, stream, streaming, manga, chat, mkv, naruto, divx anime, divx"> 
	<meta name="description" content="AnimeFTW.tv has free streaming anime in High Quality, including Psychic School, Garden of Words, Attack on Titan, R15 OAD, See Me After Class, The Devil is a Part-Timer, Sword Art Online, Akira, Mind Game, Steins;Gate, I Don't have many Friends, Valvrave the Liberator, Fairy Tail and Btooom!"/>
    <meta name="application-name" content="AnimeFTW.tv"/>
    <meta name="msapplication-TileColor" content="#5bdaff"/>
    <meta name="msapplication-TileImage" content="/images/aa92a33a-f2fa-4ace-b5b7-5a7a11b89770.png"/>
    <link rel="search" type="application/opensearchdescription+xml" title="AnimeFTW.tv Site Search" href="/AFTWSiteSearch.xml">
	<?php
	if($profileArray[0] == 1)
	{
		echo '    <link rel="alternate" href="/rss/episodes" title="AnimeFTW.tv Episode Feed" type="application/rss+xml" />';
	}
	?>
	<link rel="alternate" href="/rss/" title="AnimeFTW.tv News Feed" type="application/rss+xml" />
	<meta name="google-site-verification" content="nlqON6-3cGGASfArw7quKmD8YJbwnZRFS4bfKhTGp10" />
	<link rel="stylesheet" href="/css/jqtip.style.css" type="text/css" />
	<?php
	if($profileArray[8] == 1)
	{
		echo '<link rel="stylesheet" href="/christmas.css?v=4000" type="text/css" />';
	}
	else {
		echo '<link rel="stylesheet" href="/aftw.css?v=4004" type="text/css" />';
	}
	if($_SERVER['PHP_SELF'] == '/videos.php' && ($profileArray[2] != 0))
	{
		// New Video CSS goes here
		echo "\n".'	<link rel="stylesheet" href="/css/videojs.sublime.css?v=4.12.5" type="text/css" />'."\n";
		echo '	<link rel="stylesheet" href="/css/videojs.thumbnails.css?v=4.12.5" type="text/css" />'."\n";
		echo '	<script type="text/javascript" src="/scripts/video.js?v=4.12.5"></script>'."\n";
		echo '	<script type="text/javascript" src="/scripts/video-quality-selector.js?v=4.12.5"></script>'."\n";
		echo '	<script type="text/javascript" src="/scripts/videojs-hotkeys.min.js?v=4.12.5"></script>'."\n";
		echo '	<script type="text/javascript" src="/scripts/videojs.thumbnails.js?v=4.12.5"></script>'."\n";
	}
	function psa($profileArray,$type = NULL){
		if($type == 1){ // 1 is the users page stype..
			$style = "style='margin-top:-40px;padding-bottom:35px;'";
		}
		else if($type == 2){ // 2 is the videos pages
			$style = "style='padding-top:50px;margin-bottom:-50px;'";
		}
		else {
			$style = "style='margin-top:-40px;'";
		}
		if($profileArray[0] == 1)
		{
			$psa = '';
			$psa .= "<table align='center' cellpadding='0' cellspacing='0' width='".THEME_WIDTH."' ".$style.">\n<tr>\n";
			$psa .= "<td width='".THEME_WIDTH."' class='main-bg'>\n";
			$psa .= "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
			$psa .= "<td valign='top' class='main-mid'>\n";
			$psa .= "<div class='side-body-bg' style='min-height:50px;'>\n";	
			$psa .= '<div style="font-size:24px;font-family:Verdana,Arial,Helvetica,sans-serif;color:#666;" align="center">Major Security Update Completed!</div>';
			$psa .= '<div style="padding:5px;font-size:14px;font-family:Verdana,Arial,Helvetica,sans-serif;" align="center">Hey Guys and Gals. For the last 4 months we have been working on a new Security update to the site.<br />This update centers around keeping your account secure.<br /> Please <a href="https://www.animeftw.tv/forums/global-announcements/topic-4686/s-0">read this topic</a> for more details on this update.<br />Thank you!</div>';
			//$psa .= '<div style="font-size:24px;font-family:Verdana,Arial,Helvetica,sans-serif;color:#666;" align="center">Keep Track of all the Series you watch.</div>';
			//$psa .= '<div style="padding:5px;font-size:14px;font-family:Verdana,Arial,Helvetica,sans-serif;" align="center">The AnimeFTW.tv My WatchList feature extends the Episode Tracker but letting you know just how many episodes of a series you\'ve watched.<br />Write notes about a series and get email updates for that special airing series!</div>';
			$psa .= "</div>\n";
			$psa .= "</td>";
			$psa .= "</tr></table>";
			$psa .= "</td>";
			$psa .= "</tr></table><br />";
			//$psa = "";
			return $psa;
		}
	}
	 ?>
	<link rel="icon" href="/favicon.ico" />
	<link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png"/>
    <link rel="apple-touch-icon-precomposed" href="/images/apple-touch-icon-precomposed.png"/>
	<!--[if IE]>  <link rel="stylesheet" type="text/css" href="/iestyle.css?v=4002" />  <![endif]-->
	<script type="text/javascript" src="/scripts/jquery.min.js?v=2.1.1"></script>
	<script type="text/javascript" src="/scripts/jquery-ui.min.js"></script>
	<script type="text/javascript" src="/scripts/jquery.qtip.js"></script>
	<script type="text/javascript" src="/scripts/search-bar.js"></script>
	<script type='text/javascript'>(function(a,c){"Insticator"in a||(a.Insticator={ad:{},helper:{},client:{sid:"b4f2c3a2-2368-4c6a-8f96-6e1d8e1f5765"},version:"1.0"},a.Insticator.ad.loadAd=function(b){a.Insticator.ad.q.push(b)},a.Insticator.ad.q=[]);var b=c.createElement("script"),cof=1000*60*10,cbt=new Date(Math.floor(new Date().getTime()/cof)*cof).getTime();b.src="//embed.insticator.com/advertisements/getheadertagforsite?id="+a.Insticator.client.sid+"&cbt="+cbt;b.async=!0;var d=c.getElementsByTagName("script")[0];d.parentNode.insertBefore(b,d)})(window,document);</script>
	<?php
	if($profileArray[8] == 1)
	{
		echo '
		<script type="text/javascript" src="/scripts/snowstorm.js?ver=1.41"></script>';
	}
	if(strpos($_SERVER['REQUEST_URI'], 'store'))
	{
		echo '
		<script src="/scripts/jquery.color.js" type="text/javascript"></script>
		<script src="/scripts/thickbox.js" type="text/javascript"></script>
		<script type="text/javascript" src="/scripts/cart.js"></script>
		<link rel="stylesheet" type="text/css" href="/css/thickbox.css" />
		<script type="text/javascript">
			$(function() {
				$("form.cart_form").submit(function() {
					var title = "AnimeFTW.tv Store Cart";
					var orderCode = $("select[name=order_code]", this).val();
					if(orderCode == 0)
					{
						alert("Please Choose a Valid Size!");
						return false;
					}
					var quantity = $("input[name=quantity]", this).val();
					var url = "/scripts.php?view=cart&order_code=" + orderCode + "&quantity=" + quantity + "&TB_iframe=true&height=400&width=780";
					tb_show(title, url, false);
					
					return false;
				});
			});
		</script>';
	}
	if($profileArray[0] == 1)
	{
		echo '
		<script>
		 $(document).ready(function() {
		   var refreshId = setInterval(function() {
			  $("#notesprite").load(\'/scripts.php?view=notifications&show=sprite&randval=\'+ Math.random());
		   }, 60000);
		   $.ajaxSetup({ cache: false });
		});
		 </script>';
	}
	if($_SERVER['PHP_SELF'] == '/users.php' || $_SERVER['PHP_SELF'] == '/request.php' || $_SERVER['PHP_SELF'] == '/forums.php'){
		echo '
		<link rel="stylesheet" href="/css/redactor.css?v=2-1.1.0" />
		<script src="/scripts/redactor.min.js?v=2-1.1.0"></script>
		<script src="/scripts/redactor.table.js?v=2-1.1.0"></script>
		<script src="/scripts/redactor.inlinestyle.js?v=2-1.1.0"></script>
		<script src="/scripts/redactor.source.js?v=2-1.1.0"></script>
		<script src="/scripts/redactor.video.js?v=2-1.1.0"></script>
		<script src="/scripts/redactor.image.js?v=2-1.1.0"></script>';
		if($_SERVER['PHP_SELF'] == '/users.php' || $_SERVER['PHP_SELF'] == '/request.php') {
			echo '
			<link rel="stylesheet" href="/css/jquery-ui.min.css" />
			<script type="text/javascript" src="/scripts/popups.jquery.js"></script>
			<script language="javascript" type="text/javascript">
				$(document).ready(function(){
					$(\'.popbox\').popbox();
				});
			</script>
			<script type="text/javascript" src="/scripts/jquery.form.min.js"></script>';
		}
		if($_SERVER['PHP_SELF'] == '/users.php') {
			echo "
			<script type=\"text/javascript\">
				$(document).ready(function() { 
					var options = { 
							target:   '#avatar-div-wrapper',   // target element(s) to be updated with server response 
							beforeSubmit:  beforeSubmit,  // pre-submit callback 
							success:       afterSuccess,  // post-submit callback 
							resetForm: true        // reset the form after successful submit 
						}; 
						
					 $('#MyUploadForm').submit(function() { 
							$(this).ajaxSubmit(options);  			
							// always return false to prevent standard browser submit and page navigation 
							return false; 
						}); 
				}); 

				function afterSuccess()
				{
					$('#submit-btn').show(); //hide submit button
					$('#loading-img').hide(); //hide submit button
				}

				//function to check file size before uploading.
				function beforeSubmit(){
					//check whether browser fully supports all File API
				   if (window.File && window.FileReader && window.FileList && window.Blob)
					{
						
						if( !$('#imageInput').val()) //check empty input filed
						{
							alert('Are you kidding me?');
							return false
						}
						
						var fsize = $('#imageInput')[0].files[0].size; //get file size
						var ftype = $('#imageInput')[0].files[0].type; // get file type
						

						//allow only valid image file types 
						switch(ftype)
						{
							case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
								break;
							default:
								alert('<b>'+ftype+'</b> Unsupported file type!');
								return false
						}
						
						//Allowed file size is less than 1 MB (1048576)
						if(fsize>1048576) 
						{
							alert('<b>' + bytesToSize(fsize) + '</b> Too big Image file! <br />Please try smaller filer or reduce the size of your photo using an image editor.');
							return false
						}
								
						$('#submit-btn').hide(); //hide submit button
						$('#loading-img').show(); //hide submit button
						$('#output').html(\"\");  
					}
					else
					{
						//Output error to older unsupported browsers that doesn't support HTML5 File API
						alert('Please upgrade your browser, because your current browser lacks some new features we need!');
						return false;
					}
				}

				function bytesToSize(bytes) {
				   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
				   if (bytes == 0) return '0 Bytes';
				   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
				   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
				}

				</script>";
		}
	}
	if($_SERVER['PHP_SELF'] == '/videos.php'){
		echo '
    <script type="text/javascript" src="/scripts/rating_update.js"></script>';
	}
	if($_SERVER['PHP_SELF'] == '/secure.php' && $_GET['node'] == 'register'){
		echo "\n	<script type=\"text/javascript\" src=\"/scripts/jquery.smartWizard-2.0.min.js\"></script>\n";
		echo "	<script type=\"text/javascript\" src=\"/scripts/aftw-register.js\"></script>\n";
		echo "	<script src='https://www.google.com/recaptcha/api.js'></script>\n";
	}
	?>
    <title><?=$PageTitle;?></title>
    <?php
	if($_SERVER['PHP_SELF'] == '/users.php'){
		if($u->nVar('ID') == 1){
	?>
    <style type="text/css">
body {
	background-image: url("/images/uploads/background_user1.png");
		
			background-position:center;
			background-attachment: fixed;
			background-repeat: no-repeat;
		
}
</style>
<?php
		}
	}
	if($_SERVER['REQUEST_URI'] == '/donate'){
		echo '<style type="text/css">
		body {
			background-image: url("/images/uploads/background_donate.png");
				
					background-position:center;
					background-attachment: fixed;
					background-repeat: no-repeat;
				
		}
		</style>';
	}
	
	if($_SERVER['PHP_SELF'] == '/videos.php' && ($profileArray[2] != 3 && $profileArray[2] != 0))
	{
		?>
		<script>
		eval(function(p,a,c,k,e,d){e=function(c){return c};if(!''.replace(/^/,String)){while(c--){d[c]=k[c]||c}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('1.2.5.0="3-4.0";',6,6,'swf|videojs|options|video|js|flash'.split('|'),0,{}))
		eval(function(p,a,c,k,e,d){e=function(c){return c};if(!''.replace(/^/,String)){while(c--){d[c]=k[c]||c}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('2(0($){$("1").3("4",0(5){7 6})});',8,8,'function|video|jQuery|bind|contextmenu|e|false|return'.split('|'),0,{}))
		</script>
		<?php
	}
	?>
    <script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-6243691-1']);
  _gaq.push(['_setDomainName', 'animeftw.tv']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<script type="text/javascript" src="/scripts/aftw-functions.js?v=4.0.2"></script>
</head>
<body>
<div id="loaderImage"></div>
