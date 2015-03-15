jQuery.fn.tooltip= function(options) {
	this.each(function(){
		var settings = {
			tooltipcontentclass:"searchTipcontent",
			width:200,
			postion:"absolute",
			zindex:100 
		};
		if(options) {
			jQuery.extend(settings, options);
		}
		jQuery(this).children("."+settings.tooltipcontentclass).hide();
		jQuery(this).hover(function() {
			var de = document.documentElement;
			var w = self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
			var hasArea = w - jQuery.fn.getAbsoluteLeftObject(this);
			var clickElementy = jQuery.fn.getAbsoluteTopObject(this) - 3; //set y position
			var title="&nbsp;";
			jQuery("body").append("<div id='NT'><div id='NT_copy'><div >"+jQuery(this).children("."+settings.tooltipcontentclass).html()+"</div></div></div>");//right side
			var arrowOffset =  this.offsetWidth + 11;
			var clickElementx = jQuery.fn.getAbsoluteLeftObject(this) + arrowOffset;
			jQuery('#NT').css({left: clickElementx+"px", top: clickElementy+"px"});
			jQuery('#NT').css({width: settings.width+"px"});
			jQuery('#NT').css({position: settings.postion});
			jQuery('#NT').css("z-index",settings.zindex);
			jQuery('#NT').show();
		} ,
		function() {
			jQuery("#NT").remove();
	})

	});
}
jQuery.fn.getAbsoluteLeftObject=function(o) {
	// Get an object left position from the upper left viewport corner
	oLeft = o.offsetLeft            // Get left position from the parent object
	while(o.offsetParent!=null) {   // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent    // Get parent object reference
		oLeft += oParent.offsetLeft // Add parent left position
		o = oParent
	}
	return oLeft
}

jQuery.fn.getAbsoluteTopObject=function (o) {
	// Get an object top position from the upper left viewport corner
	oTop = o.offsetTop            // Get top position from the parent object
	while(o.offsetParent!=null) { // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent  // Get parent object reference
		oTop += oParent.offsetTop // Add parent top position
		o = oParent
	}
	return oTop
}




