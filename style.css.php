<?php
include("includes/classes/config.class.php");
$C = new Config(TRUE);
if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
   header("HTTP/1.1 304 Not Modified");
   exit;
}

$useragent = (isset($_SERVER["HTTP_USER_AGENT"]) ) ? $_SERVER["HTTP_USER_AGENT"] : $HTTP_USER_AGENT;

if (phpversion() >= '4.0.4pl1' && (strstr($useragent,'compatible') || strstr($useragent,'Gecko'))) {
	if (extension_loaded('zlib')) {
		ob_start('ob_gzhandler');
	}
}

if(isset($_GET['theme']))
{
	if($_GET['theme'] == 'christmas')
	{
		$stylelocation = '/themes/christmas/';
		$styletextcolor = 'ff3939';
		$styletextcolordark = 'ff3939';
		$styletextcolorhover = 'fd6363';
	}
	else
	{
		$stylelocation = '/themes/default/';
		$styletextcolor = '11b4e9';
		$styletextcolordark = '007fc8';
		$styletextcolorhover = '0C9';
	}
}
else
{
	$stylelocation = '/themes/default/';
	$styletextcolor = '11b4e9';
	$styletextcolordark = '007fc8';
	$styletextcolorhover = '0C9';
}

header('Content-type: text/css;');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", time() - 3600*24*365)." GMT");
header('Expires: '.gmdate("D, d M Y H:i:s", time() + 3600*24*365).' GMT');

