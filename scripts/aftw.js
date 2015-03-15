// AnimeFTW.tv Customized Javascript



function modify_boxes(to_be_checked,total_boxes){

	for ( i=0 ; i < total_boxes ; i++ ){

		if (to_be_checked){  

 			document.forms[0].chkboxarray[i].checked=true;

		}

		else{

			document.forms[0].chkboxarray[i].checked=false;

		}

 	}  

}



//Hide Script for the tag cloud...

$(document).ready(function() {

 // hides the slickbox as soon as the DOM is ready (a little sooner that page load)

  $('#slickbox').hide();

  

 // shows and hides and toggles the slickbox on click  

  $('#tagcloud-toggle').click(function() {

    $('#tagcloud').toggle(400);

    return false;

  });

});



//comments function

$(function() {

	$(".submitcomment").click(function()

	{

		var uid = $("#uid").val();

		var ip = $("#ip").val();

		var epid = $("#epid").val();

		var comment = $("#comment").val();

		var spoiler = $("#spoiler").val();

		var dataString = 'uid='+ uid + '&ip=' + ip + '&epid=' + epid + '&comment=' + comment + '&spoiler=' + spoiler;

		if(comment=='')

		{

			alert('Please Give Valid Details');

		}

		else

		{

			$("#flash").show();

			$("#flash").fadeIn(400).html('<img src="//static.ftw-cdn.com/site-images/loading-mini.gif" />Loading Comment...');

				$.ajax({

					type: "POST",

					url: "//www.animeftw.tv/includes/comment-process.php",

					data: dataString,

					cache: false,

					success: function(html){

					$("ol#update").prepend(html);

					$("ol#update li:last").fadeIn("slow");

					document.getElementById('comment').value='';

					$("#flash").focus();

				  	$("#flash").hide();

				}

			});

		}

		return false;

	}); 

});



//comments function

$(function() {

	$(".submitpc").click(function()

	{

		var uid = $("#uid").val();

		var ip = $("#ip").val();

		var pid = $("#pid").val();

		var comment = $("#comment").val();

		var dataString = 'uid='+ uid + '&ip=' + ip + '&pid=' + pid + '&comment=' + comment + '&t=1';

		if(comment=='')

		{

			alert('Please Give Valid Details');

		}

		else

		{

			$("#flash").show();

			$("#flash").fadeIn(400).html('<img src="//static.ftw-cdn.com/site-images/loading-mini.gif" />Loading Comment...');

				$.ajax({

					type: "POST",

					url: "//www.animeftw.tv/includes/comment-process.php",

					data: dataString,

					cache: false,

					success: function(html){

					$("#errmsg").hide();

					$("div#dynm").prepend(html);

					$("div#dynm div.justposted").fadeIn("slow");

					document.getElementById('comment').value='';

					$("#flash").focus();

				  	$("#flash").hide();

				}

			});

		}

		return false;

	}); 

});



//function for deleting comments from profiles..

function moddel(c1,c2,c3){

$.ajax({

        type: "GET",

        url: "//www.animeftw.tv/scripts.php",

        data: "view=profile&subview=rmcomments&s=b&id=" + c1 + "&uid=" + c2 + "&sid=" + c3,

        success: function(){

			$("#c" + c1).hide("fast", function () { });

			$("#c-" + c1).show("fast", function () { });

			$("#dico" + c1).hide("fast", function () {});

			$("#uico" + c1).hide("fast", function () {});

    	}

	});

}

function modundel(c1,c2,c3){

$.ajax({

        type: "GET",

        url: "//www.animeftw.tv/scripts.php",

        data: "view=profile&subview=rmcomments&s=a&id=" + c1 + "&uid=" + c2 + "&sid=" + c3,

        success: function(){

			$("#c-" + c1).hide();

			$("#c" + c1).show();

			$("#dico" + c1).show();

			$("#uico" + c1).show();

    	}

	});

}





$(function() {

							$("a[rel]").overlay({mask: '#000', effect: 'apple'});

						});

