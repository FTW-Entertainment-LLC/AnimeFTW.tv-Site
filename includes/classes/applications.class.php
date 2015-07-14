<?php
/****************************************************************\
## FileName: applications.class.php									 
## Author: Brad Riemann										 
## Usage: Applications Class and Functions
## Copywrite 2011-2012 FTW Entertainment LLC, All Rights Reserved
\****************************************************************/

class Applications extends Config {
	private $profileArray, $application_round, $applications_status;
	
	public function __construct($profileArray){
		parent::__construct();
		$this->profileArray = $profileArray;
		$this->ApplicationSettings();
	}
	
	public function Output(){
		if(isset($_GET['subnode']) && $_GET['subnode'] == 'apps'){
			$this->BuildApplication();
		}
		else if(isset($_GET['subnode']) && $_GET['subnode'] == 'sectest'){
			$this->BuildSecurityTest();
		}
		else if(isset($_GET['subnode']) && $_GET['subnode'] == 'show'){
			$this->ShowStaff();
		}
		else {
			echo 'This is not the page you are looking for....';
		}
	}
	
	private function ApplicationSettings(){
		$query = mysql_query("SELECT name, value FROM settings WHERE name = 'applications_status' OR name = 'application_round'");
		while($row = mysql_fetch_array($query, MYSQL_ASSOC)){
			if($row['name'] == 'applications_status'){
			$this->applications_status = $row['value'];
			}
			else if($row['name'] == 'application_round'){
				$this->application_round = $row['value'];
			}
			else {
			}	
		}		
	}
	