echo '@charset "utf-8";
/* 	Author: Falcon Pell (www.agxthemes.com) 			*/
/*	Revisions by Robotman321 of FTW Entertainment LLC	*/
/* 	Copyright 2011 FTW Entertainment LLC				*/
a{color:#' . $styletextcolordark . ';text-decoration:none}
a:hover{color:#' . $styletextcolorhover . ';text-decoration:underline}
img{border:0}
body{max-width:100%;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;color:#000;background:#e8e8e8;margin:auto}
blockquote{padding-left:10px;border-left:3px solid #0F7FC8;margin:10px 0 10px 25px}
.header-bg{height:73px;background-color:#1b1b1b;background-image:url("' . $C->Host . $stylelocation . 'header_bg.gif");background-repeat:repeat-x}
.header-log{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;font-weight:normal;color:#' . $styletextcolor . ';width:377px;height:32px;margin-right:14px;padding-top:10px}
.header-log a{color:#' . $styletextcolor . ';text-decoration:none}
.header-log a:hover{color:#' . $styletextcolorhover . ';text-decoration:underline}
.header-sort{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;font-weight:normal;color:#fff;width:580px;height:14px;margin-right:14px;margin-top:-5px}
.header-search{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;font-weight:normal;color:#fff;width:410px;height:49px;float:right;margin-right:80px;margin-top:-4px}
.header-sort a{text-decoration:none;color:#fff}
.header-sort a:hover{color:#fff;text-decoration:underline}
.header-dir{width:597px;height:13px;padding-top:5px;margin-right:90px}
.header-search-nav{width:374px;height:83px;background-image:url("' . $C->Host . $stylelocation . 'searchbox_bg_new.png");background-repeat:no-repeat;background-position:bottom right}
.header-nav{font-size:14px;height:49px;background-color:#1b1b1b;background-image:url("' . $C->Host . $stylelocation . '/lwrnav-bg.png");background-repeat:repeat-x}
.header-nav-left a{text-decoration:none;vertical-align:middle;padding:5px;height:49px;padding:18px 12px 14px 15px;color:#' . $styletextcolor . ';}
.header-nav-left a:hover{text-decoration:none;background:url("' . $C->Host . $stylelocation . 'lwrnav-bg-hvr.png") no-repeat center center;height:49px;padding:18px 12px 14px 15px;color:#' . $styletextcolorhover . '}
.header-nav-left .current{text-decoration:none;background:url("' . $C->Host . $stylelocation . 'lwrnav-bg-hvr.png") no-repeat center center;height:49px;padding:18px 12px 14px 15px;color:#' . $styletextcolor . '}
.note-message{ color:#333;background-color:#FCE9C0;border-top:2px solid #DBAC48;border-bottom:2px solid #DBAC48;padding:10px 0 10px 0}
.main-bg{ background-color:#F3F3F3;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;-webkit-box-shadow:0 8px 20px 0 #cdcdcd;box-shadow:0 8px 20px 0 #cdcdcd; padding:15px}
.footer{background-color:#0e0e0e;background-image:url("' . $C->Host . $stylelocation . 'footer.gif");background-repeat:repeat-x top;height:100px}
#loaderImage{float:left;position:fixed;z-index:9999;left:50%;font-size:18px;background-color:white;border-bottom-left-radius:5px;border-bottom-right-radius:5px;display:none;}
#body-content-index{width:950px;margin:0 auto}
.body-content-full{ background-color:#F3F3F3;padding:5px;border-top-left-radius:5px;border-top-right-radius:5px;border-bottom-left-radius:5px;border-bottom-right-radius:5px; width:930px}
.body-content{padding:5px;width:640px}
.body-content-right{padding:5px 5px 5px 5px;width:290px;float:right;border-left:#CFF1FC solid thin;height:auto}
.body-content-right img:hover{background-color:#CFF1FC}
#right-pad{padding:5px 2px 2px 2px}
#right-pad span{font-family:Arial,Helvetica,sans-serif;font-weight:bold;font-size:16px}
.right-content{padding-top:3px}
#left-content{border-bottom:#CFF1FC solid thin;padding:5px}
#left-content .title{font-family:Arial,Helvetica,sans-serif;font-weight:bold;font-size:20px;text-decoration:none;color:#' . $styletextcolor . '}
#left-content .poster{font-size:10px}
#left-content .left-body{padding-top:5px}
#left-content a{text-decoration:none;color:#0A7BA0}
#left-content a:hover{text-decoration:none;color:#' . $styletextcolorhover . '}
.left-title{padding-top:5px}
.search img{padding:30px 26px 0px 0px}
.search{padding:15px 23px 0px 0px;width:348px;height:39px;background:url("' . $C->Host . $stylelocation . 'search-box-bg.png") no-repeat center bottom;float:right}
.search-box{width:290px;height:32px;background:none;border:none;color:#000;font-size:16px;margin-top:2px;padding-left:25px;font-family:Verdana,Arial,Helvetica,sans-serif}
.search-button{width:37px;height:39px;float:right;margin-top:-30px;margin-right:-37px;border:none;cursor:pointer}
.search-text{float:left;margin-left:12px;padding-top:5px;font-size:12px;font-family:Verdana,Arial,Helvetica,sans-serif}
.search-text-pre{color:#000}
.search-text-post{color:#FFF}
.search-text img{float:left;padding:1px 2px 0 0}
.MenuLink:hover{ background:#CCC}
.MenuLink{ color:#FFF;display:block;padding:10px 0px;text-align:center;width:160px}
#SearchResults a:hover{ background:#' . $styletextcolorhover . ';color:#000;text-decoration:none}
#SearchResults a .highlight{ color:#09F}
#SearchResults a{ color:#808080;background-color:#fff;display:block;text-decoration:none;padding:2px 5px 1px 5px;border:2px #999}
#SearchResults{ background:#fff;display:none;overflow:auto;position:relative;top:-20px;left:-11px;width:345px;z-index:99;border:1px solid black}
#q{ border:none;color:#999}
.tbl-border{border:1px solid #e1e1e1}
.tbl{font-size:11px;color:#555;background-color:#fff;padding:4px}
.tbl1{font-size:11px;color:#555;background-color:#fff;padding:4px}
.tbl2{font-size:11px;color:#555;background-color:#f1f1f1;padding:4px}
.forum-caption{font-size:11px;font-weight:bold;color:#888;background-color:#f1f1f1;padding:2px 4px 4px 4px}
.scapmain{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;color:#666;padding:4px}
.side-body{font-size:11px;color:#777;background-color:#fff;border:1px solid #e1dedd;padding:6px}
.side-body-bg{background-color:#eeebea;border:1px solid #d7d4d3;padding:4px;margin:0px 0px 10px 0px;border-top-left-radius:3px;border-top-right-radius:3px; border-bottom-left-radius:3px;border-bottom-right-radius:3px;}
.floatfix{overflow:hidden}
::selection{color:#FFF;background:#093}
::-moz-selection{color:#FFF;background:#093}
div.highlightBlue::selection{color:#FFF;background:#093}
div.highlightBlue::-moz-selection{color:#FFF;background:#093}
.word{font-family:Verdana,Tahoma,Arial;padding:4px 4px 4px 4px;letter-spacing:3px;text-decoration:none;font-weight:normal}
.size9{color:#000;font-size:26px;text-decoration:none}
.size8{color:#111;font-size:24px;text-decoration:none}
.size7{color:#222;font-size:22px;text-decoration:none}
.size6{color:#333;font-size:21px;text-decoration:none}
.size5{color:#444;font-size:19px;text-decoration:none}
.size4{color:#555;font-size:17px;text-decoration:none}
.size3{color:#666;font-size:15px;text-decoration:none}
.size2{color:#777;font-size:13px;text-decoration:none}
.size1{color:#888;font-size:11px;text-decoration:none}
.size0{color:#999;font-size:9px;text-decoration:none}
#lister #col1{width:250px;float:left}
#lister #col2outer{width:610px;float:right;margin:0;padding:0}
#col2outer #col2{width:300px;float:left}
#col2outer #col3{width:300px;float:right}
#va{width:990px;height:78px;margin:0px auto}
#va .head{font-family:Arial,Helvetica,sans-serif;font-size:24px;padding:5px 0px 5px 5px}
#va .headend{font-family:Arial,Helvetica,sans-serif;font-size:20px;padding:5px 5px 5px 0px}
#ua{width:990px;margin:0px auto;text-align:left;margin-top:-44px}
#ua .head{font-family:Arial,Helvetica,sans-serif;font-size:24px;padding:5px 0px 5px 5px}
#ua .headend{font-family:Arial,Helvetica,sans-serif;color:#999;font-size:12px;font-style:italic;padding:0px 5px 5px 5px}
.ucleft{border-right:solid 1px #CCC;margin-right:15px}
.linfo{min-width:185px;margin-right:10px;padding:5px 0 5px 0;border-bottom:solid 1px #E5E5E5;font-size:14px}
.linfo a{text-decoration:none}
.linfo img{float:left;padding:0 2px 0 2px}
.linfo span{margin-left:2px}
.linfo:hover{background-color:#E5E5E5}
#subfriends{padding:20px 0 5px 0;margin-right:10px}
.fds{font-family:Arial,Helvetica,sans-serif;font-size:18px;border-bottom:solid 1px #D1D1D1}
#sleftusernav{background:url("' . $C->Host . $stylelocation . 'leftusernavv1.png") no-repeat 0 0}
#srightusernav{background:url("' . $C->Host . $stylelocation . 'rightusernavv1.png") no-repeat 0 0}
#sleftusernav span,#srightusernav span{display:none}
#sleftusernav:hover,#srightusernav:hover{background-position:0 -31px}
#sleftusernav,#srightusernav{display:inline-block;width:36px;height:31px;cursor:pointer}
.pstats dt{display:inline;float:left;margin:0px;padding:0px 0px 0px;width:155px;white-space:nowrap}
.psettings dt{display:inline;float:left;margin:0px;padding:0px 0px 0px;width:155px;white-space:nowrap}
.objects{margin-left:7px}
div#left div{padding-top:10px}
.searchdiv{height:100px}
.searchinfo{float:left;padding-left:10px;width:490px}
.vertical{position:relative;overflow:hidden;height:582px;width:710px;border-top:1px solid #ddd}
.items{position:absolute;height:100000em;margin:0px}
.item{margin:10px 0;padding:10px 0px 10px 0px;height:100px;width:710px}
.item h3{margin:0 0 5px 0;font-size:16px;color:#456;font-weight:normal}
#actions{width:710px;margin:30px 0 10px 0}
#actions a{font-size:11px;cursor:pointer;color:#666}
#actions a:hover{text-decoration:underline;color:#000}
.next{float:right}
.tooltip{display:none;background:transparent url("' . $C->Host . $stylelocation . 'blue_arrow.png");font-size:12px;height:70px;width:160px;padding:25px;color:#fff}
.pane-subnav{display:none;padding:7px 5px 2px 5px;margin-bottom:-29px;border-top:0;height:20px;font-size:12px;background-color:#F3F3F3;width:500px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;-webkit-box-shadow:0 8px 20px 0 #cdcdcd;box-shadow:0 8px 20px 0 #cdcdcd}
.panes{padding-right:480px}
#RegisterForm{background:#333;padding:15px 20px;color:#eee;width:800px;margin:0 auto;position:relative;border-radius:5px;}
#RegisterForm fieldset{border:0;margin:0;padding:0;background:#333 url(https://flowplayer.org/tools/img/logo-medium.png) no-repeat scroll 215px 40px}
#RegisterForm h3{color:#eee;margin-top:0px}
#RegisterForm p{font-size:11px}
#RegisterForm input{border:1px solid #444;background-color:#666;padding:5px;color:#ddd;font-size:12px;text-shadow:1px 1px 1px #000;border-radius:4px;}
#RegisterForm input:focus{color:#fff;background-color:#777}
#RegisterForm input:active{background-color:#888}
#RegisterForm button{outline:0;border:1px solid #666}
.error{height:15px;background-color:#FFFE36;font-size:11px;border:1px solid #E1E16D;padding:4px 10px;color:#000;display:none;border-radius:4px;box-shadow:0 0 6px #ddd;-webkit-box-shadow:0 0 6px #ddd}
.error p{margin:0}
label{display:block;font-size:11px;color:#ccc}
#terms label{float:left}
#terms input{margin:0 5px}
#RegisterForm fieldset{background-image:none;float:left;width:250px}
#RegisterForm label{font-size:11px;margin-top:20px;display:block}
#RegisterForm label input{display:block}
.clear{clear:both;height:15px}
.error{margin:0 0 2px;padding:2px 6px;border-radius:4px;}
.error em{border:10px solid;border-color:#FFFE36 transparent transparent;bottom:-17px;display:block;height:0;left:60px;position:absolute;width:0}
.video-div{background:#F7F7F7;padding:5px}
#button{height:32px;width:184px;padding-left:700px}
.menu_class{margin-bottom:-4px}
.the_menu{display:none;width:300px;border:1px solid #1c1c1c;float:right}
.the_menu li{background-color:#302f2f}
.the_menu li a{color:#FFF;text-decoration:none;padding:10px;display:block}
.the_menu li a:hover{padding:10px;font-weight:bold;color:#F00880}
a.feature01{display:block;border:1px solid #dfd0cb;border-width:0 1px 1px 0;float:left;position:relative}
a.feature01:hover{border-color:#' . $styletextcolor . '}
a.feature01:hover .overlay01{position:absolute;z-index:3;width:154px;height:86px;border:3px solid #' . $styletextcolor . '}
a.feature01 .overlay01 .caption01,img.caption01{position:absolute;height:30px;line-height:30px;width:100%;z-index:3;text-indent:-9999em;color:#000;font-size:11px;bottom:0;overflow:hidden}
a.feature01:hover .caption01{text-indent:10px;background:rgb(17,180,233);background:rgba(17,180,233,0.75)}
a.feature01 img{display:block}
#NT_copy{background-color:#333;color:#FFF;font-weight:bold;font-size:10px;font-family:"Trebuchet MS";width:400px;left:0;top:0;padding:4px;position:absolute;text-align:left;z-index:20;border-radius:0 10px 10px 10px;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=87);-khtml-opacity:.87;opacity:.87;}
#ajax_tooltipObj{z-index:1000000;text-align:left}
#ajax_tooltipObj div{position:relative}
#ajax_tooltipObj .ajax_tooltip_arrow{;width:20px;position:absolute;left:0px;top:0px;background-repeat:no-repeat;background-position:center left;z-index:1000005;height:60px}
#ajax_tooltipObj .ajax_tooltip_content{background-color:#333;color:#FFF;font-weight:bold;font-size:10px;font-family:"Trebuchet MS";width:400px;left:10px;top:0;padding:4px;position:absolute;text-align:left;z-index:20;border-radius:0 10px 10px 10px;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=87);-khtml-opacity:.87;opacity:.87;}
#pageselected{font-weight:bold}
.fontcolor a,.fontcolor span{background-color:#F8F8F8;padding:1px 2px 1px 2px}
.fontcolor a:hover{background-color:#F0F0F0}
.star-rating,.star-rating a:hover,.star-rating a:active,.star-rating .current-rating{background:url(' . $C->Host . '/rating_star.png) left -1000px repeat-x}
.star-rating{position:relative;width:125px;height:25px;overflow:hidden;list-style:none;margin:0;padding:0;background-position:left top}
.star-rating li{display:inline}
.star-rating a,.star-rating .current-rating{position:absolute;top:0;left:0;text-indent:-1000em;height:25px;line-height:25px;outline:none;overflow:hidden;border:none}
.star-rating a:hover{background-position:left bottom}
.star-rating a.one-star{width:20%;z-index:6}
.star-rating a.two-stars{width:40%;z-index:5}
.star-rating a.three-stars{width:60%;z-index:4}
.star-rating a.four-stars{width:80%;z-index:3}
.star-rating a.five-stars{width:100%;z-index:2}
.star-rating .current-rating{z-index:1;background-position:left center}
.star-rating2,.star-rating2 a:active,.star-rating2 .current-rating{background:url(' . $C->Host . '/rating_star_2.png) left -1000px repeat-x}
.star-rating2{position:relative;width:125px;height:25px;overflow:hidden;list-style:none;margin:0;padding:0;background-position:left top}
.star-rating2 li{display:inline}
.star-rating2 a,.star-rating2 .current-rating{position:absolute;top:0;left:0;text-indent:-1000em;height:25px;line-height:25px;outline:none;overflow:hidden;border:none;cursor:default}
.star-rating2 a.one-star{width:20%;z-index:6}
.star-rating2 a.two-stars{width:40%;z-index:5}
.star-rating2 a.three-stars{width:60%;z-index:4}
.star-rating2 a.four-stars{width:80%;z-index:3}
.star-rating2 a.five-stars{width:100%;z-index:2}
.star-rating2 .current-rating{z-index:1;background-position:left center}
.inline-rating{display:-moz-inline-block;display:-moz-inline-box;display:inline-block;vertical-align:middle}
.voted_twice{background:#FDD url(' . $C->Host . $stylelocation . 'rating_warning.gif) no-repeat 5px 50%;padding:5px 5px 5px 16px;text-align:center;font-family:Verdana,Arial,Helvetica,sans-serif;color:#333;width:130px;font-size:11px}
.voted{background:#E7FFCE url(' . $C->Host . $stylelocation . 'rating_tick.gif) no-repeat 5px 50%;padding:5px 5px 5px 16px;text-align:center;font-family:Verdana,Arial,Helvetica,sans-serif;color:#333;width:130px;font-size:11px}
.rated_text{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;margin-bottom:5px;color:#666}
.out5Class{color:#0C0;font-weight:bold}
.percentClass{}
.votesClass{}
.topRatedList{padding:0;margin:0}
.topRatedList li{list-style-type:none}
.highlight01 a:hover img,.highlight01 a:focus img{outline-color:#1E528C}
.highlight01 a:hover:after,.highlight01 a:focus:after{background:rgb(30,82,140);background:rgba(30,82,140,0.7);background:-moz-linear-gradient(top,rgba(30,82,140,0.7),rgba(43,117,200,0.7));background:-webkit-gradient(linear,0% 0%,0% 100%,from(rgba(30,82,140,0.7)),to(rgba(43,117,200,0.7)));color:#fff;text-shadow:1px 1px 1px #000}
a.linkopacity img{filter:alpha(opacity=40);opacity:0.5; -khtml-opacity:0.5}
a.linkopacity:hover img{ filter:alpha(opacity=100);opacity:1.0; -khtml-opacity:1.0}
.speech_bubble{  position:relative}
.speech_bubble.say{  margin-top:8px}
.speech_bubble.say .top-left,.speech_bubble.say .top-right,.speech_bubble.say .bottom-left,.speech_bubble.say .bottom-right{  background:url(' . $C->Host . $stylelocation . 'say_sprite_2.png) no-repeat top left;  height:16px}
.speech_bubble.say .top-left{  margin-right:16px}
.speech_bubble.say .top-right{  margin-top:-16px;  margin-left:16px;  background-position:top right}
.speech_bubble.say .bottom-left{  margin-right:16px;  background-position:0 -16px}
.speech_bubble.say .bottom-right{  background-position:right -16px;  margin-top:-16px;  margin-left:16px}
.speech_bubble.say .content{  background-color:#fff;  border-right:2px solid #7d7e82;  border-left:2px solid #7d7e82;  min-height:75px;  overflow:auto;  padding:0 15px}
.speech_bubble.say .tail{  background-image:url(' . $C->Host . $stylelocation . 'say_sprite_2.png); background-position:0 -31px; height:40px; left:-17px; position:absolute; top:32px; width:19px}
.speech_bubble.say .tail.rt{ background-position:-19px -31px; left:auto; right:-17px}
.speech_bubble IMG{ max-width:750px}
.speech_bubble IMG:active{ max-width:none}
.footer-panels{background-color:#202020;background-image:url(' . $C->Host . $stylelocation . 'footer-bg.png);background-position:inherit;background-repeat:repeat-x;}
.footer-mascot{width:279px;height:300px;background-color:#202020;background-image:url(' . $C->Host . $stylelocation . 'footer-mascot.jpg);background-repeat:no-repeat}
.panels{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#fff;padding-left:20px;padding-right:20px;padding-top:60px}
.panel-line{background-image:url(' . $C->Host . $stylelocation . 'dott.gif);background-repeat:repeat-x;padding-top:5px}
.panel-title{font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;color:#' . $styletextcolor . '}
.copyright{ font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#fff;padding-left:20px;padding-bottom:20px}
 .ddmenu,.ddmenu2{list-style:none;padding:0;margin:0;margin-top:-5px}
.ddmenu >li{float:left;padding-top:5px;margin-left:275px}
.ddmenu >li >a{display:block;text-decoration:none;color:#232323;font-weight:bold;padding:0 2px 1px 2px;border-top-right-radius:5px; border-top-left-radius:5px; }
.ddmenu >li >a:hover{padding:0 2px 1px 2px;border-top-right-radius:5px;border-top-left-radius:5px;}
.ddmenu >li >.ddmenu-hover{background-color:#0B8AB3}
.ddmenu >li ul{display:none;list-style:none;position:absolute;margin:-1px 0 0 0;z-index:90;padding:0}
.ddmenu >li ul li{display:inline-block;padding:2px 5px 2px 0;width:295px;text-decoration:none;font-weight:bold;font-size:10pt;color:#232323;background-color:#0B8AB3;border-bottom-left-radius:5px;border-bottom-right-radius:5px; border-top-right-radius:5px;}
.ddmenu >li ul li a.first,.ddmenu2 >li ul li a.first{border-top:1px solid #545454}
.ddmenu >li ul a.ddchildhover,.ddmenu2 >li ul a.ddchildhover{text-decoration:none}
.ddmenu2 >li{float:left;padding-top:5px;margin-left:10px}
.ddmenu2 >li >a{display:block;text-decoration:none;color:#232323;font-weight:bold;padding:3px 3px 1px 3px;margin-top:2px;border-top-right-radius:5px; border-top-left-radius:5px;}
.ddmenu2 >li >a:hover{padding:3px 3px 1px 3px;border-top-right-radius:5px;border-top-left-radius:5px;}
.ddmenu2 >li >.ddmenu-hover{background-color:#0B8AB3}
.ddmenu2 >li ul{display:none;list-style:none;position:absolute;margin:-1px 0 0 0;z-index:90;padding:0}
.ddmenu2 >li ul li{display:inline-block;padding:3px 5px 2px 1px;width:240px;text-decoration:none;font-weight:bold;font-size:10pt;color:#232323;background-color:#0B8AB3;border-bottom-left-radius:5px;border-bottom-right-radius:5px; border-top-right-radius:5px;}
.langbar{float:left;color:#fff;font-weight:bold;padding:3px 0 0 2px}
.timeline{list-style:none;width:730px}
#main{width:500px; margin-left:200px;font-family:"Trebuchet MS"}
#flash{margin-left:100px}
.box{min-height:55px;height:100%;border-bottom:#dedede dashed 1px;margin-bottom:5px;padding:2px}
.comment{font-size:13px}
.more,.more2{display:none}
#update li:hover .more{display:block;float:right;z-index:1;margin-top:-5px}
#update li:hover .more2{display:block;float:left;z-index:1;margin-top:-5px}
.com_name{color:#B2B2B2}
#movie{margin-right:20px;position:relative;z-index:102}
#command{position:relative;height:25px;display:block;margin:0 0 0 0}
.lightSwitcher{position:absolute;z-index:101;background-image:url(' . $C->Host . $stylelocation . 'light_bulb_off.png);background-repeat:no-repeat;background-position:left;padding:0 0 0 20px;outline:none;text-decoration:none}
.lightSwitcher:hover{text-decoration:underline}
#shadow{background-image:url(' . $C->Host . $stylelocation . 'shade1x1.png);position:absolute;left:0;top:0;width:100%;z-index:100}
.turnedOff{color:#ff0;background-image:url(' . $C->Host . $stylelocation . 'light_bulb.png)}
#clickHere{position:absolute;top:-25px;left:130px}
#tab1{color:#' . $styletextcolor . ';background-color:#000;text-decoration:none;padding:2px 7px 2px 7px;border-top-left-radius:5px;border-top-right-radius:5px;}
#tabs{font-size:14px;padding-bottom:2px; border-bottom:2px solid #000}
#tabs a{color:#FFF;background-color:#' . $styletextcolor . ';padding:2px 7px 2px 7px;border-top-left-radius:5px;border-top-right-radius:5px;}
#tabs a:hover{color:#' . $styletextcolor . ';background-color:#000;text-decoration:none;padding:2px 7px 2px 7px;border-top-left-radius:5px;border-top-right-radius:5px; }
#tabs .tabactive{color:#' . $styletextcolor . ';background-color:#000;text-decoration:none;border-top-left-radius:5px;border-top-right-radius:5px;}
input,textarea,pre{font-family:verdana;font-size:inherit;font-family:inherit}
label{width:110px}
#userName,#userName_field{font-size:14px}
#cityName,#cityName_field{font-size:14px;background-color:#333;color:#fff}
#blogTitle,#blogTitle_field{font-size:24px}
#blogText,#blogText_field{width:240px}
#lorumText,#lorumText_field{width:500px}
.pagetitle{background-image:url(' . $C->Host . $stylelocation . 'img03.gif);background-repeat:repeat-x;border-right-width:thin;border-left-width:thin;border-right-style:solid;border-left-style:solid;border-right-color:#10AEE1;border-left-color:#10AEE1;font-family:Verdana;font-size:14px;font-weight:bold;color:#10AEE1}
.sectiontitle{font-family:Verdana;font-size:14px;font-weight:bold;color:#10AEE1;padding-top:5px;padding-right:5px;padding-bottom:5px;text-align:left}
.sectionheadercenter{font-family:Verdana;font-size:14px;font-weight:bold;color:#FFF;background-image:url(' . $C->Host . $stylelocation . 'img04.gif);background-repeat:repeat-x;padding:5px;text-align:left}
.sectionheaderleft{font-family:Verdana;font-size:14px;font-weight:bold;color:#FFF;background-image:url(' . $C->Host . $stylelocation . 'img04.gif);background-repeat:repeat-x;border-left-width:thin;border-left-style:solid;border-left-color:#10AEE1;padding:5px;text-align:left}
.sectionheaderright{font-family:Verdana;font-size:14px;font-weight:bold;color:#FFF;background-image:url(' . $C->Host . $stylelocation . 'img04.gif);background-repeat:repeat-x;border-right-width:thin;border-right-style:solid;border-right-color:#10AEE1;padding:5px}
.sectionheadercenter a,.sectionheaderleft a{color:#FFF}
.sectionbottom{border-top-width:thin;border-top-style:solid;border-top-color:#10AEE1;font-size:1px}
.sectioncontentscenter{font-family:Verdana;font-size:12px;font-weight:normal;color:#00719B;padding:5px}
.sectioncontentscenter2{font-family:Verdana;font-size:12px;font-weight:normal;color:#00719B;padding:5px}
.sectioncontentsleft{font-family:Verdana;font-size:12px;font-weight:normal;color:#00719B;border-left-width:thin;border-left-style:solid;border-left-color:#10AEE1;padding:5px}
.sectioncontentsright{font-family:Verdana;font-size:12px;font-weight:normal;color:#00719B;padding:5px;border-right-width:thin;border-right-style:solid;border-right-color:#10AEE1}
#Layer1{position:fixed;width:100%;height:130px;z-index:1;left:0;top:35%;background-color:#000;border-top-width:medium;border-bottom-width:medium;border-top-style:ridge;border-bottom-style:ridge;border-top-color:#646C89;border-bottom-color:#646C89;visibility:hidden}
.style1{border-top-width:thin;border-top-style:solid;border-top-color:#10AEE1;font-size:12px;font-family:Verdana;font-weight:bold;color:#10AEE1}
.editcol a:visited{color:#10AEE1;text-decoration:none;font-weight:bold}
.editcol a:active{color:#10AEE1;text-decoration:none;font-weight:bold}
.editcol a:hover{color:#3F4756;text-decoration:none;background-color:#000}
.editcol a:link{color:#10AEE1;text-decoration:none;font-weight:bold}
iframe{margin:0px;padding:0px;clip:rect(10px,auto,auto,auto)}
.sectionformright{font-family:Verdana;font-size:12px;font-weight:normal;color:#10AEE1;padding:5px;border-right-width:thin;border-right-style:solid;border-right-color:#10AEE1}
#Layer2{position:fixed;width:100%;height:130px;z-index:1;left:0;top:35%;background-color:#000;border-top-width:medium;border-bottom-width:medium;border-top-style:ridge;border-bottom-style:ridge;border-top-color:#646C89;border-bottom-color:#646C89;visibility:hidden}
.top10{margin:-5px 0 0 -20px}
.swMain{ position:relative; display:block; margin:0; padding:0; border:0px solid #CCC; overflow:visible; float:left; width:980px}
.swMain .stepContainer{ display:block; position:relative; margin:0; padding:0;  border:0px solid #CCC;  overflow:hidden; clear:both; height:300px}
.swMain .stepContainer div.content{ display:block; position:absolute;  float:left; margin:0; padding:5px;  border:1px solid #CCC; font:normal 12px Verdana,Arial,Helvetica,sans-serif;color:#5A5655;background-color:#F8F8F8;height:300px;text-align:left;overflow:visible;z-index:88;border-radius:5px; width:968px; clear:both}
.swMain div.actionBar{ display:block; position:relative; clear:both; margin: 3px 0 0 0;  border: 1px solid #CCC; padding: 0;  color: #5A5655;  background-color: #F8F8F8; height:40px;text-align:left;overflow:auto;z-index:88;border-radius :5px;left:0}
.swMain .stepContainer .StepTitle{ display:block; position:relative; margin:0;  border:1px solid #E0E0E0; padding:5px;  font:bold 16px Verdana,Arial,Helvetica,sans-serif; color:#5A5655;background-color:#E0E0E0;clear:both;text-align:left;z-index:88;border-radius:5px}
.swMain ul.anchor{ position:relative; display:block; float:left; list-style:none; padding:0px;  margin:10px 0;  clear:both; border:0px solid #CCC;  background:transparent}
.swMain ul.anchor li{ position:relative; display:block; margin:0; padding:0; padding-left:3px; padding-right:3px; border:0px solid #E0E0E0;  float:left}
.swMain ul.anchor li a{ display:block; position:relative; float:left; margin:0; padding:3px; height:60px; width:230px; text-decoration:none; outline-style:none;border-radius:5px;z-index:99}
.swMain ul.anchor li a .stepNumber{ position:relative; float:left; width:30px; text-align:center; padding:5px; padding-top:0; font:bold 45px Verdana,Arial,Helvetica,sans-serif}
.swMain ul.anchor li a .stepDesc{ position:relative; display:block; float:left; text-align:left; padding:5px; font:bold 20px Verdana,Arial,Helvetica,sans-serif}
.swMain ul.anchor li a .stepDesc small{ font:normal 12px Verdana,Arial,Helvetica,sans-serif}
.swMain ul.anchor li a.selected{ color:#F8F8F8; background:#0C90BB;  border:1px solid #0C90BB; cursor:text;-webkit-box-shadow:5px 5px 8px #888; box-shadow:5px 5px 8px #888}
.swMain ul.anchor li a.selected:hover{ color:#F8F8F8;  background:#0C90BB}
.swMain ul.anchor li a.done{ position:relative; color:#CCC; background:#E6E6E6;  border:1px solid #E6E6E6;  z-index:99}
.swMain ul.anchor li a.done:hover{ color:#0C90BB; background:#E6E6E6; border:1px solid #0C90BB}
.swMain ul.anchor li a.disabled{ color:#CCC;  background:#E6E6E6; border:1px solid #CCC;  cursor:text}
.swMain ul.anchor li a.disabled:hover{ color:#CCC;  background:#E6E6E6}
.swMain ul.anchor li a.error{ color:#6c6c6c !important;  background:#f08f75 !important; border:1px solid #fb3500 !important}
.swMain ul.anchor li a.error:hover{ color:#000 !important}
.swMain .buttonNext{ display:block; float:right; margin:5px 3px 0 3px; padding:5px; text-decoration:none; text-align:center; font:bold 13px Verdana,Arial,Helvetica,sans-serif; width:100px; color:#FFF; outline-style:none; background-color: #5A5655; border:1px solid #5A5655;border-radius:5px;}
.swMain .buttonDisabled{ color:#F8F8F8 !important; background-color:#CCC !important; border:1px solid #CCC !important; cursor:text}
.swMain .buttonPrevious{ display:block; float:right; margin:5px 3px 0 3px; padding:5px; text-decoration:none; text-align:center; font:bold 13px Verdana,Arial,Helvetica,sans-serif; width:100px; color:#FFF; outline-style:none; background-color: #5A5655; border:1px solid #5A5655; border-radius :5px;}
.swMain .buttonFinish{ display:block; float:right; margin:5px 10px 0 3px; padding:5px; text-decoration:none; text-align:center; font:bold 13px Verdana,Arial,Helvetica,sans-serif; width:100px; color:#FFF; outline-style:none; background-color: #5A5655; border:1px solid #5A5655; border-radius :5px;}
.txtBox{  border:1px solid #CCC;  color:#5A5655;  font:13px Verdana,Arial,Helvetica,sans-serif;  padding:2px;  width:430px}
.txtBox2{  border:1px solid #CCC;  color:#5A5655;  font:13px Verdana,Arial,Helvetica,sans-serif;  padding:2px;  width:143px}
.txtBox3{  border:1px solid #CCC;  color:#5A5655;  font:13px Verdana,Arial,Helvetica,sans-serif;  padding:2px;  width:70px}
.txtBox:focus,.txtBox2:focus,.txtBox3:focus{  border:1px solid #0C90BB}
.swMain .loader{  position:relative;   display:none;  float:left;   margin:2px 0 0 2px;  padding:8px 10px 8px 40px;  border:1px solid #FFD700;  font:bold 13px Verdana,Arial,Helvetica,sans-serif;  color:#5A5655;background:#FFF url(' . $C->Host . $stylelocation . '/loader.gif) no-repeat 5px;border-radius :5px;z-index:998}
.swMain .msgBox{ position:relative;  display:none; float:left; margin:4px 0 0 5px; padding:5px; border:1px solid #FFD700; background-color:#FFD;font:normal 12px Verdana,Arial,Helvetica,sans-serif; color:#5A5655;border-radius :5px;z-index:999; min-width:200px}
.swMain .msgBox .content{ font:normal 12px Verdana,Arial,Helvetica,sans-serif; padding:0px; float:left}
.swMain .msgBox .close{ border:1px solid #CCC; border-radius:3px; color:#CCC; display:block; float:right; margin:0 0 0 5px; outline-style:none; padding:0 2px 0 2px; position:relative; text-align:center; text-decoration:none}
.swMain .msgBox .close:hover{ color:#0C90BB; border:1px solid #0C90BB}
.loginForm{border:1px solid #CCC;color:#5A5655;font:13px Verdana,Arial,Helvetica,sans-serif;padding:2px}
.loginForm:focus{ border:1px solid #0C90BB}
#uploadbox{display:none;width:400px;border:10px solid #666;border:10px solid rgba(82,82,82,0.698);border-radius:8px;}
#uploadbox div{padding:10px;border:1px solid #202020;background-color:#fff;font-family:"lucida grande",tahoma,verdana,arial,sans-serif}
#uploadbox h2{margin:-11px;margin-bottom:0px;color:#fff;background-color:#202020;padding:5px 10px;border:1px solid #3B5998;font-size:20px}
div.feature02{position:relative}
div.feature02:hover{border-color:#' . $styletextcolor . ';cursor:pointer}
div.feature02:hover .overlay02{position:absolute;z-index:3;border:3px solid #' . $styletextcolor . ';width:50px;height:50px;font-size:24px}
div.feature02 .overlay02 .caption02,img.caption02{position:absolute;height:30px;line-height:30px;width:100%;z-index:3;text-indent:-9999em;color:#000;font-size:11px;bottom:0;overflow:hidden}
div.feature02:hover .caption01{text-indent:10px;background:rgb(17,180,233);background:rgba(17,180,233,0.75)}
div.feature02 img{display:block}
.msg{text-align:left; color:#666;background-repeat:no-repeat; margin-left:30px; margin-right:30px;padding:5px; padding-left:30px}
.emsg{text-align:left;margin-left:30px; margin-right:30px;color:#666;background-repeat:no-repeat;padding:5px; padding-left:30px}
#loader{ visibility:hidden}
#f1_upload_form{ height:100px}
#f1_error{ font-family:Geneva,Arial,Helvetica,sans-serif;font-size:12px; font-weight:bold; color:#F00}
#f1_ok{ font-family:Geneva,Arial,Helvetica,sans-serif;font-size:12px; font-weight:bold; color:#0F0}
#f1_upload_form{font-family:Geneva,Arial,Helvetica,sans-serif;font-size:12px;font-weight:normal;color:#666}
#f1_upload_process{ z-index:100; visibility:hidden; position:absolute; text-align:center; width:400px}
.messages_bound{background-color:#eeebea;border:1px solid #d7d4d3;padding:4px;margin:0px 0px 10px 0px;border-radius:3px;}
.messages_bound .header{color:#3f3f3f;font-size:18px;font-weight:bold;margin:5px}
.messages_bound .inner{font-size:11px;color:#777;background-color:#fff;border:1px solid #e1dedd;padding:6px}
#messages{min-height:350px}
#mes_header TABLE,.mes_row{padding:3px 0px 3px 0px}
#mes_header TABLE,.mes_row:hover{background:#f0f0f0}
#mes_header TABLE,.mes_row TABLE{width:100%}
#mes_header .mes_subj,.mes_row .mes_subj{width:350px;overflow:hidden}
#mes_header .mes_time,.mes_row .mes_time{width:175px;overflow:hidden}
.mes_compose{margin:5px}
.mes_to INPUT{width:98%;font-size:14px;border:1px solid #e1dedd;border-radius:4px;padding:4px}
.mes_subject INPUT{width:98%;font-size:14px;border:1px solid #e1dedd;border-radius:4px;padding:4px}
.mes_message TEXTAREA{width:98%;height:325px;font-size:14px;border:1px solid #e1dedd;border-radius:4px;padding:4px}
.mes_buttons{text-align:right;height:30px;margin-top:8px;border:solid #ccc;border-width:1px 0px 0px 0px;padding:5px}
.mes_buttons INPUT{background:#EEE;padding:5px;border:1px solid #e1dedd;border-radius:4px;cursor:pointer}
.conout{min-height:34px;font-size:11px;color:#777;background-color:#fff;border:1px solid #e1dedd}
.conout img{float:left;padding-left:5px;padding-top:2px;padding-bottom:2px;padding-right:10px}
.conout div{ padding-top:9px}
.redmsg{padding:5px;background-color:#F00;color:#FFF}
.pmquser{font-size:95%;font-weight:bold;margin-left:5px;opacity:0.9}
.pmquote{padding:5px;background:#EEE;border:1px solid #BBB;font-size:95%;margin:5px;opacity:0.9}
.message_pg{padding:8px;text-align:right}
.message_pg .pg_this{border:1px solid #888;padding:2px 5px 2px 5px;margin-right:3px;cursor:pointer}
.message_pg .pg_list{border:1px solid #888;padding:2px 5px 2px 5px;margin-right:3px;opacity:0.6;filter:alpha(opacity=60);cursor:pointer}
.message_pg .pg_list:hover{opacity:1.0;filter:alpha(opacity=100)}
.message_pg .pg_ellipse{padding:2px 5px 2px 5px;margin-right:3px;cursor:pointer}
.apple_overlay{display:none;background-image:url(' . $C->Host . $stylelocation . 'white.png);width:640px;padding:35px;font-size:11px}
.apple_overlay .close{background-image:url(' . $C->Host . $stylelocation . 'close.png);position:absolute;right:5px;top:5px;cursor:pointer;height:35px;width:35px}
.forum_button{float:right;font-size:13px;position:relative;overflow:hidden;padding:5px 0 0 0}
.forum_button span{}
.forum_button a{float:left;background:#FFF url(' . $C->Host . $stylelocation . 'forum_button.png) 105px 9px no-repeat; border:2px solid #747478;color:#555;display:inline-block;width:110px;padding:4px 0 6px 8px;text-align:left;margin-left:20px;border-radius:3px;text-decoration:none;}
.forum_button a:hover{background:#' . $styletextcolor . ' url(' . $C->Host . $stylelocation . 'forum_button.png) 105px -16px no-repeat;border:2px solid #' . $styletextcolor . ';color:#fff}
.oddrow{background-color:#E4E4E4}
.evenrow{background-color:#D3D3D3}
.erow{padding:5px}
.eleftcol{width:340px;float:left;padding:3px 5px 2px 2px}
.eepcol{width:20px;float:left;padding-right:2px;padding:3px 5px 2px 0}
.eseriescol{width:150px;float:left;padding:3px 5px 2px 0}
.eactioncol{padding:3px 5px 2px 0;float:right;width:80px}
.pcommodtxt{display:none}
#pcommod:hover .pcommodtxt{display:inline;float:left; z-index:1;margin-left:-37px;padding-bottom:30px}
#tracker{background-color:#CCC;padding:3px}
.tracker2{ z-index:0;background-color:#CCC;padding:3px}
.tracker2:hover{padding:2px;background-color:#FFF;border-color:#CCC;border-style:dashed;border-width:thin}
.tracker_more{display:none;}
.tracker2:hover .tracker_more{display:block;float:left; z-index:1;margin:5px 5px:0 0;background-color:#09F;}
.srightcol{float:right;width:360px;max-width:360px;margin-top:5px;background-color:c8c8c8;}
.sleftcol{width:250px;max-width:250px;}
.srow{padding:8px;background-color:#EEE;}
.srow2{padding:8px;}
.sactive{background-color:#4eff08;}
.sinactive{background-color:#ff0000;}
div.newnavbar{position:fixed;z-index:9999;background-color:rgba(0,0,0,0.8);float:right;text-align:top;margin-top:-20px;margin-left:20px;padding:5px 7px 5px 7px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;-webkit-box-shadow:0 8px 20px 0 #262626;box-shadow:0 8px 20px 0 #262626;}
.aftwNot{padding:0; padding-top:2px; position:relative; height:25px; margin-top:6px; display:inline-block; width:24px; cursor:pointer;}
.aftwU{padding:0 5px 0 0; padding-top:2px; position:relative; height:25px; margin-top:6px; display:inline-block; cursor:pointer; font-size:14px;}
.aftwNot img{ height:18px;padding:2px 2px 0 2px;}
.JewelNotif { height:11px; background-color:#f03d25; border-radius:2px; border:1px solid #d83722; -webkit-box-shadow: 0 0 1px 0 rgba(0, 0, 0, 1); line-height:11px; color:#fff; font-size:9px; position:absolute; top:-2px; left:15px; text-align:center; padding:0 1px 0 1px; z-index:1; display:none; }
.JewelNotif2 { height:11px; background-color:#f03d25; border-radius:2px; border:1px solid #d83722; -webkit-box-shadow: 0 0 1px 0 rgba(0, 0, 0, 1); line-height:11px; color:#fff; font-size:9px; position:absolute; top:-5px; left:15px; text-align:center; padding:0 1px 0 1px; z-index:1; display:none; }
.disBlock{display:block;}
/*notification dropdown st00f*/
.dropdown {padding:0 5px 0 0;position:relative;height:25px;margin-top:-15px;display:inline-block;width:24px;z-index:105;}
/* UL styles */
.dropdown ul {margin:15px 0 0 -130px;float:left;z-index:106;background:#000;background-color:rgba(0,0,0,0.85);display:none;list-style:none; padding:3px 0; width:290px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;border-top-left-radius:5px;border-top-right-radius:5px;-webkit-box-shadow:0 8px 20px 0 #262626;box-shadow:0 8px 20px 0 #262626;border:1px solid #666;}
.dropdown .selected {height:50px;background:url(' . $C->Host . $stylelocation . 'active-spritev2.png) repeat-x center;}
.dropdown ul li a:visited{text-decoration:none;}
.notificationHeader {background:#000;width:278px;font-size:10px;font-weight:bold;padding:6px;border-bottom:1px solid #666;}
.notificationFooter {background:#000;width:280px;font-size:12px;font-weight:bold;padding:5px;border-top:1px solid #666;}
.inotif {width:220px;font-size:11px;padding-top:5px;float:right;}
.mainli {width:280px;border-bottom:1px solid #333;padding: 3px 0 3px 0;font-size:11px;padding:2px 5px 2px 5px;}
.mainli:hover {background:#333333;background-color:rgba(0,0,0,0.35);cursor:pointer;}
/* Watchlist Styles */
#tbl-wl{font-size:11px;color:#555;background-color:#fff;padding:4px;min-height:80px;height:100%;}
#tbl-wl:hover{padding:3px;border-color:#CCC;border-style:dashed;border-width:thin;}
.whitelist_more{display:none;}
#tbl-wl:hover .whitelist_more{display:block;float:right;z-index:1;padding:5px;}
.all-rounded {border-radius: 5px;}
.spacer {display: block;}
#progress-bar {width: 300px;margin: 0 auto;background: #cccccc;border: 3px solid #f2f2f2;}
#progress-bar-percentage {background: #3063A5;padding: 5px 0px;color: #FFF;font-weight: bold;text-align: center;}
#pagingControls ul{display:inline;padding-left:0.2em}
#pagingControls li{display:inline;padding:0 0.2em}
.management-nav{font-size:9px;float:right;margin-top:-25px;}
.management-nav a{padding:2px 2px 0 2px;}
.management-nav a:hover{background-color: #' . $styletextcolor . ';border-radius:5px;}
#SizBar{margin:0 0 0 40px;}
#SizeBar a{text-decoration:none;}
#SizeBar a:hover{text-decoration:none;}
.availableOption{background-color: #999;color:#FFF;padding: 5px;font-size:12px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;}
.availableOption:hover{background-color: #' . $styletextcolor . ';text-decoration:none;color:#000;}
.ActiveType{background-color: #' . $styletextcolor . ';text-decoration:none;color:#FFF;}
.disabledOption{color:#EBEBEB;background-color: #CCC;padding: 5px;font-size:12px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;}
.disabledOption:hover{color:#EBEBEB;text-decoration:none;}
.FormError {display: none;float:right;width:200px;color:#000;}
/* Editor Classes */
#tinyeditor {border:none; margin:0; padding:0; font:14px \'Courier New\',Verdana}
.tinyeditor {border:1px solid #bbb; padding:0 1px 1px; font:12px Verdana,Arial}
.tinyeditor iframe {border:none; overflow-x:hidden}
.tinyeditor-header {height:31px; border-bottom:1px solid #bbb; background:url(' . $C->Host . '/editors/header-bg.gif) repeat-x; padding-top:1px}
.tinyeditor-header select {float:left; margin-top:5px}
.tinyeditor-font {margin-left:12px}
.tinyeditor-size {margin:0 3px}
.tinyeditor-style {margin-right:12px}
.tinyeditor-divider {float:left; width:1px; height:30px; background:#ccc}
.tinyeditor-control {float:left; width:34px; height:30px; cursor:pointer; background-image:url(' . $C->Host . '/editors/icons.png)}
.tinyeditor-control:hover {background-color:#fff; background-position:30px 0}
.tinyeditor-footer {height:32px; border-top:1px solid #bbb; background:#f5f5f5}
.toggle {float:left; background:url(' . $C->Host . '/editors/icons.png) -34px 2px no-repeat; padding:9px 13px 0 31px; height:23px; border-right:1px solid #ccc; cursor:pointer; color:#666}
.toggle:hover {background-color:#fff}
.resize {float:right; height:32px; width:32px; background:url(' . $C->Host . '/editors/resize.gif) 15px 15px no-repeat; cursor:s-resize}
#editor {cursor:text; margin:10px}
#aftw-player{z-index:9900;}
/* Profile Popup box styles */
.popbox {width:200px;}
.collapse { position:relative; }
.open {}
.box2 {display:block;display:none;background:#FFF;border:solid 1px #BBBBBB;border-radius:5px;box-shadow:0px 0px 15px #999;position:absolute;height:175px;padding:5px;}
.box a.close {color:red;font-size:12px;font-family:arial;text-decoration:underline;}
.arrow {width: 0;height: 0;border-left: 11px solid transparent;border-right: 11px solid transparent;border-bottom: 11px solid #FFF;position:absolute;left:1px;top:-10px;z-index:1001;}
.arrow-border {width: 0;height: 0;border-left: 11px solid transparent;border-right: 11px solid transparent;border-bottom: 11px solid #BBBBBB;position:absolute;top:-12px;z-index:1000;}
#video-wrapper {width:1200px;margin-top:10px;}
#video-left-column {display:inline-block;padding:5px;width:850px;vertical-align:top;}
.video-information {border:1px solid #e7e7e7;width:100%;height:100%;margin-top:10px;background-color:#eeeeee;border-radius:2px;}
.video-information-image {display:inline-block;vertical-align:top;margin:3px 2px 3px 5px;width:10%;}
.video-information-details {display:inline-block;vertical-align:top;margin:3px 3px 3px 2px;border-left:1px solid #e6e6e6;width:65%;}
.video-information-title {font-size:22px;padding:6px 10px 3px 10px;}
.video-information-series {font-size:14px;padding:3px 10px 3px 10px;}
.video-information-views {font-size:12px;padding:3px 10px 3px 10px;}
.video-information-ratings {display:inline-block;vertical-align:top;margin:3px 3px 3px 2px;width:20%;}
.video-information-subrate {border-bottom:#e6e6e6 1px solid;padding:3px 0 5px 15px;}
.video-information-tracker {margin-top:10px;}
.video-information-bottom {width:99%;height:50px;margin-top:5px;margin-left:5px;border-top:1px solid #e6e6e6;}
#video-right-column {display:inline-block;height:100%;width:320px;vertical-align:top;margin-left:10px;border:1px solid #e7e7e7;margin-top:5px;background-color:#eeeeee;border-radius:2px;}
.video-episodes {font-size:14px;padding:5px;}
.video-suggested-anime {font-size:14px;padding:5px;}
.video-comments {border:1px solid #e7e7e7;width:100%;height:100%;margin-top:10px;background-color:#eeeeee;border-radius:2px;}
.episode-list-wrapper {}
.episode-list-entry {margin:3px 0 3px 0px;padding:5px;text-decoration:none;border-radius:2px;}
.episode-list-entry:hover {background-color:#12b5ea;}
.episode-list-entry-image {display:inline-block;width:80px;vertical-align:top;}
.episode-list-entry-details {display:inline-block;width:200px;vertical-align:top;}
.episode-list-entry-epname {word-wrap: break-word;}
.episode-list-entry-subtext {color:#c1c1c1;}
.episode-list-entry:hover .episode-list-entry-subtext {color:white;}
.episode-list-entry-subtext a {text-decoration:none;color:#c1c1c1;}
.episode-list-entry:hover .episode-list-entry-subtext a {color:white;}
.next-episode-entry {background-color:#0b8ab3;}
.next-episode-entry .episode-list-entry-subtext {color:white;}
/* New Series Page enhancements */
.ui-tooltip, .arrow:after {background: black;border: 2px solid white;}
.ui-tooltip {padding: 10px 20px;color: white;border-radius: 20px;font: bold 14px "Helvetica Neue", Sans-Serif;text-transform: uppercase;box-shadow: 0 0 7px black;}
.ui-tooltip-content {width:200px;}
.qtip-wiki{max-width: 385px;}
.qtip-wiki p{margin: 0 0 6px;}
.qtip-wiki h1{font-size: 20px;line-height: 1.1;}
.qtip-wiki img{float: left;margin: 10px 10px 10px 0;}
.qtip-wiki .info{overflow: hidden;}
.qtip-wiki p.note{font-weight: 700;}
/* Profile Styles */
.user-settings-link-header{display:inline-block;border-top:#D1D1D1 1px solid; border-right:1px solid #D1D1D1; border-left:1px solid #D1D1D1;padding:0 5px 0 5px;border-top-left-radius:5px;border-top-right-radius:5px;}
.user-settings-link-header a {color:black;}
.header-active {background-color:#D1D1D1}
.full-session-row {padding:5px;border-left:1px solid #D1D1D1;border-bottom:1px solid #D1D1D1;border-right:1px solid #D1D1D1;}
.session-micro-row-left {display:inline-block;width:305px;vertical-align:top;}
.session-inside-row {padding:2px 0 2px 0;}
.session-left-column {display:inline-block;width:145px;text-align:right;padding-right:3px;}
.session-center-column {display:inline-block;width:145px;text-align:left;}
.session-micro-row-right {display:inline-block;width:100px;vertical-align:top;}
#upload-wrapper {width: 70%;margin-right: auto;margin-left: auto;margin-top: 50px;background: #F5F5F5;padding: 50px;border-radius: 10px;box-shadow: 1px 1px 3px #AAA;}
#upload-wrapper h3 {padding: 0px 0px 10px 0px;margin: 0px 0px 20px 0px;margin-top: -30px;border-bottom: 1px dotted #DDD;}
#upload-wrapper input[type=file] {border: 1px solid #DDD;padding: 6px;background: #FFF;border-radius: 5px;}
#upload-wrapper #submit-btn {border: none;padding: 10px;background: #61BAE4;border-radius: 5px;color: #FFF;}
#output{padding: 5px;font-size: 12px;}
#output img {border: 1px solid #DDD;padding: 5px;}
.dropit {list-style: none;padding: 0;margin: 0;}
.dropit .dropit-trigger{position: relative;}
.dropit .dropit-submenu{position:absolute;top:100%;left:0;z-index:1000;display:none;min-width:80px;list-style:none;padding: 0;margin: 0;}
.dropit .dropit-open .dropit-submenu{display: block;}
.download-menu ul { display: none; } /* Hide before plugin loads */
.download-menu ul.dropit-submenu {background-color: #fff;border: 1px solid #b2b2b2;padding: 3px 0;margin: 3px 0 0 1px;-webkit-border-radius: 3px;border-radius: 3px;-webkit-box-shadow: 0px 1px 3px rgba(0,0,0,0.15);box-shadow: 0px 1px 3px rgba(0,0,0,0.15);}  
.download-menu ul.dropit-submenu a {display: block;font-size: 14px;line-height: 25px;color: #7a868e;padding: 0 18px;}
.download-menu ul.dropit-submenu a:hover {background: #248fc1;color: #fff;text-decoration: none;}
#dot-container {  margin-top: -30px;  margin-left: -10px;  position: relative;  background: #45453f;}
.pulse {  width: 10px;  height: 10px;  border: 5px solid #f7f14c;  -webkit-border-radius: 30px;  border-radius: 30px;  background-color: #716f42;  z-index: 10;  position: absolute;}
.dot {  border: 10px solid #fff601;  background: transparent;  -webkit-border-radius: 60px;  border-radius: 60px;  height: 50px;  width: 50px;  -webkit-animation: pulse 3s ease-out;  -moz-animation: pulse 3s ease-out;  animation: pulse 3s ease-out;  -webkit-animation-iteration-count: infinite;  animation-iteration-count: infinite;  position: absolute;  top: -25px;  left: -25px;  z-index: 1;  opacity: 0;}
@-moz-keyframes pulse { 0% {-moz-transform: scale(0);opacity: 0.0; } 25% {-moz-transform: scale(0);opacity: 0.1; } 50% {-moz-transform: scale(0.1);opacity: 0.3; } 75% {-moz-transform: scale(0.5);opacity: 0.5; } 100% {-moz-transform: scale(1);opacity: 0.0; }}
@-webkit-keyframes "pulse" { 0% {-webkit-transform: scale(0);opacity: 0.0; } 25% {-webkit-transform: scale(0);opacity: 0.1; } 50% {-webkit-transform: scale(0.1);opacity: 0.3; } 75% {-webkit-transform: scale(0.5);opacity: 0.5; } 100% {-webkit-transform: scale(1);opacity: 0.0; }}
.transparent-arrow-down { min-width:10px;min-height:5px; background:url(\'data:image/png;base64,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\') no-repeat; text-align:middle;background-position: center; }
.transparent-arrow-up { min-width:10px;min-height:5px; background:url(\'data:image/png;base64,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\') no-repeat;background-position: center; }
.enhanced-user-sprites {background: url("' . $C->Host . $stylelocation . 'user-enhanced-sprite.png");display:inline-block;min-height:32px;min-width:32px;}
.user-sprite-gears {}
.user-sprite-notification {background-position:-32px 0px;}
.user-sprite-user {background-position:0px -32px;}
.user-sprite-lightbulb {background-position:-32px -32px;}
.series-listing-section{margin: auto;padding: 20px;width: 500px;position: relative;font-size: 20px;}
.series-listing-section-header-wrapper{height:20px;}
.series-listing-section-header{transition: all 0.4s ease;}
.sticky{font-size: 30px;background-color: rgba(200, 200, 200, 0.5);height: 40px;}
.sticky-active{position: fixed;top: 0;}
.sticky-parked{position: absolute;bottom: 0px;}
.series-entry-wrapper {border:1px solid #f3f3f3;}
.series-entry-wrapper:hover {border:1px solid #' . $styletextcolordark . '}
.mywatchlist-flag-sprite {background: url("' . $C->Host . $stylelocation . 'flag_sprite.png");display:inline-block;min-height:16px;min-width:16px;}
.mywatchlist-flag-watching {background-position:64px 0px;}
.mywatchlist-flag-planning {background-position:48px 0px;}
.mywatchlist-flag-finished {background-position:32px 0px;}
.mywatchlist-flag-untracked {background-position:16px 0px;}
.video-size-sprite {background: url("' . $C->Host . $stylelocation . 'video-size-sprite.png");display:inline-block;min-height:16px;min-width:23px;}
.video-size-480p {background-position:69px 0px;}
.video-size-720p {background-position:46px 0px;}
.video-size-1080p {background-position:23px 0px;}
.contains-a-movie {background: url("' . $C->Host . $stylelocation . 'movie-icon.png");display:inline-block;min-height:16px;min-width:23px;}
.airing-series {background: url("' . $C->Host . $stylelocation . 'airing-series-icon.png");display:inline-block;min-height:16px;min-width:16px;}
';
?>