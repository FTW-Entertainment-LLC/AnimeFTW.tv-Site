<table align='center' cellpadding='0' cellspacing='0' width='%theme_width%'>
 <tr><td width='%theme_width%' class='main-bg'>
 
  <table cellpadding='0' cellspacing='0' width='100%'>
   <tr><td class='note-message' align='center'>
    %index_global_message%
   </td></tr>
  </table>
   <br /><br />
  <table cellpadding='0' cellspacing='0' width='100%'>
   <tr><td valign='top' class='main-mid'>

    <DIV class="messages_bound">
	 <DIV class="header" id="mes_type">%boxname%</DIV>
	 <DIV class="inner">
	  <DIV class="menu" id="mes_menu"><a href="/pm/inbox">Inbox</a> | <a href="/pm/sent">Sent</a> | <a href="/pm/drafts">Drafts</a> | <a href="/pm/compose">Compose</a></DIV>
	 </DIV>
	 <DIV class="inner" id="mes_header">
		 <TABLE>
		  <TR><TD class="mes_subj">
		   <DIV id="mes_subj">%col_subj%</DIV>
		  </TD><TD class="mes_from">
		   <DIV id="mes_fromto">%col_fromto%</DIV>
		  </TD><TD class="mes_time">
		   <DIV id="mes_time">%col_date%</DIV>
		  </TD></TR>
		 </TABLE>
	 </DIV>
	 <DIV class="inner" id="messages">
		%messages%
	 </DIV>
         <DIV class="message_pg">%pagination%</DIV>
	</DIV>
   
   </td><td style='padding-left:10px; width:250px;  vertical-align:top;' class='main-right'>
    <div class='side-body-bg'>
	 <div class='scapmain'>Message from the Dev</div>
	 <div class='side-body floatfix'>
	  Questions? Comments? Flirtatious remarks? Contact <a href="/pm/compose/1">Brad</a>!
	 </div>
	</div>	 
   </td></tr>
  </table>
  </td></tr>
 </table>
 <!-- IE conditional check, cause IE dosn't play fair. -->
 <!--[if IE]><script language="javascript">var isIE = true;</script><![endif]-->
 <!--[if !IE]><!--><script language="javascript">var isIE = false;</script><!--<![endif]-->
 
<script language="javascript" src="/scripts/mesjs.js"></script>