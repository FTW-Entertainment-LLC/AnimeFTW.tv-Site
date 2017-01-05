<?php
/****************************************************************\
## FileName: reviews.class.php									 
## Author: Brad Rimeann	 
## Usage: Handles anything related to the review system
## Copywrite 2014 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Review extends Config {
	var $UserArray;

	public function __construct()
	{
		parent::__construct();
	}
	
	public function connectProfile($input)
	{
		$this->UserArray = $input;
	}
	
	// Shows the reviews for a certain series.
	public function showSeriesReviews($sid)
	{
		$query = "SELECT `reviews`.`id`, `reviews`.`sid`, `reviews`.`uid`, `reviews`.`date`, `reviews`.`review`, `reviews`.`approved`, `reviews`.`stars`, `reviews`.`approved`, `reviews`.`approvedby`, `reviews`.`approvaldate`, `users`.`Username`, `users`.`avatarActivate`, `users`.`avatarExtension` FROM `reviews`,`users` WHERE `reviews`.`sid` = $sid AND `users`.`ID`=`reviews`.`uid` ORDER BY `reviews`.`date` DESC";
		
		$result = mysql_query($query);
		
		$count = mysql_num_rows($result);
		echo '<div style="padding-top:10px;border-top:1px solid #d8d8d8;">';
		echo '<div style="font-size:10px;color:#c0c0c0;">Reviews:</div><a name="series-reviews"></a>';
		echo '<div id="hidden-entry-point" style="display:none;"></div>';
		if($count < 1)
		{
			if($this->UserArray[0] == 1)
			{
				echo '<div style="font-size:20px;margin:10px;color:#d0d0d0;" align="center" id="missing-reviews-placeholder">This series is missing reviews.. Help by submitting a series review!</div>';
			}
			else
			{
				echo '<div style="font-size:20px;margin:10px;color:#d0d0d0;" align="center" id="missing-reviews-placeholder">This series needs reviews! Help AnimeFTW.tv today by registering and reviewing this series!</div>';
			}
		}
		else
		{
			while($row = mysql_fetch_assoc($result))
			{
				// Build the users avatar
				if($row['avatarActivate'] == 'no')
				{
					$avatar = '<img src="' . $this->Host . '/avatars/default.gif" alt="avatar" height="50px" border="0" />';
				}
				else
				{
					$avatar = '<img src="' . $this->Host . '/avatars/user' . $row['uid'] . '.' . $row['avatarExtension'] . '" alt="User avatar" height="60px" border="0" />';
				}
				
				if($row['approved'] == 0)
				{
					if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
					{
						$Approved = TRUE;
						$style = 'background-color:#FF9757;';
					}
					else
					{
						$Approved = FALSE;
						$style = '';
					}
					$approvedtitle = '<div><i>Approval Pending.</i></div>';
				}
				else if($row['approved'] == 1)
				{
					$Approved = TRUE;
					$style = '';
					$approvedtitle = '';
				}
				else if($row['approved'] == 2)
				{
					if($this->UserArray[2] == 1 || $this->UserArray[2] == 2)
					{
						$Approved = TRUE;
						$style = 'background-color:#fe8888;';
					}
					else
					{
						$Approved = FALSE;
						$style = '';
					}
					$approvedtitle = '<div><b>Review was denied.</b></div>';
				}
				else
				{
					$Approved = FALSE;
					$style = '';
					$approvedtitle = '';
				}				
				
				if($Approved == TRUE)
				{
					echo '
					<div class="single-comment-wrapper" style="margin:10px 0 10px 10px;' . $style . '">
						<div class="comment-wrapper" id="review-' . $row['id'] . '">
							<div class="comment-left" style="display:inline-block;width:10%;vertical-align:top;" align="center">
								<a href="/user/' . $row['Username'] . '">' . $avatar . '</a>
							</div>
							<div class="comment-right" style="display:inline-block;width:80%;vertical-align:top;margin-left:10px;">
								' . $approvedtitle . '
								<div>
									<div style="display:inline-block;">
										' . $this->formatUsername($row['uid']) . '
									</div>
									<div style="display:inline-block;margin-left:15px;">
										Reviewed on ' . date('l, F jS Y \a\t g:ia',$row['date']) . '
									</div>
									<div style="display:inline-block;margin-left:15px;">
										<img src="' . $this->Host . '/series-pages/' . $row['stars'] . 'star-rating.png" alt="" style="height:16px;" />
									</div>
								</div>
								<div>
									' . stripslashes($row['review']) . '
								</div>
							</div>
						</div>
					</div>';
				}
			}
		}
		if($this->UserArray[0] == 1)
		{
			$query = "SELECT `id`, `approved` FROM `reviews` WHERE `uid` = " . $this->UserArray[1] . " AND `sid` = $sid";
			$result = mysql_query($query);
			
			if(mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_assoc($result);
				if($row['approved'] == 2){
					// denied review
					echo '<div style="font-size:20px;margin:10px;color:#d0d0d0;" align="center" id="missing-reviews-placeholder">We\'re sorry, but your review was rejected.<br />Please PM a staff member for further information.</div>';
				}
				else if($row['approved'] == 1){
					// approved review
					echo '<div style="font-size:20px;margin:10px;color:#d0d0d0;" align="center" id="missing-reviews-placeholder">Thank you for your Successful review. We look forward to more reviews in the future!</div>';
				}
				else {
					// pending review
					echo '<div style="font-size:20px;margin:10px;color:#d0d0d0;" align="center" id="missing-reviews-placeholder">You currently have a pending review for this series, fear not, it should be approved soon.</div>';
				}
			}
			else
			{
				echo '
				<div id="root-review-wrapper">
					<div>
						<form id="series-review-form">
						<input type="hidden" name="id" value="' . $sid . '" />
						<div style="display:inline-block;width:92%;vertical-align:top;">
							<textarea style="width:99%;height:60px;" id="review-textarea" name="review-textarea" class="loginForm"></textarea>
						</div>
						<div style="display:inline-block;width:7%;vartical-align:top;" align="center">
							<div>
								<select id="rating-rated-select" name="rating-rated-select" class="loginForm">
									<option value="0">Rated</option>
									<option value="1">1 Star</option>
									<option value="2">2 Stars</option>
									<option value="3">3 Stars</option>
									<option value="4">4 Stars</option>
									<option value="5">5 Stars</option>
								</select>
							</div>
							<div style="padding-top:3px;">
								<input type="submit" id="submit-button-review" value="Submit" style="height:40px;width:60px;" />
							</div>
						</div>
						</form>
					</div>
					<div id="review-notice" style="display:none;">
						When Reviewing:<br />
						1. Note that users ​will see your review at the bottom of each series (unfiltered), please use language appropriate for all ages.<br />
						2. No Links are allowed, if links are submitted we will remove them as part of the review process.<br />
						3. Avoid using spoilers, while we understand that not everything can be filtered for a review, we ask that spoilers be kept to a minimum.<br />
						4. Do not use less than 80 words in your review, these are meant to be cumulative reviews of a series expressing opinions and thought about all episodes, not just one.<br />
						5. Have fun, we appreciate reviews as they add character to our site with fresh user submitted content.<br />
					</div>
					<script>
						$("#review-textarea").on("focus",function(){
							$("#review-notice").slideDown(200);
						});
					</script>
				</div>';
			}
		}
		echo '</div>';
		/*
				echo '
					<div class="single-comment-wrapper" style="margin:20px 0 20px 10px;">
						<div class="comment-wrapper" id="comment-' . $cid . '">
							<div class="comment-left" style="display:inline-block;width:10%;vertical-align:top;" align="center">
								<a href="/user/' . $row['Username'] . '">' . $avatar . '</a>
							</div>
							<div class="comment-right" style="display:inline-block;width:80%;vertical-align:top;margin-left:10px;">
								<div>
									<div style="display:inline-block;">
										' . $this->string_fancyUsername($row['uid']) . '
									</div>
									<div style="display:inline-block;margin-left:15px;">
										' . $dated . '
									</div>
								</div>
								<div>
									' . $comments . '
								</div>
							</div>
						</div>
					</div>';*/
	}
	
	public function processReview()
	{	
		// So the user may have gottne here, but we first, need to check to make sure they gave us all of the data, then to make sure that they haven't already given us a review.
		//print_r($_POST);
		if(!isset($_POST['id']) || (isset($_POST['id']) && !is_numeric($_POST['id'])) || !isset($_POST['review-textarea']) || !isset($_POST['rating-rated-select']) || $this->UserArray[0] == 0)
		{
			echo 'The data was not properly formatted, please try again.';
		}
		else
		{
			// so they pass the data check, let's make sure there are no reviews waiting to be processed, or already approved.
			$query = "SELECT * FROM `reviews` WHERE `sid` = " . mysql_real_escape_string($_POST['id']) . " AND `uid` = " . $this->UserArray[1];
			$result = mysql_query($query);
			
			$count = mysql_num_rows($result);
			
			if($count < 1)
			{
				// nothing here, let's submit the review!
				mysql_query("INSERT INTO `reviews` (`id`, `sid`, `uid`, `date`, `review`, `stars`, `approved`, `approvedby`, `approvaldate`) VALUES (NULL, '" . mysql_real_escape_string($_POST['id']) . "', '" . $this->UserArray[1] . "', " . time() . ", '" . mysql_real_escape_string($_POST['review-textarea']) . "', '" . mysql_real_escape_string($_POST['rating-rated-select']) . "', 0, 0, 0)");
				$reviewid = mysql_insert_id();
				$slackData = "*New Review Posted*: \n ```" . $_POST['review-textarea'] . "``` <https://www.animeftw.tv/manage/#reviews| Manage this review.>";
				$slack = $this->postToSlack($slackData);
				echo '<!-- Success -->';
				echo '
					<div class="single-comment-wrapper" style="margin:10px 0 10px 10px;background-color:#FF9757;border:1px solid #FF5E5E;">
						<div class="comment-wrapper" id="review-' . $reviewid . '">
							<div class="comment-left" style="display:inline-block;width:10%;vertical-align:top;" align="center">
								' . $this->formatAvatar($this->UserArray[1]) . '
							</div>
							<div class="comment-right" style="display:inline-block;width:80%;vertical-align:top;margin-left:10px;">
								<div><i>Review Pending Admin approval</i></div>
								<div>
									<div style="display:inline-block;">
										' . $this->formatUsername($this->UserArray[1]) . '
									</div>
									<div style="display:inline-block;margin-left:15px;">
										Reviewed on ' . date('l, F jS Y \a\t g:ia',time()) . '
									</div>
									<div style="display:inline-block;margin-left:15px;">
										<img src="' . $this->Host . '/series-pages/' . $_POST['rating-rated-select'] . 'star-rating.png" alt="" style="height:16px;" />
									</div>
								</div>
								<div>
									' . stripslashes(nl2br($_POST['review-textarea'])) . '
								</div>
							</div>
						</div>
					</div>';
			}
			else
			{
				echo 'Failed';
			}
			
		}
	}
}
