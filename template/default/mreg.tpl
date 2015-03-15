<HTML>
 <HEAD>
  <TITLE>Mobile Registration | AnimeFTW.tv</TITLE>
  <link rel="stylesheet" href="/css/mobile.css" type="text/css" />
  <SCRIPT src="/mobile.js" language="javascript" type="text/javascript"></SCRIPT>
  <meta name="viewport" content="width=device-width,user-scalable=no" />
 </HEAD>
 <BODY>
  <A name="top"></a>
  <DIV class="header">
   <IMG src="/images/new-logo.png">
  </DIV>
  <DIV class="message" id="message">
   <b>Oops!</b> <SPAN id="mes">You shouldn't see this message!</SPAN>
  </DIV>
  <div align="center">If you are having issues with this form, <br />please use the Web version found at www.animeftw.tv/m/register
  </div>
  <DIV class="content">
   <DIV class="chead">
    AnimeFTW.tv Registration
   </DIV>
   <DIV class="form">
    <INPUT id="u" class="input" placeholder="Choose a username...">
    <INPUT id="p" class="input" type="password" placeholder="Choose a password...">
    <INPUT id="p2" class="input" type="password" placeholder="Enter your password again...">
    <INPUT id="e" class="input" type="email" placeholder="Enter your email...">
    <INPUT id="e2" class="input" type="email" placeholder="Enter your email again...">
   </DIV>
  </DIV>
  <DIV class="content">
   <DIV class="chead">
    Optional Fields
   </DIV>
   <DIV class="form">
    <INPUT id="n" class="input" placeholder="Enter your first name...">
	<SELECT id="g" class="input" placeholder="Select your gender...">
	 <OPTION value="0">Select your gender...</OPTION>
	 <OPTION value="1">Male</OPTION>
	 <OPTION value="2">Female</OPTION>
	 <OPTION value="3">Bending Unit</OPTION>
	</SELECT>
	<INPUT id="b" class="input" placeholder="Enter your birthday (MM/DD/YYYY)....">
	<SELECT id="t" class="input" placeholder="Select your timezone...">
	 <OPTION value="-12.0">(GMT -12:00) Eniwetok, Kwajalein</OPTION>
	 <OPTION value="-11.0">(GMT -11:00) Midway Island, Samoa</OPTION>
	 <OPTION value="-10.0">(GMT -10:00) Hawaii</OPTION>
	 <OPTION value="-9.0">(GMT -9:00) Alaska</OPTION>
	 <OPTION value="-8.0">(GMT -8:00) Pacific Time (US &amp; Canada)</OPTION>
	 <OPTION value="-7.0">(GMT -7:00) Mountain Time (US &amp; Canada)</OPTION>
	 <OPTION value="-6.0" selected>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</OPTION>
	 <OPTION value="-5.0">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</OPTION>
	 <OPTION value="-4.0">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</OPTION>
	 <OPTION value="-3.5">(GMT -3:30) Newfoundland</OPTION>
	 <OPTION value="-3.0">(GMT -3:00) Brazil, Buenos Aires, Georgetown</OPTION>
	 <OPTION value="-2.0">(GMT -2:00) Mid-Atlantic</OPTION>
	 <OPTION value="-1.0">(GMT -1:00 hour) Azores, Cape Verde Islands</OPTION>
	 <OPTION value="0.0">(GMT) Western Europe Time, London, Lisbon, Casablanca</OPTION>
	 <OPTION value="1.0">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</OPTION>
	 <OPTION value="2.0">(GMT +2:00) Kaliningrad, South Africa</OPTION>
	 <OPTION value="3.0">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</OPTION>
	 <OPTION value="3.5">(GMT +3:30) Tehran</OPTION>
	 <OPTION value="4.0">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</OPTION>
	 <OPTION value="4.5">(GMT +4:30) Kabul</OPTION>
	 <OPTION value="5.0">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</OPTION>
	 <OPTION value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</OPTION>
	 <OPTION value="5.75">(GMT +5:45) Kathmandu</OPTION>
	 <OPTION value="6.0">(GMT +6:00) Almaty, Dhaka, Colombo</OPTION>
	 <OPTION value="7.0">(GMT +7:00) Bangkok, Hanoi, Jakarta</OPTION>
	 <OPTION value="8.0">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</OPTION>
	 <OPTION value="9.0">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</OPTION>
	 <OPTION value="9.5">(GMT +9:30) Adelaide, Darwin</OPTION>
	 <OPTION value="10.0">(GMT +10:00) Eastern Australia, Guam, Vladivostok</OPTION>
	 <OPTION value="11.0">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</OPTION>
	 <OPTION value="12.0">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</OPTION>
	</SELECT>
	<SELECT id="a" class="input" placeholder="Receive administrator notifications?">
	 <OPTION value="0">Recieve administrator notifications?</OPTION>
	 <OPTION value="1">Yes</OPTION>
	 <OPTION value="2">No</OPTION>
	</SELECT>
	<SELECT id="s" class="input" placeholder="Receive private message notifications?">
	 <OPTION value="0">Recieve private message notifications?</OPTION>
	 <OPTION value="1">Yes</OPTION>
	 <OPTION value="2">No</OPTION>
	</SELECT>
   </DIV>
  </DIV>
  <DIV class="content">
   <DIV class="chead">
    Humanity Verification
   </DIV>
   <DIV class="form">
	<SELECT id="vh" class="input" placeholder="Are you human?">
	 <OPTION value="0">Are you human?</OPTION>
	 <OPTION value="1">No</OPTION>
	 <OPTION value="2">Yes</OPTION>
	</SELECT>
	<DIV class="question">How many fingers are on your right hand? Spell out the number.</DIV>
	<INPUT id="q" class="input" placeholder="Enter your answer here...">
   </DIV>
  </DIV>
  <DIV class="content bottom">
   <DIV class="chead">
    Submit Your Registration
   </DIV>
   <DIV class="form">
	<INPUT type="button" class="input submit" value="Complete Registration" onClick="submitRegister()">
   </DIV>
  </DIV>
 </BODY>
</HTML>