;(function($, undefined){

	// bgiframe is needed to fix z-index problem for IE6 users.

	$.fn.bgiframe = $.fn.bgiframe ? $.fn.bgiframe : $.fn.bgIframe ? $.fn.bgIframe : function(){

		// For applications that don't have bgiframe plugin installed, create a useless 

		// function that doesn't break the chain

		return this;

	};



 	// Drop Menu Plugin

	$.fn.singleDropMenu = function(options){

		return this.each(function(){

			// Default Settings

			var $obj = $(this), timer, menu,

				settings = $.extend({

					timer: 500,

					parentMO: undefined,

					childMO: undefined,

					show: 'show',

					hide: 'hide'

				}, options||{}, $.metadata ? $obj.metadata() : {});

	

			// Run Menu

			$obj.children('li').bind('mouseover.single-ddm', function(){

				// Clear any open menus

				if (menu && menu.data('single-ddm-i') != $(this).data('single-ddm-i'))

					closemenu();

				else

					menu =false;

				

				// Open nested list

				$(this).children('a').addClass(settings.parentMO).siblings('ul')[settings.show]();

			}).bind('mouseout.single-ddm', function(){

				// Prevent auto close

				menu = $(this);

				timer = setTimeout(closemenu, settings.timer);

			}).each(function(i){

				// Attach indexs to each menu

				$(this).data('single-ddm-i', i);

			}).children('ul').bgiframe();



			// Dropped Menu Highlighting

			$('li > ul > li', $obj).bind('mouseover.single-ddm', function(){

				$('a', this).addClass(settings.childMO);

			}).bind('mouseout.single-ddm', function(){

				$('a', this).removeClass(settings.childMO);

			});

	

			// Closes any open menus when mouse click occurs anywhere else on the page

			$(document).click(closemenu);

	

			// Function to close set menu

			function closemenu(){

				if (menu && timer){

					menu.children('a').removeClass(settings.parentMO).siblings('ul')[settings.hide]();

					clearTimeout(timer);

					menu = false;

				}

			}

		});

	};

})(jQuery);



//forum functions

function link_to_post(pid){

	temp=prompt("Manually copy the direct link to this post below to store the link in your computer's clipboard","http://www.animeftw.tv/forums/find/post-"+pid);

	return false;

}

function toggle_visibility(id) {

	var e = document.getElementById(id);

	if(e.style.display == 'block')

		e.style.display = 'none';

	else

		e.style.display = 'block';

}



//commants show for spoilers

function ShowHideContent(elem,contentId){

    var con = document.getElementById(contentId);

    var isHidden = ( con.style.display == "none" );

    this.innerHTML = (isHidden)?"Hide Spoiler":"Show Spoiler";

    con.style.display = (isHidden)?"block":"none";

    con=null;

}

var Imtech = {};
Imtech.Pager = function() {
    this.paragraphsPerPage = 8;
    this.currentPage = 1;
    this.pagingControlsContainer = '#pagingControls';
    this.pagingContainerPath = '#TrackerEpisodes';

    this.numPages = function() {
        var numPages = 0;
        if (this.paragraphs != null && this.paragraphsPerPage != null) {
            numPages = Math.ceil(this.paragraphs.length / this.paragraphsPerPage);
        }        
        return numPages;
    };

    this.showPage = function(page) {
        this.currentPage = page;
        var html = '';
        this.paragraphs.slice((page-1) * this.paragraphsPerPage,
            ((page-1)*this.paragraphsPerPage) + this.paragraphsPerPage).each(function() {
            html += '<div>' + $(this).html() + '</div>';
        });
        $(this.pagingContainerPath).html(html);
        renderControls(this.pagingControlsContainer, this.currentPage, this.numPages());
    }
    var renderControls = function(container, currentPage, numPages) {
        var pagingControls = 'Page: <ul>';
        for (var i = 1; i <= numPages; i++) {
            if (i != currentPage) {
                pagingControls += '<li><a href="#" onclick="pager.showPage(' + i + '); return false;">' + i + '</a></li>';
            } else {
                pagingControls += '<li>' + i + '</li>';
            }
        }
        pagingControls += '</ul>';
        $(container).html(pagingControls);
    }
}

	var cSpeed=10;
	var cWidth=98;
	var cHeight=20;
	var cTotalFrames=22;
	var cFrameWidth=98;
	var cImageSrc='/images/loading-sprites.png';
	
	var cImageTimeout=false;
	var cIndex=0;
	var cXpos=0;
	var SECONDS_BETWEEN_FRAMES=0;
	
	function startAnimation(){
		
		document.getElementById('loaderImage').style.backgroundImage='url('+cImageSrc+')';
		document.getElementById('loaderImage').style.width=cWidth+'px';
		document.getElementById('loaderImage').style.height=cHeight+'px';
		
		//FPS = Math.round(100/(maxSpeed+2-speed));
		FPS = Math.round(100/cSpeed);
		SECONDS_BETWEEN_FRAMES = 1 / FPS;
		
		setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES/1000);
		
	}
	
	function continueAnimation(){
		
		cXpos += cFrameWidth;
		//increase the index so we know which frame of our animation we are currently on
		cIndex += 1;
		 
		//if our cIndex is higher than our total number of frames, we're at the end and should restart
		if (cIndex >= cTotalFrames) {
			cXpos =0;
			cIndex=0;
		}
		
		document.getElementById('loaderImage').style.backgroundPosition=(-cXpos)+'px 0';
		
		setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES*1000);
	}
	
	function imageLoader(s, fun)//Pre-loads the sprites image
	{
		clearTimeout(cImageTimeout);
		cImageTimeout=0;
		genImage = new Image();
		genImage.onload=function (){cImageTimeout=setTimeout(fun, 0)};
		genImage.onerror=new Function('alert(\'Could not load the image\')');
		genImage.src=s;
	}
	
	//The following code starts the animation
	new imageLoader(cImageSrc, 'startAnimation()');

	