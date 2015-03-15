<DIV class="header">Using LogDog</DIV>
<DIV class="information">
 LogDog uses a special set of keywords to develop a search result set. The order of the query
 generally doesn't matter, just as long as the prefixed keyword is attached.
 <DIV class="example">User Zigbigidorlu Developer 18</div>
 Words that are not attached in some way to an operator keyword are automatically discarded,
 meaning that it's also possible to query in a more general way. For example:
 <DIV class="example">When did user Zigbigidorlu use Developer 18?</div>
 All punctuation is automatically discarded, along with any filler words, leaving the phrase
 &quot;User Zigbigidorlu Developer 18&quot; to be interpreted.
 <br /><br />
 Users that include spacing in their names need to have quotation marks (&quot;) around the
 name, otherwise the interpreter will regard the characters after the space as junk words and
 will send the wrong results. Punctuation associated with usernames, however, will be left alone
 and require no special measures.
</DIV>
<DIV class="header">Magic Methods</DIV>
<DIV class="information">
 LogDog has two magic methods: User and Developer. Any alphanumeric query that doesn't match with
 any keywords will be interpreted as a User magic method, meaning a result set containing information
 associated with a username specified.
 <DIV class="example">Zigbigidorlu</DIV>
 Any numeric input that doesn't match with any keywords will
 be considered a Developer magic method, returning the result set associated with that developer.
</DIV>
<DIV class="header">OR Queries</DIV>
<DIV class="information">
 You may include any of the keywords as many times as you'd like during a query -- they will be
 treated as an OR query set, so you will get results from all of the requests.
 <DIV class="example">User Zigbigidorlu user Tomska user Robotman321 Agent Firefox</div>
 The above example will pull data from users Zigbigidorlu, Tomska, and Robotman321 when they used Firefox.
 There are two keywords that do <i>not</i> apply to OR queries: From and Till. If either of these keywords
 are used multiple times, only the first instance will be used, and the others discarded.
</DIV>
<DIV class="header">Keywords</DIV>
	<DIV class="subheader">User</DIV>
	<DIV class="usage">Usage: User Zigbigidorlu</DIV>
	<DIV class="definition">
		The &quot;User&quot; keyword, followed by a username will filter results for that user only.
	</DIV>
	<DIV class="subheader">Developer</DIV>
	<DIV class="usage">Usage: Developer 12</DIV>
	<DIV class="definition">
		Filters results based on the ID of the developer, often specific to application.
	</DIV>
	<DIV class="subheader">Agent</DIV>
	<DIV class="usage">Usage: Agent Firefox</DIV>
	<DIV class="definition">
		Filters results based on the agent of the browser requesting data. Can be as simple
		as a one word phrase, such as &quot;Firefox&quot;, or as complicated as a full browser
		string, such as &quot;Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1
		(KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1&quot;
	</DIV>
	<DIV class="subheader">IP</DIV>
	<DIV class="usage">Usage: IP 208.93.159.25</DIV>
	<DIV class="definition">
		Filters results based on the IP of the requesting agent.
	</DIV>
	<DIV class="subheader">Query</DIV>
	<DIV class="usage">Usage: Query search=Fullmetal</DIV>
	<DIV class="definition">
		Filters results based on the url request. You may use part of the entirety of the url --
		however, note that you will need quotes if there are spaces.
	</DIV>
	<DIV class="subheader">From</DIV>
	<DIV class="usage">Usage: From 04-16-1986-04:32:00</DIV>
	<DIV class="definition">
		Filters results based on a specific date, or date and time. Will accept any format
		compatable with the <a href="http://php.net/manual/en/function.strtotime.php">strtotime()</a> function in PHP.
		Pretty much any human-readable format works.<br />
	</DIV>
	<DIV class="subheader">Till</DIV>
	<DIV class="usage">Usage: Till 04-16-1986-04:32:00</DIV>
	<DIV class="definition">
		MUST BE USED WITH &quot;From&quot;. If &quot;From&quot; is not found, &quot;Till&quot;
		will be discarded. For usage, see &quot;From&quot;.
	</DIV>
	<DIV class="footpad"></DIV>
</DIV>