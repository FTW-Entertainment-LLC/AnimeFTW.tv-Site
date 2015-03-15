<?php
/*
This is the 'language' file.

It is provided so you can customize displayed words and phrases that are used in the comments 
display script.  You can adjust to suit YOUR word or language preferences and display words 
as you see fit.  

Be careful editing this file.
*/

// displayed above comments listing
$no_comments = "No comments have been provided.";
$comments_to_date = "Comments to date: "; 
$this_is_page = ". Page ";
$page_of_page = " of ";
$average_rating = " Average Rating: ";

// used when displaying comments where fields left empty
$unknown_poster = "Anonymous";
$unknown_location = "Location unknown";

// comment options for long posts and administrator comment identification
$show_more = "read more"; // for extended comments to link to separate page
$admin_comment = "<strong>Admin</strong>: "; // optional 

// next/previous image links text when displaying comments
$page_next = "Next";
$page_prev = "Previous";

//form input labels
$your_name = "Your Name:";
$your_location = "Your Location:";
$your_comments = "Your Comment:";
$rating_select = "Vote";

// article ratings - order from LOWEST rating to HIGHEST rating
$ratings = array("Choose", "Dreadful", "Poor", "Fair", "Good", "Excellent");// options in the rating dropdown

// what the comments SUBMIT form button says
$form_submit = "Add Comment";

// function to display posted date/time
// adjust this CAREFULLY to your preferred time/date format
// refer to the php manual date() function for alternatives
// the date in the database is stored as yyyy-mm-dd h:i:s 

function niceday($dated) {
	$dated = str_replace(array(" ",":"),"-",$dated);
	list($year,$month,$day,$hour,$minute) = explode("-",$dated);
	// you can edit this line to display date/time in your preferred notation
	$niceday = @date("g:ia \o\\n\ l, F jS, Y",mktime($hour,$minute,0,$month,$day,$year));
	echo $niceday;
}
?>