	private function BuildApplication(){
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Staff Applications - Application System</span>\n";
		echo "</div>\n";
		echo "<div class='side-body'>\n";
		if($this->profileArray[0] == 0){
			echo '<h3>You must be <a href="/register">Registered</a> &amp; <a href="/login">Logged</a> in to submit an application</h3>';
		}
		else {
			echo '	<script>
function agreesubmit(a){checkobj=a;if(document.all||document.getElementById){for(i=0;i<checkobj.form.length;i++){var b=checkobj.form.elements[i];if(b.type.toLowerCase()=="submit")b.disabled=!checkobj.checked}}}function defaultagree(a){if(!document.all&&!document.getElementById){if(window.checkobj&&checkobj.checked)return true;else{alert("Please read/accept terms to submit form");return false}}}var checkobj
</script>';
			if(!isset($_GET['step']) || (isset($_GET['step']) && $_GET['step'] == 1)){
				echo "<div align=\"center\">";
				$query  = "SELECT COUNT(username) AS numrows FROM applications_submissions WHERE username='".$this->profileArray[5]."' AND appRound='".$this->application_round."'";
				$result  = mysql_query($query);
				$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				$numrows = $row['numrows'];
				if ($numrows > 0 ){
					echo '<h3>You have already submitted an application this round.  Please wait by while we try to gather more applications, new staff will be announced soon.</h3>';
				}
				else if ($this->applications_status == 0 ){
					echo '<h3>Applications are now closed, we will contact all applicants by email based on the decision we make.</h3>';
				}
				else {	
					echo '<div align="center">Before we begin, you agree to be bound by AnimeFTW\'s Terms of Service to become a staff member.<br />Please be advised there is a hidden question, that WILL need to be answered before your application is submitted.</div><br /><br />';
					echo '<script>
					//change two names below to your form\'s names
					document.forms.agreeform.agreecheck.checked=false
					</script>
					<form action="/staff/applications/step-2" method="post" name="agreeform" onSubmit="return defaultagree(this)">
					  <textarea cols="75" rows="10" readonly="readonly">
	FTW Entertainment LLC. Provides our website accessible at animeftw.tv (the "Site") through which users can participate in an online community dedicated to enjoying streaming anime in DivX codec quality (the "Services"). Please read the following important terms and conditions ("Terms of Use") carefully. These Terms of Use and all policies referenced in this document or elsewhere on the Site which are incorporated herein by reference govern your access to and use of the Site and Services. These Terms of Use are a legal agreement between you, FTW Entertainment LLC & Animeftw.tv and apply to you whether you are a Registered Animeftw Member (defined below) or a visitor just browsing the Site (collectively, "AnimeFTW Guests"). These Terms of Use limit FTW Entertainment\'s liability and obligations to you, grant us certain rights and allow us to change, suspend or terminate your access to and use of the Site and Services.
	
	YOU UNDERSTAND THAT BY CLICKING ANY "LINKS", BY USING THE SITE, SERVICES OR YOUR ANIMEFTW MEMBERS ACCOUNT, YOU ARE AGREEING TO BE BOUND BY THESE TERMS OF USE. IF YOU DO NOT ACCEPT THESE TERMS OF USE IN THEIR ENTIRETY, YOU MAY NOT ACCESS OR USE THE SITE OR SERVICES. IF YOU AGREE TO THESE TERMS OF USE ON BEHALF OF A BUSINESS, YOU REPRESENT AND WARRANT THAT YOU HAVE THE AUTHORITY TO BIND THAT BUSINESS TO THESE TERMS OF USE AND YOUR AGREEMENT TO THESE TERMS WILL BE TREATED AS THE AGREEMENT OF THE BUSINESS. IN THAT EVENT, "YOU" AND "YOUR" WILL REFER AND APPLY TO THAT BUSINESS.
	Eligibility and Registration.
	
	All Anime is free to watch, member\'s accounts are also free, but certain perks i.e. Posting comments on videos, requires that you will have an AnimeFTW account and become an "Animeftw Member".
	
	When you register with Animeftw and set up your Animeftw account, you must provide FTW Entertainment with accurate and complete information. You agree to promptly update your Animeftw account information with any new information that may affect the operation of your Animeftw account. You authorize FTW Entertainment, directly or through third parties, to make any inquiries we consider necessary or appropriate to verify your Animeftw account information. Our Privacy Policy contains information about our policies and procedures regarding the collection, use and disclosure of information we receive from Animeftw Users.
	
	You will not use false identities or impersonate any other person or use a username or password that you are not authorized to use. FTW Entertainment reserves the right to require you to change your username for any reason and may do so at anytime.
	
	You are responsible for safeguarding and maintaining the confidentiality of your username, password and corresponding Animeftw account information. You agree not to disclose your password to anyone. You agree that you are entirely and solely responsible for any and all activities or actions that occur under your Animeftw account, whether or not you have authorized such activities or actions. You agree to immediately notify FTW Entertainment of any unauthorized use of your username, password or Animeftw account.
	Access to Certain Content: Fees and Billing.
	
	Animeftw Members may be able to access and/or purchase certain premium content ("Premium Content") through the Site or Services in a few different ways including: (i) Animeftw Members can pay a monthly subscription fee to view certain Premium Content for the applicable time period ("Subscription Fee"); or (ii) Animeftw Members can pay a one time fee to download certain Premium Content for unlimited viewing ("Download Fee"). Fees are refundable in FTW Entertainment\'s sole discretion. There is no prorated refund of any fees upon any termination or cancellation. You agree to immediately pay any amounts accrued, but remaining unpaid, as of termination (if any).
	
	You hereby authorize FTW Entertainment to collect fees by charging the credit card you provide to us as part of your Animeftw account information, either directly or indirectly via a third party online payment service, such as PayPal.
	Proprietary Rights and Licenses.
	
	(a) Definitions. Certain content and materials are made available through the Site and Services, including the following:
	
	"Animeftw Content" means, collectively, the text, data, graphics, images, FTW Entertainment trademarks and logos and other content (including Licensed Content) made available through the Site and Services, excluding User Submissions.
	
	"Licensed Content" means any content (including any audio or video content) provided to FTW Entertainment by its third party content partners (which may include Premium Content or "download to own" content) made available through the Site and Services.
	
	"User Submissions" means, collectively, the text, data, communications, bulletin board messages, chat, graphics, images, photos, audio or video files and other content and information which Animeftw Members post, upload and otherwise submit to the Site or Services, including, without limitation, in their profile page, excluding Translated Content.
	
	(b) User Submissions. You retain all rights in your User Submissions. However, by uploading, posting, submitting or otherwise transmitting any User Submissions on or to the Site or Services, you hereby grant to FTW Entertainment a non-exclusive, worldwide, royalty-free, sublicensable, perpetual and irrevocable right and license to use, reproduce, modify, distribute, prepare derivative works of, display, publish, perform and transmit your User Submissions in connection with the Services and FTW Entertainment\'s (and its successors) business including, without limitation, for promotion and redistributing part or all of the Services (and derivative works thereof), in any media formats and through any media channels. You represent and warrant that you own or have the necessary licenses, rights, consents and permissions to grant the foregoing licenses to FTW Entertainment. You acknowledge and agree that your posting of User Submissions will comply with our Copyright and IP Policy as set forth in Section 4 below.
	
	You acknowledge and agree that FTW Entertainment may, at its option, reclassify or recatergorize any User Submissions and establish limits concerning User Submissions, including, without limitation, the maximum number of days that User Submissions will remain available via the Services or on the Site, the maximum size of any files that may be stored on or uploaded to the Site or Services and the maximum disk space that may be allotted to you for the storage of User Submissions on FTW Entertainment\'s servers. FTW Entertainment will have no responsibility or liability for maintaining copies of User Submissions on our servers, and you are solely responsible for creating back-ups of your User Submissions.
	
	(c) Animeftw Content.
	
	(i) FTW Entertainment and its licensors own all right, title and interest, including all worldwide intellectual property rights in the Site, Services, Animeftw Content and any other content made available through the Site or Services contained therein, other than your User Submissions. You will not remove, alter or conceal any copyright, trademark, service mark or other proprietary rights notices incorporated in or accompanying the Site, Services, Animeftw Content, any other content made available through the Site or Services or related products and services, and except as explicitly described herein, you will not reproduce, modify, adapt, prepare derivative works based on, perform, display, publish, distribute, transmit, broadcast, sell, license or otherwise exploit the Site, Services, Animeftw Content or any other content made available through the Site or Services (other than your User Submissions).
	
	(ii) Subject to Section 3(c)(i), from time to time, FTW Entertainment may permit Animeftw Members to create translations of certain Licensed Content ("Translated Content") subject to the underlying rights of our third party content providers. Any activity in this regard will be governed by additional terms and conditions ("Translation Terms") that will be provided to Animeftw Members in advance of their having access to any Licensed Content for this limited purpose. Any Translation Terms will form part of these Term of Use and be subject to the terms and conditions contained herein. To the extent that there are any conflicts or inconsistencies between these Terms of Use and the Translation Terms, the provisions of the Translation Terms will govern and control.
	
	(d) Reminder Regarding Premium Content. For the avoidance of doubt with respect to any Premium Content that you purchase or access via the Site or Service, such content is only made available for personal and non-commercial purposes. The delivery of any Premium Content to you neither transfers any commercial or promotional use rights in the content to you nor does it constitute a grant or waiver of any rights of the copyright owners in any audio or video content, sound recording, underlying musical composition, or artwork embodied in the content.
	
	(e) Disclaimer. FTW Entertainment does not guarantee that any content (including without limitation Animeftw Content, User Submissions or Translated Content) will be made available through the Site or Services, continuously or at all. WHILE FTW ENTERTAINMENT IS UNDER NO OBLIGATION TO DO SO, FTW ENTERTAINMENT RESERVES THE RIGHT TO REMOVE AND PERMANENTLY DELETE ANY CONTENT FROM THE SITE OR SERVICES WITHOUT NOTICE, AND FOR ANY REASON FTW ENTERTAINMENT DEEMS SUFFICIENT. FTW Entertainment does not have any obligation to monitor the User Submissions or Translated Content that is uploaded, posted, submitted or otherwise transmitted using the Site or Services, for any purpose and, as a result, is not responsible for the accuracy, completeness, appropriateness, legality or applicability of the User Submissions or Translated Content or anything said, depicted or written by Animeftw Members, including, without limitation, any information obtained by using the Site or Services. FTW Entertainment does not endorse any User Submissions or Translated Content or any opinion, recommendation or advice expressed therein and you agree to waive, and hereby do waive, any legal or equitable rights or remedies you have or may have against FTW Entertainment with respect thereto.
	Copyrighted Materials: No Infringing Use.
	
	You will not use the Site or Services to offer, display, distribute, transmit, route, provide connections to or store any material that infringes copyrighted works or otherwise violates or promotes the violation of the intellectual property rights of any third party. FTW Entertainment has adopted and implemented a policy that provides for the termination in appropriate circumstances of the accounts of users who repeatedly infringe or are believed to be or are charged with repeatedly infringing the rights of copyright holders. Please see the FTW Entertainment Copyright Policy for further information.
	Termination or Suspension of the Site or Services & Modification of these Terms of Use.
	
	FTW Entertainment reserves the right in its sole discretion, at any time, to modify, discontinue or terminate the Site or Services or to modify or terminate these Terms of Use without advance notice. Modifications to these Terms of Use or any policies will be posted on the Site or made in compliance with any notice requirements set forth in these Terms of Use. If any modification is not acceptable to you, your only recourse is to cease using the Site and Services. By continuing to use the Site or Services after FTW Entertainment has posted any modifications on the Site or provided any required notices, you accept and agree to be bound by the modifications.
	
	Without limiting other remedies, FTW Entertainment may at any time suspend or terminate your Animeftw account and refuse to provide access to the Site or Services. In addition, FTW Entertainment may notify authorities or take any actions it deems appropriate, without notice to you, if FTW Entertainment suspects or determines, in its own discretion, that you may have or there is a significant risk that you have (i) failed to comply with any provision of these Terms of Use or any policies or rules established by FTW Entertainment; or (ii) engaged in actions relating to or in the course of using the Site or Services that may be illegal or cause liability, harm, embarrassment, harassment, abuse or disruption for you, Animeftw Users, FTW Entertainment or any other third parties or the Site or Services.
	
	You may terminate your Animeftw account at any time and for any reason by selecting this option on your account information page. Upon any termination by a Animeftw Member, the related account will no longer be accessible.
	
	After any termination, you understand and acknowledge that we will have no further obligation to provide the Site or Services and all licenses and other rights granted to you by these Terms of Use will immediately cease. FTW Entertainment will not be liable to you or any third party for termination of the Site or Services or termination of your use of either. UPON ANY TERMINATION OR SUSPENSION, ANY CONTENT, MATERIALS OR INFORMATION (INCLUDING USER SUBMISSIONS OR TRANSLATED CONTENT) THAT YOU HAVE SUBMITTED ON THE SITE OR THAT WHICH IS RELATED TO YOUR ACCOUNT MAY NO LONGER BE ACCESSED BY YOU. Furthermore, FTW Entertainment will have no obligation to maintain any information stored in our database related to your account or to forward any information to you or any third party.
	
	Any suspension, termination or cancellation will not affect your obligations to FTW Entertainment under these Terms of Use (including, without limitation, proprietary rights and ownership, indemnification and limitation of liability), which by their sense and context are intended to survive such suspension, termination or cancellation.
	Interactions between Animeftw Users.
	
	You are solely responsible for your interactions (including any disputes) with other Animeftw Users. You understand that FTW Entertainment does not in any way screen Animeftw Users. You are solely responsible for, and will exercise caution, discretion, common sense and judgment in, using the Site and Services and disclosing personal information to other Animeftw Users. You agree to take reasonable precautions in all interactions with other Animeftw Users, particularly if you decide to meet a Animeftw Users offline, or in person. Your use of the Site, Services, Animeftw Content and any other content made available through the Site or Services is at your sole risk and discretion and FTW Entertainment hereby disclaims any and all liability to you or any third party relating thereto. FTW Entertainment reserves the right to contact Animeftw Members, in compliance with applicable law, in order to evaluate compliance with the rules and policies in these Terms of Use. You will cooperate fully with FTW Entertainment to investigate any suspected unlawful, fraudulent or improper activity, including, without limitation, granting authorized FTW Entertainment representatives access to any password-protected portions of your Animeftw account.
	Obligations for Animeftw Users.
	
	The Site and Services may be used and accessed for lawful purposes only. You agree to abide by all applicable local, state, national and foreign laws, treaties and regulations in connection with your use of the Site and Services. In addition, without limitation, you agree that you will not do any of the following while using or accessing the Site or Services:
	
	Circumvent, disable or otherwise interfere with security related features of the Site or features that prevent or restrict use or copying of any content;
	
	Use any meta tags or other hidden text or metadata utilizing a FTW Entertainment trademark or logo, URL or product name;
	
	Forge any TCP/IP packet header or any part of the header information in any posting or in any way use the Site or Services to send altered, deceptive or false source-identifying information;
	
	Interfere with or disrupt (or attempt to interfere with or disrupt) any web pages available at the Site, servers or networks connected to the Site, Services or the technical delivery systems of FTW Entertainment\'s providers or disobey any requirements, procedures, policies or regulations of networks connected to the Site or Services;
	
	Attempt to probe, scan or test the vulnerability of any FTW Entertainment system or network or breach or impair or circumvent any security or authentication measures protecting the Site or Services;
	
	Attempt to decipher, decompile, disassemble or reverse engineer any of the software used to provide the Site or Services;
	
	Attempt to access, search or meta-search the Site with any engine, software, tool, agent, device or mechanism other than software and/or search agents provided by FTW Entertainment or other generally available third party web browsers (such as Microsoft Internet Explorer, Mozilla Firefox, Safari or Opera), including without limitation any software that sends queries to the Site to determine how a website or web page ranks;
	
	Collect or store personal data about other Animeftw Users without their express permission;
	
	Impersonate or misrepresent your affiliation with any person or entity, through pretexting or some other form of social engineering or otherwise commit fraud;
	
	Use the Site or Services in any manner not permitted by these Terms of Use; or
	
	Encourage or instruct any other individual to do any of the foregoing or to violate any term of these Terms of Use.
	Sweepstakes and Contests.
	
	(a) Animeftw Promotions. FTW Entertainment may operate sweepstakes, contests and similar promotions (collectively, "Promotions") through the Site. You should carefully review the rules (e.g., the "Official Rules") of each Promotion in which you participate through the Site, as they may contain additional important information about FTW Entertainment\'s rights to and ownership of the submissions you make as part of the Promotions and as a result of your participation in such Promotion. To the extent that the terms and conditions of such Official Rules conflict with these Terms of Use, the terms and conditions of such Official Rules will control.
	
	(b) Animeftw Member Promotions. You must be at least 18 years of age to run a Promotion via the Site or Services. In the event you choose to run a Promotion, you acknowledge and agree to the following:
	
		* You are solely responsible for:
			  o conducting such Promotions according to all applicable laws and regulations as they generally are interpreted and enforced;
			  o preparing and publishing official rules and appropriate legal disclaimers, registering and bonding the Promotion where required; and
			  o handling submission of Promotion entries, judging Promotion entries, furnishing Promotion prizes, and fulfilling requests for winners\' lists.
		* FTW Entertainment will have no liability to any Promotion winners, any Animeftw Users, or other third parties for any harm, damages, costs, or expenses related to their participation in the Promotion, or their claiming, acceptance, or use of any prize.
		* You will ensure that Promotion materials, including official Promotion rules, contain language that: (i) disclaims any and all warranties on FTW Entertainment\'s behalf with respect to the Promotion in question and any prize, and (ii) releases FTW Entertainment from liability to any winners, Animeftw Users, or other third parties for any harm, damages, costs, or expenses related to their participation in the Promotion, or their claiming, acceptance, or use of any prize.
		* You are solely responsible for securing appropriate affidavits of eligibility, liability and publicity releases and travel companion releases if appropriate (collectively "Releases"), which release you and FTW Entertainment from any and all liability as set forth in the Releases, from winners before providing winners with Promotion prizes.
		* You are responsible for all costs associated with fulfillment of the prizes and your obligations under these Terms of Use. At FTW Entertainment\'s request, Partner will permit FTW Entertainment to review all Promotion materials. FTW Entertainment may make changes to the Promotion materials in furtherance of its interests and may terminate any Promotions at any time and for any reason in its sole discretion.
		* FTW Entertainment is not responsible for the administration and operation of any Promotions run, sponsored or administrated by you. FTW Entertainment hereby disclaims any and all liability relating thereto.
	
	Ratings and Comments & Feedback.
	
	Comments. You can rate and make comments about content made available through the Site or Services ("Comments"). FTW Entertainment advises you to exercise caution and good judgment when leaving such Comments. Once you complete and submit your Comments to the Site or Services you will not be able to go back and edit your Comments. You should also be aware that you could be held legally responsible for damages to someone\'s reputation if your Comments are deemed to be defamatory. FTW Entertainment may, but is under no obligation to, monitor or censor Comments and disclaims any and all liability relating thereto. Notwithstanding the foregoing, FTW Entertainment does reserve the right, in its sole discretion, to remove any Comments that it deems to be improper, inappropriate or inconsistent with the online activities that are permitted under these Terms of Use.
	
	Feedback. We welcome and encourage you to provide feedback, comments and suggestions for improvements to the Site and Services ("Feedback"). You may submit Feedback by emailing us at brad a[t] ftwentertainment [d]o[t] com or through the "Members" section of the Site. You acknowledge and agree that all Comments and all Feedback will be the sole and exclusive property of FTW Entertainment and you hereby assign and agree to assign all rights, title and interest you have in such Comments and Feedback to FTW Entertainment together with all intellectual property rights therein.
	Indemnification.
	
	You agree to defend, indemnify and hold FTW Entertainment and its affiliates, subsidiaries and distribution partners and their respective officers, directors, employees and/or agents harmless from and against any claims, liabilities, damages, losses and expenses, including, without limitation, reasonable attorneys\' fees and costs, arising out of or in any way connected with: (i) your access to or use of the Site, Services, Animeftw Content, User Submissions or Translated Content; (ii) your violation of these Terms of Use; (iii) your violation of any third party right, including without limitation any intellectual property right, publicity, confidentiality, property or privacy right; or (iv) any claim that any content you posted to the Site or via the Services (including without limitation your User Submissions or any Translated Content) caused damage to a third party, including without limitation claims that your User Submissions or any Translated Content are infringing. As to (i), (iii) and (iv) in this Section 10, your obligation to indemnify FTW Entertainment applies to your activities on the Site at any time.
	Disclaimer.
	
	THE SITE, SERVICES, FTW ENTERTAINMENT CONTENT AND ANY OTHER CONTENT MADE AVAILABLE THROUGH THE SITE OR SERVICES ARE PROVIDED "AS IS" WITH NO WARRANTY OF ANY KIND. FTW ENTERTAINMENT EXPRESSLY DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, REGARDING THE SITE, SERVICES, ANIMEFTW CONTENT AND ANY OTHER CONTENT MADE AVAILABLE THROUGH THE SITE OR SERVICES, INCLUDING ANY IMPLIED WARRANTY OF QUALITY, AVAILABILITY, MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR NON-INFRINGEMENT. IN ADDITION, FTW ENTERTAINMENT MAKES NO REPRESENTATION OR WARRANTY THAT THE SITE, SERVICES, ANIMEFTW CONTENT OR ANY OTHER CONTENT MADE AVAILABLE THROUGH THE SITE OR SERVICES WILL BE ERROR FREE OR THAT ANY ERRORS WILL BE CORRECTED. SOME STATES OR JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF CERTAIN WARRANTIES, SO SOME OF THE ABOVE EXCLUSIONS MAY NOT APPLY TO YOU.
	Limitation of Liability.
	
	UNDER NO CIRCUMSTANCES WILL FTW ENTERTAINMENT OR ITS AFFILIATES, SUBSIDIARIES, PARTNERS OR LICENSORS OR ANY OF THEIR RESPECTIVE OFFICERS, DIRECTORS, EMPLOYEES AND/OR AGENTS BE LIABLE TO YOU OR ANY THIRD-PARTY FOR ANY INDIRECT, INCIDENTAL, CONSEQUENTIAL, SPECIAL OR EXEMPLARY DAMAGES ARISING OUT OF OR IN CONNECTION WITH USE OF THE SITE, SERVICES, FTW ENTERTAINMENT CONTENT AND ANY OTHER CONTENT MADE AVAILABLE THROUGH THE SITE OR SERVICES INCLUDING, WITHOUT LIMITATION, INJURY OR DAMAGES RESULTING FROM THE CONDUCT OF ANY ANIMEFTW USER, ONLINE OR OFFLINE, WHETHER OR NOT FTW ENTERTAINMENT HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. WITHOUT LIMITING THE GENERALITY OF THE FOREGOING, FTW ENTERTAINMENT\'S AGGREGATE LIABILITY TO YOU ARISING WITH RESPECT TO THESE TERMS OF USE WILL NOT EXCEED $50. FTW ENTERTAINMENT WILL NOT BE LIABLE FOR THE CONSEQUENCES OF ANY INTERRUPTIONS OR ERRORS RELATING TO THE SITE, SERVICES OR THE SCHEDULED OR UNSCHEDULED SERVICE INTERRUPTIONS. SOME STATES DO NOT ALLOW THE EXCLUSION OR LIMITATION OF INCIDENTAL OR CONSEQUENTIAL DAMAGES, SO THE ABOVE LIMITATION AND EXCLUSIONS MAY NOT APPLY TO YOU.
	Force Majeure.
	
	FTW Entertainment will not be liable to you by reason of any failure or delay in the performance of its obligations hereunder on account of events beyond its reasonable control, which may include, without limitation, denial-of-service attacks, strikes, shortages, riots, insurrection, fires, flood, storm, explosions, acts of God, war, terrorism, governmental action, labor conditions, earthquakes, material shortages, failure of the internet or extraordinary connectivity issues experienced by major telecommunications providers and unrelated to FTW Entertainment infrastructure or connectivity to the internet or failure at an FTW Entertainment co-location facility (each a "Force Majeure Event"). Upon the occurrence of a Force Majeure Event, FTW Entertainment will be excused from any further performance of its obligations effected by the Force Majeure Event for so long as the event continues, and for such further period of time that FTW Entertainment may reasonably require to recover from the effects of such Force Majeure Event.
	Relationship of the Parties.
	
	Notwithstanding any provision hereof, for all purposes of these Terms of Use each party will be independent and act independently and not as a contractor, partner, joint venturer, agent, employee or employer of the other and will not bind nor attempt to bind the other to any contract.
	Notice; Consent to Electronic Notice.
	
	You consent to the use of (a) electronic means to complete these Terms of Use and to deliver any notices pursuant to these Terms of Use and (b) electronic records to store information related to these Terms of Use or your use of the Site or Services. Any notice or other communication to be given hereunder will be in writing and given (x) by FTW Entertainment via email (in each case to the address that you provide), (y) a posting on the Site or (z) by you via email to brad a[t] ftwentertainment [d]o[t] com or to such other addresses as FTW Entertainment may specify in writing. The date of receipt will be deemed the date on which such notice is transmitted.
	Links to other Websites.
	
	The Site or Services may provide links to websites other than Animeftw.tv. Such links are provided for reference only, and FTW Entertainment neither controls such websites nor endorses any of the material on any such websites or any association with their operators. FTW Entertainment is not responsible for the activities or such sites, and has no liability to you for any harms, injuries or losses you might suffer as a result of using or accessing such websites.
	Miscellaneous.
	
	In the event that any provision in these Terms of Use is held to be invalid or unenforceable, the unenforceable part will be given effect to the greatest extent permitted by law and the remaining provisions will remain in full force and effect to the maximum extent permitted by law. The failure of a party to enforce any right or provision of these Terms of Use will not be deemed a waiver of such right or provision. You may not assign or transfer these Terms of Use (by operation of law or otherwise) without the prior written consent of FTW Entertainment and any prohibited assignment will be null and void. FTW Entertainment may assign these Terms of Use or any rights hereunder without your consent. These Terms of Use will be governed by and interpreted in accordance with the laws of the State of California excluding that body of law pertaining to conflict of laws. Any legal action or proceeding arising under these Terms of Use will be brought exclusively in courts located in Northern California and the parties hereby irrevocably consent to the personal jurisdiction and venue therein. You agree that these Terms of Use and the rules, restrictions and policies contained herein, and FTW Entertainment\'s enforcement thereof, are not intended to confer and do not confer any rights or remedies upon any person other than you and FTW Entertainment. These Terms of Use together with the rules and policies of FTW Entertainment incorporated herein by reference constitute the entire agreement between FTW Entertainment and you with respect to the subject matter of these Terms of Use.
	Questions.
	
	If you have questions about these Terms of Use or would like to request a copy of these Terms of Use or any other records relating to these Terms of Use or your use of the Site and Services, please contact FTW Entertainment by emailing us at brad a[t] ftwentertainment [d]o[t] com or by Using the member contact form..
	
	SPECIAL WORD IS: gravyowns
	
	if you were smart and read that, pat yourself on the back, you will need it later.</textarea>

					<input name="step1" type="hidden" value="yes" /><br /><br />
					<input id="agreecheck" name="agreecheck" type="checkbox" onClick="agreesubmit(this)">&nbsp;<b><label for="agreecheck" style="display:inline;color:#555;">I agree to the above terms</label></b><br /><br>
					<input type="Submit" value="Continue to Step 2" disabled="disabled" />
					</form>';
				}
			}
			else if($_GET['step'] == 2){
				if ($_POST['step1'] == 'yes'){
					$Pass = TRUE;
				}
				else {
					$Pass = FALSE;
				}
				
				if($Pass == FALSE){
					echo '<div align="center"><h4>Please go back to <a href="/staff/applications/step-1">Step One</a> and start over, you cannot skip the agreement.</h4></div>';
				}
				else {
					echo '<div align="center">Welcome to Step 2 in the application process, below is a list of the available positions that we are looking for at AnimeFTW. If you rollover a given name it will supply a brief description of the selected position.<br /><br />Please note, all attempts to skip ahead in the process will be recorded, and you WILL be banned from applying for staff.</div><br>';
					echo '<script>
						//change two names below to your form\'s names
						document.forms.step2form.step2check.checked=false
						</script>';
					echo '<form method="post" action="/staff/applications/step-3" name="step2form" onSubmit="return defaultagree(this)">';
					echo '<table width="100%" border="0" cellspacing="1" cellpadding="3">';
					echo '<tr>
						<td width="18%" align="center" valign="top" colspan="2" nowrap><span style="font-size:14px;">Position</span><br /><span style="font-size:8px;">(Click the Position for Duties)</span></td>
						<td width="2%">&nbsp;</td>
						<td width="85%" align="center" valign="top"><span style="font-size:14px;">Requirements</span></td>
					</tr>';
					$query   = "SELECT id, position, duties, requirements FROM applications_positions WHERE online_offline='online' ORDER BY id";
					$result  = mysql_query($query) or die('Error : ' . mysql_error());
	
					while(list($id,$position,$duties,$requirements) = mysql_fetch_array($result)){
					$position = stripslashes($position);
					$duties = stripslashes($duties);
					$requirements = stripslashes($requirements);
						echo '<tr>
								<td><input name="positionticked" value="'.$position.'" type="radio" onClick="agreesubmit(this)"></td>';
								echo '<td nowrap><a href="#" onClick="$(\'#Position-'.$id.'\').toggle();return false;">'.$position.'</a></td>
								<td>&nbsp;</td>
								<td>'.$requirements.'</td>
							  </tr>';
						echo '<tr id="Position-'.$id.'" style="display:none;">
							<td colspan="4">
							<div style="padding-left:30px;"><span style="font-size:14px;">Duties</span><br />
							'.$duties.'
							</div>
							</td>
						</tr>
							  <tr>
							  <td>&nbsp;</td>
							  <td colspan="3">&nbsp;</td>
							  </tr>';
					}  
					echo '</table><br />';
					echo '<div align="center"><input name="step2" type="hidden" value="yes" /><input type="Submit" value="Continue to Step 3" disabled></div>';
				}
			}
			else if($_GET['step'] == 3){
				if ($_POST['step2'] == 'yes'){
					$Pass = TRUE;
				}
				else {
					$Pass = FALSE;
				}
				
				if($Pass == FALSE){
					echo '<div align="center"><h4>Please go back to <a href="/staff/applications/step-1">Step One</a> and start over, you cannot skip anything.</h4></div>';
				}
				else {
					echo '<div align="center">Welcome to Step 3 in the application process, below is the list of requirements AND duties for when you will be staff.  Please read them over, and check the check box to the left when you agree.</div><br>';
					echo '<script>
						//change two names below to your form\'s names
						document.forms.step3form.step3check.checked=false
						</script>';
					echo '<form method="post" action="/staff/applications/step-4" name="step3form" onSubmit="return defaultagree(this)">';
					$query   = "SELECT position, duties, requirements FROM applications_positions WHERE position='".mysql_real_escape_string($_POST['positionticked'])."'";
					$result  = mysql_query($query) or die('Error : ' . mysql_error()); 
					$row     = mysql_fetch_array($result, MYSQL_ASSOC);
				
					$position = $row['position'];
					$duties = $row['duties'];
					$requirements = $row['requirements'];
					$position = stripslashes($position);
					$duties = stripslashes($duties);
					$requirements = stripslashes($requirements);
					echo 'You have chosen to apply for:<br /> <h2>'.$position.'</h2><br /> the following are the Duties and Requirments for being a(n) '.$position.'<br />';	
					echo '<table width="100%" border="0" cellspacing="1" cellpadding="3">';
					echo '<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td width="20%" align="center"><h3>Requirements</h3></td>
							<td>'.$requirements.'</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td width="20%" align="center"><h3>Duties</h3></td>
							<td>'.$duties.'</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						  <tr>
							<td colspan="2"><input id="step3agree" name="step3agree" type="checkbox" value="step3agree" onClick="agreesubmit(this)">&nbsp; <label for="step3agree" style="display:inline;color:#555;">I Agree to the above Requirements and Duties.</label></td>
						  </tr>'; 
					echo '</table><br />';
					echo '<div align="center"><input name="step3" type="hidden" value="yes" />
					<input name="positionticked" type="hidden" value="'.$_POST['positionticked'].'" />
					<input type="Submit" value="Continue to Step 4" disabled></div>';
				}
			}
			else if($_GET['step'] == 4){
				if ($_POST['step3'] == 'yes'){
					$Pass = TRUE;
				}
				else {
					$Pass = FALSE;
				}
				
				if($Pass == FALSE){
					echo '<div align="center"><h4>Please go back to <a href="/staff/applications/step-1">Step One</a> and start over, you cannot skip anything.</h4></div>';
				}
				else {
					echo '<div align="center">Welcome to Step 4 in the application process, below we ask that you input <b>all</b> the information you believe that will help you in optaining your desired position.  Please remember quality, over quantity</div><br>';
					echo '<script>
						//change two names below to your form\'s names
						document.forms.step4form.step4check.checked=false
						</script>';
					echo '<form method="post" action="/staff/applications/step-5" name="step4form" onSubmit="return defaultagree(this)">';
					echo '<div align="center">
						<textarea cols="80" rows="10" name="userData" id="userData"></textarea>
						</div>';
					echo '<div align="center"><input name="step4" type="hidden" value="yes" /><br />
					<input name="positionticked" type="hidden" value="'.$_POST['positionticked'].'" />
					<input id="step4agree" name="step4agree" type="checkbox" value="step4agree" onClick="agreesubmit(this)" />&nbsp; <label for="step4agree" style="display:inline;color:#555;">I am finished and wish to move on.</label><br /><br />
					<input type="Submit" value="Continue to Step 5" disabled></div>';
				}
			}
			else if($_GET['step'] == 5){
				if ($_POST['step4'] == 'yes'){
					$Pass = TRUE;
				}
				else {
					$Pass = FALSE;
				}
				
				if($Pass == FALSE){
					echo '<div align="center"><h4>Please go back to <a href="/staff/applications/step-1">Step One</a> and start over, you cannot skip anything.</h4></div>';
				}
				else {
					$reqInformation = $_POST['userData'];
					$reqInformation = htmlspecialchars($reqInformation);
					$Ticked = $_POST['positionticked'];	
					$query = mysql_query("INSERT INTO applications_submissions (positionID, username, reqInformation, appRound) VALUES ('".mysql_real_escape_string($Ticked)."', '".mysql_real_escape_string($this->profileArray[5])."', '".mysql_real_escape_string($reqInformation)."', '".mysql_real_escape_string($this->application_round)."')") or die('Error: ' . mysql_error());
						
					
					echo '<div align="center" style="padding:10px;font-size:14px;">Welcome to Step 5 in the application process.<br />Below is an option for you personally.  We have 2 options for forms of working.<br /><br />
					1) is working for AnimeFTW as staff, nothing special.<br />
					2) is where you can work as a volunteer staff member for FTW Entertainment LLC, based on your age, you will be asked to submit a form to the company so you can be considered official volunteer staff.  It will provide benefits as well as modern day work time (you can say to an employer that you worked for the company, and they can call us and get out take on you.), along with oodles of software and support bonuses, you will be backed by a fully registered company.<br /><br />Now the choice is yours, what would you like to choose?<br /><b>NOTE: both will work on animeftw.tv!</b></div><br /><br />';
					echo '<div align="center"><table width="100%">
						<tr>
							<td width="40%">
							<form method="post" action="/staff/applications/step-6">
							<input name="step5" type="hidden" value="yes" />
							<input name="company" type="hidden" value="animeftw" />
							<input type="Submit" value="&lt;&lt; AnimeFTW for Me!">
							</form>
							</td>
							<td width="20%">&nbsp;</td>
							<td width="40%">
							<form method="post" action="/staff/applications/step-6">
							<input name="step5" type="hidden" value="yes" />
							<input name="company" type="hidden" value="ftwentertainment" />
							<input type="Submit" value="FTW Entertainment for Me! &gt;&gt;">
							</form>
							</td>
						</tr>
						</table></div>';
				}
			}
			else if($_GET['step'] == 6){
				if ($_POST['step5'] == 'yes'){
					$Pass = TRUE;
				}
				else {
					$Pass = FALSE;
				}
				
				if($Pass == FALSE){
					echo '<div align="center"><h4>Please go back to <a href="/staff/applications/step-1">Step One</a> and start over, you cannot skip anything.</h4></div>';
				}
				else {
					$company = $_POST['company'];
					$query = 'UPDATE applications_submissions SET company=\'' . mysql_real_escape_string($company) . '\' WHERE appRound = \'' . $this->application_round . '\' AND username=\'' . $this->profileArray[5] . '\'';
					mysql_query($query) or die('Error : ' . mysql_error());
					if ($company == 'animeftw'){
						echo '<div align="center">Thank You, you chose to apply for AnimeFTW for Your Staff application, it will be reviewed and handled as soon as possible, please expect a responce back by email AND site PM.</div>';
					}
					else if ($company == 'ftwentertainment'){
		
						echo '<div>Thank You, you chose to apply for FTW Entertainment LLC staff, Your Staff application will be reviewed by the admins then proccessed accordingly, we ask that you make your way over to our corperate site found <a href="http://ftwentertainment.com">here</a> and register on our forums.<br />Doing so allows us another place to contact you when you have passed and are in need of further information.<br /><br /> Before you leave, please enter your birthday if it does not show up:<br /><br />';
						echo '	Please Enter your Age for our processing, this will ensure that everything goes smoothly on our end.<br /><br />Birthday:';
						$query  = "SELECT ageMonth, ageDate, ageYear FROM users WHERE ID='".$this->profileArray[1]."'";
						$result = mysql_query($query) or die('Error : ' . mysql_error());
						list($ageMonth, $ageDate, $ageYear) = mysql_fetch_array($result, MYSQL_NUM);
						echo '<form method="post" action="/staff/applications/step-finish">
						<br />
						<select name="ageDate">
							<option value="00"'; if($ageDate == '00'){echo' selected';} echo '>--Day--</option>
							<option value="01"'; if($ageDate == '01'){echo' selected';} echo '>1</option>
							<option value="02"'; if($ageDate == '02'){echo' selected';} echo '>2</option>
							<option value="03"'; if($ageDate == '03'){echo' selected';} echo '>3</option>
							<option value="04"'; if($ageDate == '04'){echo' selected';} echo '>4</option>
							<option value="05"'; if($ageDate == '05'){echo' selected';} echo '>5</option>
							<option value="06"'; if($ageDate == '06'){echo' selected';} echo '>6</option>
							<option value="07"'; if($ageDate == '07'){echo' selected';} echo '>7</option>
							<option value="08"'; if($ageDate == '08'){echo' selected';} echo '>8</option>
							<option value="09"'; if($ageDate == '09'){echo' selected';} echo '>9</option>
							<option value="10"'; if($ageDate == '10'){echo' selected';} echo '>10</option>
							<option value="11"'; if($ageDate == '11'){echo' selected';} echo '>11</option>
							<option value="12"'; if($ageDate == '12'){echo' selected';} echo '>12</option>
							<option value="13"'; if($ageDate == '13'){echo' selected';} echo '>13</option>
							<option value="14"'; if($ageDate == '14'){echo' selected';} echo '>14</option>
							<option value="15"'; if($ageDate == '15'){echo' selected';} echo '>15</option>
							<option value="16"'; if($ageDate == '16'){echo' selected';} echo '>16</option>
							<option value="17"'; if($ageDate == '17'){echo' selected';} echo '>17</option>
							<option value="18"'; if($ageDate == '18'){echo' selected';} echo '>18</option>
							<option value="19"'; if($ageDate == '19'){echo' selected';} echo '>19</option>
							<option value="20"'; if($ageDate == '20'){echo' selected';} echo '>20</option>
							<option value="21"'; if($ageDate == '21'){echo' selected';} echo '>21</option>
							<option value="22"'; if($ageDate == '22'){echo' selected';} echo '>22</option>
							<option value="23"'; if($ageDate == '23'){echo' selected';} echo '>23</option>
							<option value="24"'; if($ageDate == '24'){echo' selected';} echo '>24</option>
							<option value="25"'; if($ageDate == '25'){echo' selected';} echo '>25</option>
							<option value="26"'; if($ageDate == '26'){echo' selected';} echo '>26</option>
							<option value="27"'; if($ageDate == '27'){echo' selected';} echo '>27</option>
							<option value="28"'; if($ageDate == '28'){echo' selected';} echo '>28</option>
							<option value="29"'; if($ageDate == '29'){echo' selected';} echo '>29</option>
							<option value="30"'; if($ageDate == '30'){echo' selected';} echo '>30</option>
							<option value="31"'; if($ageDate == '31'){echo' selected';} echo '>31</option>							
						</select>
						<select name="ageMonth">
							<option value="00"'; if($ageMonth == '00'){echo' selected ';} echo '>--Month--</option>
							<option value="01"'; if($ageMonth == '01'){echo' selected ';} echo '>January</option>
							<option value="02"'; if($ageMonth == '02'){echo' selected ';} echo '>February</option>
							<option value="03"'; if($ageMonth == '03'){echo' selected ';} echo '>March</option>
							<option value="04"'; if($ageMonth == '04'){echo' selected ';} echo '>April</option>
							<option value="05"'; if($ageMonth == '05'){echo' selected ';} echo '>May</option>
							<option value="06"'; if($ageMonth == '06'){echo' selected ';} echo '>June</option>
							<option value="07"'; if($ageMonth == '07'){echo' selected ';} echo '>July</option>
							<option value="08"'; if($ageMonth == '08'){echo' selected ';} echo '>August</option>
							<option value="09"'; if($ageMonth == '09'){echo' selected ';} echo '>September</option>
							<option value="10"'; if($ageMonth == '10'){echo' selected ';} echo '>October</option>
							<option value="11"'; if($ageMonth == '11'){echo' selected ';} echo '>November</option>
							<option value="12"'; if($ageMonth == '12'){echo' selected ';} echo '>December</option>							 
						</select>
						<select name="ageYear">
							<option value="0000"'; if($ageYear == '0000'){echo' selected ';} echo '>--Year--</option>
							<option value="2005"'; if($ageYear == '2000'){echo' selected ';} echo '>2005</option>
							<option value="2004"'; if($ageYear == '2000'){echo' selected ';} echo '>2004</option>
							<option value="2003"'; if($ageYear == '2000'){echo' selected ';} echo '>2003</option>
							<option value="2002"'; if($ageYear == '2000'){echo' selected ';} echo '>2002</option>
							<option value="2001"'; if($ageYear == '2000'){echo' selected ';} echo '>2001</option>
							<option value="2000"'; if($ageYear == '2000'){echo' selected ';} echo '>2000</option>
							<option value="1999"'; if($ageYear == '1999'){echo' selected ';} echo '>1999</option>
							<option value="1998"'; if($ageYear == '1998'){echo' selected ';} echo '>1998</option>
							<option value="1997"'; if($ageYear == '1997'){echo' selected ';} echo '>1997</option>
							<option value="1996"'; if($ageYear == '1996'){echo' selected ';} echo '>1996</option>
							<option value="1995"'; if($ageYear == '1995'){echo' selected ';} echo '>1995</option>
							<option value="1994"'; if($ageYear == '1994'){echo' selected ';} echo '>1994</option>
							<option value="1993"'; if($ageYear == '1993'){echo' selected ';} echo '>1993</option>
							<option value="1992"'; if($ageYear == '1992'){echo' selected ';} echo '>1992</option>
							<option value="1991"'; if($ageYear == '1991'){echo' selected ';} echo '>1991</option>
							<option value="1990"'; if($ageYear == '1990'){echo' selected ';} echo '>1990</option>
							<option value="1989"'; if($ageYear == '1989'){echo' selected ';} echo '>1989</option>
							<option value="1988"'; if($ageYear == '1988'){echo' selected ';} echo '>1988</option>
							<option value="1987"'; if($ageYear == '1987'){echo' selected ';} echo '>1987</option>
							<option value="1986"'; if($ageYear == '1986'){echo' selected ';} echo '>1986</option>
							<option value="1985"'; if($ageYear == '1985'){echo' selected ';} echo '>1985</option>
							<option value="1984"'; if($ageYear == '1984'){echo' selected ';} echo '>1984</option>
							<option value="1983"'; if($ageYear == '1983'){echo' selected ';} echo '>1983</option>
							<option value="1982"'; if($ageYear == '1982'){echo' selected ';} echo '>1982</option>
							<option value="1981"'; if($ageYear == '1981'){echo' selected ';} echo '>1981</option>
							<option value="1980"'; if($ageYear == '1980'){echo' selected ';} echo '>1980</option>
							<option value="1979"'; if($ageYear == '1979'){echo' selected ';} echo '>1979</option>
							<option value="1978"'; if($ageYear == '1978'){echo' selected ';} echo '>1978</option>
							<option value="1977"'; if($ageYear == '1977'){echo' selected ';} echo '>1977</option>
							<option value="1976"'; if($ageYear == '1976'){echo' selected ';} echo '>1976</option>
							<option value="1975"'; if($ageYear == '1975'){echo' selected ';} echo '>1975</option>
							<option value="1974"'; if($ageYear == '1974'){echo' selected ';} echo '>1974</option>
							<option value="1973"'; if($ageYear == '1973'){echo' selected ';} echo '>1973</option>
							<option value="1972"'; if($ageYear == '1972'){echo' selected ';} echo '>1972</option>
							<option value="1971"'; if($ageYear == '1971'){echo' selected ';} echo '>1971</option>
							<option value="1970"'; if($ageYear == '1970'){echo' selected ';} echo '>1970</option>
							<option value="1969"'; if($ageYear == '1969'){echo' selected ';} echo '>1969</option>
							<option value="1968"'; if($ageYear == '1968'){echo' selected ';} echo '>1968</option>
							<option value="1967"'; if($ageYear == '1967'){echo' selected ';} echo '>1967</option>
							<option value="1966"'; if($ageYear == '1966'){echo' selected ';} echo '>1966</option>
							<option value="1965"'; if($ageYear == '1965'){echo' selected ';} echo '>1965</option>
							<option value="1964"'; if($ageYear == '1964'){echo' selected ';} echo '>1964</option>
							<option value="1963"'; if($ageYear == '1963'){echo' selected ';} echo '>1963</option>
							<option value="1962"'; if($ageYear == '1962'){echo' selected ';} echo '>1962</option>
							<option value="1961"'; if($ageYear == '1961'){echo' selected ';} echo '>1961</option>
							<option value="1960"'; if($ageYear == '1960'){echo' selected ';} echo '>1960</option>
						</select><br /><br />
						<input name="step6" type="hidden" value="yes" />
						<input type="Submit" value="Finish Your Application">';
					}
					else {
						echo '<h4>WARNING: ERROR S6-1, There was an error, please go back and try again.</h4>~<a href="/staff/applications/step-1">Back to Application Start</a>';
					}
				}
			}
			else if($_GET['step'] == 'finish'){
				if ($_POST['step6'] == 'yes'){
					$Pass = TRUE;
				}
				else {
					$Pass = FALSE;
				}
				
				if($Pass == FALSE){
					echo '<div align="center"><h4>Please go back to <a href="/staff/applications/step-1">Step One</a> and start over, you cannot skip anything.</h4></div>';
				}
				else {
					$ageDate1 = $_POST['ageDate'];
					$ageMonth1 = $_POST['ageMonth'];
					$ageYear1 = $_POST['ageYear'];
					$finalDate = $ageMonth1.'/'.$ageDate1.'/'.$ageYear1;
					$query = 'UPDATE applications_submissions SET Age=\'' . mysql_real_escape_string($finalDate) . '\' WHERE appRound = \'' . $this->application_round . '\' AND username=\'' . $this->profileArray[5] . '\'';
					mysql_query($query) or die('Error : ' . mysql_error());
					echo '<div align="center">Thank you for your application it is now finished, feel free to move to <a href="http://animeftw.tv">the main site</a> and watch some more anime as your application is processed and evaluated.</div>';
				}
			}
			else {
				echo 'Whatever you just tried, don\'t do it again.<br />';
				echo print_r($_POST);
			}
		}
		echo "</div>";
	}
	
	private function BuildSecurityTest(){
		if(isset($_POST['submit'])){
			$query = "INSERT INTO applications_sectests (uid, date, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17) VALUES ('".mysql_real_escape_string($this->profileArray[1])."', '".mysql_real_escape_string(time())."', '".mysql_real_escape_string($_POST['q1'])."', '".mysql_real_escape_string($_POST['q2'])."', '".mysql_real_escape_string($_POST['q3'])."', '".mysql_real_escape_string($_POST['q4'])."', '".mysql_real_escape_string($_POST['q5'])."', '".mysql_real_escape_string($_POST['q6'])."', '".mysql_real_escape_string($_POST['q7'])."', '".mysql_real_escape_string($_POST['q8'])."', '".mysql_real_escape_string($_POST['q9'])."', '".mysql_real_escape_string($_POST['q10'])."', '".mysql_real_escape_string($_POST['q11'])."', '".mysql_real_escape_string($_POST['q12'])."', '".mysql_real_escape_string($_POST['q13'])."', '".mysql_real_escape_string($_POST['q14'])."', '".mysql_real_escape_string($_POST['q15'])."', '".mysql_real_escape_string($_POST['q16'])."', '".mysql_real_escape_string($_POST['q17'])."')";
			mysql_query($query) or die('Could not connect, way to go retard:' . mysql_error());
			//echo $query;
			$done = TRUE;
			$msg = '<div><h2>Application submitted successfully, please contact your manager letting them know it was completed.</h2></div><br />';
		}
		$query = mysql_query("SELECT COUNT(id) FROM applications_sectests WHERE uid = ".$this->profileArray[1]); 
		$total = mysql_result($query, 0);
		echo "<div class='side-body-bg'>\n";
		echo "<span class='scapmain'>AnimeFTW.tv Staff Applications - Security Test</span>\n";
		echo "</div>\n";
		echo "<div class='side-body'>\n";
		echo "<div align=\"center\">";
		echo "Congratulations on making it this far, the following is a security test that we will evaluate to make sure that with specific situations you will handle yourself with the most professionalizm possible. We will also verify that by becoming staff your habits will not make the site vulnerable to attacks.<br /><br /><b>Application Notes:</b> Please be aware, these are just questions to acertain your abilities, please answer to the best of your ability.";
		echo "</div><br /><br />";
		if(isset($done) && $done == TRUE){
			echo $msg;
		}
		else {
			if($total < 1){
				echo '<script type="text/javascript">
				function checkForm(f)
				{
					if (f.elements[\'q1\'].value == "" || f.elements[\'q2\'].value == "" || f.elements[\'q3\'].value == "" || f.elements[\'q4\'].value == "" || f.elements[\'q5\'].value == "" || f.elements[\'q6\'].value == "" || f.elements[\'q7\'].value == "" || f.elements[\'q8\'].value == "" || f.elements[\'q9\'].value == "" || f.elements[\'q10\'].value == "" || f.elements[\'q11\'].value == "" || f.elements[\'q12\'].value == "" || f.elements[\'q13\'].value == "" || f.elements[\'q14\'].value == "" || f.elements[\'q15\'].value == "" || f.elements[\'q16\'].value == "" || f.elements[\'q17\'].value == "")
					{
						alert("Please make sure you have filled out all of the form values.");
						return false;
					}
					else
					{
						f.submit();
						return false;
					}
				}
				</script>';
				echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="submit" onSubmit="return checkForm(this); return false;">';
				echo '<div>Question 1: <div align="center"><b>Can you Share a PC with Friends or Family or log into the Site/FTP Site from School or the Library?</b></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q1" name="q1" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 2: <div align="center"><b>What must you do if you have family that also uses the same PC and especially if they also use AnimeFTW.tv?</b></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q2" name="q2" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 3: <div align="center"><b>Why is sharing a PC or using a community/public pc bad?</b></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q3" name="q3" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 4: <div align="center"><b>What virus scanner is running on your PC?</b> <br /><i>(Please provide a link to (a) screenshot(s))</i></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q4" name="q4" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 5: <div align="center"><b>Are your virus definitions up to date?</b><br /><i>(We need a screenshot that shows the date of the last update)</i></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q5" name="q5" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 6: <div align="center"><b>How often do you do a virus scan of your PC?</b> <br /><i>(Staff members are now required to scan twice a week or more so if you don\'t do it, set it now before replying to this question.)</i></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q6" name="q6" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 7: <div align="center"><b>What spyware scanner do you use on a regular basis on your PC?</b><i>Please provide a current screenshot</i></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q7" name="q7" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 8: <div align="center"><b>What are you required to do if you get a virus or keylogger on your PC?</b></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q8" name="q8" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 9: <div align="center"><b>What are the consequences of getting infected with a virus or keylogger more than once?</b></div></div>';
				echo '<div>Answer:<div align="center"><textarea id="q9" name="q9" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 10: <div align="center"><b>What are you required to do if another staff member tells you they may be infected or they send you a virus via MSN/E-mail or some other means?</b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q10" name="q10" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 11: <div align="center"><b>Do you understand the importance of PC security and the purpose of this portion of the test?</b><br /><i>(Please do not answer with a Yes/No answer.)</i></div>';
				echo '<div>Answer:<div align="center"><textarea id="q11" name="q11" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 12: <div align="center"><b>Are "I was Hacked" Or "My Brother/Sister did it" accepted excuses for any issues concerning your account or its use/misuse?</b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q12" name="q12" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 13: <div align="center"><b>Who is responsible for security of your PC, your FTW account and for the security of AnimeFTW.tv?</b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q13" name="q13" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 14: <div align="center"><b>What is a good secure password length, how many characters?</b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q14" name="q14" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 15: <div align="center"><b>What should a good secure password contain? </b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q15" name="q15" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 16: <div align="center"><b>How often should you change your password? </b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q16" name="q16" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div>Question 17: <div align="center"><b>Who are you allowed to give your password to?</b></div>';
				echo '<div>Answer:<div align="center"><textarea id="q17" name="q17" style="width:650px;height:60px;"></textarea></div><br /></div>';
				echo '<div align="center"><input id="submit" name="submit" type="submit" value="Submit Security Test" /></div>
							
					</form>';
							
			}
			else {
				echo "<div><h2>You have already submitted a security test......</h2></div><br />";
			}
		}
		echo "</div>";
	}
	
	private function ShowStaff(){
		echo "<div class='side-body-bg'>\n";
				echo "<span class='scapmain'>AnimeFTW.tv Staff</span>\n";
				echo "</div>\n";
				echo "<div class='side-body'>\n";
				echo "<div align=\"center\">";
				echo "Over the years, AnimeFTW.tv has had the privelage to work with many talented individuals. They never got paid for their work, but at every turn there was innovation while treading into the unknown. Below is the current list of our Staff, we are proud to call them our own.<br /><i>For without them, there would be no AnimeFTW.tv. Period.</i>";
				echo "</div><br />";
				echo '<div align="center">';
				echo '<h2>Site Owners/Administrators</h2>';
						$query  = "SELECT ID, personalMsg, avatarExtension FROM users WHERE Level_access='1' ORDER BY Username";
						$result = mysql_query($query) or die('Error : ' . mysql_error());
						$a = 0; // variable for each user
						$b = 5; // base multiple of 5 per row
						$c = 1; // multiples times b so when A == B*C it makes another row
						echo '<table cellpadding="10">';
						while(list($ID,$personalMsg,$avatarExtension) = mysql_fetch_array($result))
						{
							if($avatarExtension == ''){ $avatar = '<img src="' . $this->Host . '/avatars/default.gif" alt="" border="0" width="100px" />';}
							else {$avatar = '<img src="' . $this->Host . '/avatars/user'.$ID.'.'.$avatarExtension.'" alt="" border="0" width="100px" />';}
							$user = '<td align="center" valign="top"><div style="padding-bottom:10px">'.$personalMsg.'</div>'.$avatar.'<div style="padding-top:5px;">'.$this->formatUsername($ID).'</div></td>';
							if($a == ($b*$c))
							{
								echo '</tr>'."\n";
								echo '<tr>'."\n";
								echo $user."\n";
								$c++;
							}
							else {
								echo $user."\n";
							}
							$a++;
						}
						echo '
						</table>
						 <br />
						 <h2>Site Managers</h2>
						';
						$query  = "SELECT ID, personalMsg, avatarExtension FROM users WHERE Level_access='2' ORDER BY Username";
						$result = mysql_query($query) or die('Error : ' . mysql_error());
						$e = 0; // variable for each user
						$f = 5; // base multiple of 5 per row
						$g = 1; // multiples times b so when A == B*C it makes another row
						echo '<table cellpadding="10">';
						while(list($ID,$personalMsg,$avatarExtension) = mysql_fetch_array($result))
						{
							if($avatarExtension == ''){ $avatar = '<img src="' . $this->Host . '/avatars/default.gif" alt="" border="0" width="100px" />';}
							else {$avatar = '<img src="' . $this->Host . '/avatars/user'.$ID.'.'.$avatarExtension.'" alt="" border="0" width="100px" />';}
							$user = '<td align="center" valign="top"><div style="padding-bottom:10px">'.$personalMsg.'</div>'.$avatar.'<div style="padding-top:5px;">'.$this->formatUsername($ID).'</div></td>';
							if($e == ($f*$g))
							{
								echo '</tr>'."\n";
								echo '<tr>'."\n";
								echo $user."\n";
								$c++;
							}
							else {
								echo $user."\n";
							}
							$e++;
						}
						echo '
						</table>
						 <br />
						 <h2>Site Staff</h2>
						';
						$query  = "SELECT ID, personalMsg, avatarExtension FROM users WHERE Level_access='4' OR Level_access='5' OR Level_access='6' ORDER BY Username";
						$result = mysql_query($query) or die('Error : ' . mysql_error());
						$a = 0; // variable for each user
						$b = 5; // base multiple of 5 per row
						$c = 1; // multiples times b so when A == B*C it makes another row
						echo '<table cellpadding="10">';
						while(list($ID,$personalMsg,$avatarExtension) = mysql_fetch_array($result))
						{
							if($avatarExtension == ''){ $avatar = '<img src="' . $this->Host . '/avatars/default.gif" alt="" border="0" width="100px" />';}
							else {$avatar = '<img src="' . $this->Host . '/avatars/user'.$ID.'.'.$avatarExtension.'" alt="" border="0" width="100px" />';}
							$user = '<td align="center" valign="top"><div style="padding-bottom:10px">'.$personalMsg.'</div>'.$avatar.'<div style="padding-top:5px;">'.$this->formatUsername($ID).'</div></td>';
							if($a == ($b*$c))
							{
								echo '</tr>'."\n";
								echo '<tr>'."\n";
								echo $user."\n";
								$c++;
							}
							else {
								echo $user."\n";
							}
							$a++;
						}
						echo '
						</table>
						</div>';
	}
}