<?php
/****************************************************************\
## FileName: config.php									 
## Author: Brad Riemann
## Version: 5.0.0
## Usage: Builds the static config information
## Copyright 2013 FTW Entertainment LLC, All Rights Reserved.
\****************************************************************/

// Initialize the array
$config = array();

/* Database Connection information */
$config['host'] 					= 'localhost';
$config['user'] 					= 'mainaftw_anime';
$config['pass'] 					= '26V)YPh:|IJG';
$config['table'] 					= 'mainaftw_anime';

/* Global Site specific settings */
$config['posting_comments']			= 1;
$config['comments_active']			= 1;
$config['videos_exclusive']			= 1;
$config['videos_active']			= 1;
$config['forums_active']			= 1;
$config['shop_active']				= 0;
$config['tracker_active']			= 0;

/* Debugging Settings */
$config['debugging'] 				= 0;

/* Staff aspect settings */
$config['upload_tracker_statuses'] 	= 'Claimed|Encoding|Uploading|Ongoing|Done|Live';
$config['application_round']		= 8;
$config['applications_status']		= 0;

/* Group Information */
$config['group_id'][0]				= 'Guest';
$config['group_id'][1]				= 'Administrator';
$config['group_id'][2]				= 'Manager';
$config['group_id'][3]				= 'Basic Member';
$config['group_id'][4]				= '';
$config['group_id'][5]				= 'Video Technician';
$config['group_id'][6]				= 'Forum Moderator';
$config['group_id'][7]				= 'Advanced Member';

/* MyWatchList Settings */
$config['watchlist_status'][1]		= 'Planning to Watch';
$config['watchlist_status'][2]		= 'Watching';
$config['watchlist_status'][3]		= 'Finished';
$config['watchlist_status'][4]		= 'Someday Maybe..';

/* Donation Setings */
$config['donation_active']			= 0;
$config['donation_round']			= 2;

/* Misc Settings */
$config['theme']					= 'default'; // Change the default theme, available are 'christmas', 'default', 'halloween'
$config['keywords']					= 'hd anime, anime, free, hd, high definition, hq, high quality, online, stream, streaming, manga, chat, mkv, bleach, naruto, divx anime, divx';
$config['site']						= 'AnimeFTW.tv';

?>