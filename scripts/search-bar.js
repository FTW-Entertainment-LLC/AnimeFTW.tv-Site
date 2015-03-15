// SearchBar Javascript
$(document).ready(function() {
			
				$("#q").focus(function() {
					if($("#q").val() == "AnimeFTW.tv Site Search") {
						$("#q").val("");
					}
					$("#q").css("color", "#2C71AE");
				});
				
				$("#q").blur(function() {
					if($("#q").val() == "") {
						$("#q").val("AnimeFTW.tv Site Search");
						$("#q").css("color", "#999999");
					}
					$("#SearchResults").slideUp();
				});
				
				$("#q").keydown(function(e) {
					if(e.which == 8) {
						SearchText = $("#q").val().substring(0, $("#q").val().length-1);
					}
					else {
						SearchText = $("#q").val() + String.fromCharCode(e.which);
					}
					$("#SearchResults").load("/ajax.php", { q: SearchText });
					$("#SearchResults").slideDown();
				});
			
			});