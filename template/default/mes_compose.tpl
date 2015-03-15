<DIV class="mes_compose">
 <DIV class="mes_to">
  <INPUT id="mes_to" placeholder="Recipient" value="%to%">
 </DIV>
 <DIV class="mes_subject">
  <INPUT id="mes_subject" placeholder="Subject" maxlength="64" value="%subj%">
 </DIV>
 <DIV class="mes_message">
  <TEXTAREA id="mes_message" placeholder="Your message">%message%</TEXTAREA>
 </DIV>
 <DIV class="mes_buttons">
  <INPUT type="button" value="Save Draft" onclick="sendmessage(true)"> <INPUT type="button" value="Send Message" onclick="sendmessage()">
 </DIV>
 <INPUT type="hidden" id="mes_draftid">
</DIV>