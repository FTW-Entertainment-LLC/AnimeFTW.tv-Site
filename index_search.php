<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Out Of Towner</title>
		<style>
			body {
				font-family: Verdana, Arial, Helvetica, sans-serif;
				background: url('images/background.jpg') #000000 no-repeat top;
			}
			
			#SearchBox {
				background: url('images/search_box.png');
				float: left;
				height: 60px;
				width: 305px;
			}
			
			#SearchButton {
				float: left;
				height: 60px;
				width: 70px;
			}
			
			#SearchInput {
				background: none;
				border: none;
				color: #999999;
				font-size: 16px;
				outline: none;
				margin: 20px;
				width: 280px;
			}
			
			#SearchResults {
				background: #000000;
				display: none;
				overflow: auto;
				position: absolute;
				width: 330px;
				z-index: 99;
			}
			
			#SearchResults a {
				color: #FFFFFF;
				display: block;
				padding: 5px 5px 5px 15px;
				text-decoration: none;
			}
			
			#SearchResults a .highlight {
				color: #0099FF;
			}
			
			#SearchResults a:hover {
				color: #333333;
				background: #CCCCFF;
				text-decoration: underline;
			}
			
			.MenuLink {
				color: #FFFFFF;
				display: block;
				padding: 10px 0px;
				text-align: center;
				width: 160px;
			}
			
			.MenuLink:hover {
				background: #CCCCCC;
			}
				
		</style>
		<script type="text/javascript" src="scripts/jquery-1.3.2.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
			
				$("#SearchInput").focus(function() {
					if($("#SearchInput").val() == "City Search") {
						$("#SearchInput").val("");
					}
					$("#SearchInput").css("color", "#000000");
				});
				
				$("#SearchInput").blur(function() {
					if($("#SearchInput").val() == "") {
						$("#SearchInput").val("City Search");
						$("#SearchInput").css("color", "#999999");
					}
					$("#SearchResults").slideUp();
				});
				
				$("#SearchInput").keydown(function(e) {
					if(e.which == 8) {
						SearchText = $("#SearchInput").val().substring(0, $("#SearchInput").val().length-1);
					}
					else {
						SearchText = $("#SearchInput").val() + String.fromCharCode(e.which);
					}
					$("#SearchResults").load("ajax.php", { SearchInput: SearchText });
					$("#SearchResults").slideDown();
				});
			
			});
		</script>
	</head>
	
<body>

<form action="" method="post">

<table style="margin: auto; width: 975px;">
	<tr>
		<td>
			<img src="images/logo.png" />
		</td>
		<td width="380">
			<div id="SearchBox">
				<input id="SearchInput" name="SearchInput" value="City Search" />
			</div>
			<div id="SearchButton">
				<input type="image" src="images/search_button.png" />
			</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<div style="position: relative; left: 20px; top: -48px;">
				<div id="SearchResults"></div>
			</div>
		</td>
	</tr>
</table>

</form>

<table style="background: #000000; opacity: 0.8; filter: alpha(opacity=80); margin: auto; height: 30px; width: 975px;">
	<tr>
		<td><a class="MenuLink" href="#">Home</a></td>
		<td><a class="MenuLink" href="#">Package Deals</a></td>
		<td><a class="MenuLink" href="#">Flights</a></td>
		<td><a class="MenuLink" href="#">Hotels</a></td>
		<td><a class="MenuLink" href="#">Contact</a></td>
	</tr>
</table>

</body>
</html>