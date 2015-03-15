<?php
/*
The only reason for the configuration file is so that YOU can customize the 
script behaviour WITHOUT any need to edit any of the script files.
*/

/*
************* configuration of the comments display ***********************
This section of the configuration file contains CSS styles applied INLINE 
to some elements of the comments displayed.  Change them as you see fit.
For configuration of how posted dates are displayed see the php function in 
the comments-lang.php file
*/

// alternate row background colours for displayed comments
$ro1 = "#F7F7F7"; // odd rows
$ro2 = "#F7F7F7"; // even rows
$space_color = "#EAEAEA"; // divider line colour between comments

// CSS style applied to comments count line (24 comments on 2 pages, this is page 1 of 2, etc.
$num_style = "font-family: arial, helvetica, sans-serif; font-size:11px; color: #666666;";

// CSS style applied to next/previous type links on comments display
$pagelink_style = "border:1px solid #003; background-color:#ccf; text-decoration:none;";

// CSS style applied to displayed comments (in paragraph tags)
$comm_style = "margin:3px 5px 3px 5px; font-family: arial, helvetica, sans-serif; font-size:12px; color: #6B7272;";

/*
************* configuration of the comments form ***************************
*/

// NOTE: the comments input form takes its styles from those YOU provide for YOUR own pages

// show countries dropdown and flag (image) selected by poster
$show_flags = 0; // 1 = show country flags/show dropdown, 0 = disable feature

// show optional 'rating' of an article
$art_rating = 1; // 0=OFF, set to 1 to show rating dropdown in the form

// show 'rating' of an article by individual
$visitor_rating = 1; // 0=OFF, set to 1 to show rating by posters (only works if $art_rating=1)

// use captcha_lite as security image check
$captchas = 0; // set to 0 to skip display of captcha image


/*
************* configuration of the comments handling ***********************
*/

// turn off ability to post comments on ALL pages
$commoff = 0; // set to 1 to turn off comment form

// auto-approval (and posting) of comments
$is_approved = 1; // set to zero to allow you to decide to approve or not

// avoid insignificant comments
$useful = 4; // shortest acceptable comment length in characters

// include swear-checked posts to display (including baaaaad words)
$swearban = 1; // set to 0 to include posts that fail swear checking, otherwise set to 1

// YOUR email/domain name information for emails sent from the comments system 
$eaddr = "you"; // your email NAME for comment post notification
$domain = "animeftw.tv"; // your email DOMAIN for comment post notification
$mail_on = 0; // 1 = provide post notification, 0 = disable post notification

// set limit to number of comments displayed at a time - paginates automatically
$comment_limit = 10;

// avoid DISPLAYING all of VERY long posts
$maxshow_comments = 15000; // # of characters displayed before 'more' link

// flood control by IP - delay between consecutive posts
$flood_control = 0; // set to one to enable this form of flood control
$flood_delay = 300; // number of seconds 
$flood_page = 1; // flood control per page basis. Set to zero for all pages flood control

// flood control by IP - maximum posts in a period
$posts_flood = 1; // set to zero to disable this form of flood control
$posts_max = 40; // maximum number of posts in $posts_period
$posts_period = 1; // number of HOURS in flood control time

// tag rejection - posts containing html, script, etc. prohibited
$reject_tags = 1; // set to zero to allow posts with tags stripped

// link rejection - posts containing links prohibited
$reject_links = 1; // set to zero to allow link text to be posted


?> 
