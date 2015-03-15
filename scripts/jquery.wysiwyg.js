function action(e,a,p)
{
	$(e).parents().each(function(){
		var obj = $(this);
		var classes = obj.attr('class').split(' ');
		if ($.inArray('de77_wysiwyg', classes) > -1)
		{
			obj.find('.de77_editor').focus();
		}		
	});

	if (p == null) p = false;
	document.execCommand(a,null,p);
}

function makeToolbar(el)
{
	toolbar = $.ajax({url:'/jquery.wysiwyg.htm',async:false}).responseText;
	el.parent().prepend(toolbar);

}

(function($){
 $.fn.de77_wysiwyg = function(options) {
 	if (options == false)
 	{
		return this.each(function() {
			var obj = $(this);
			obj.parent().find('.de77_toolbar').detach();
			obj.unwrap();
			obj.removeClass('de77_editor');
			this.contentEditable="false";			
		});				
	}
	else
	{
		return this.each(function() {
			var obj = $(this);
			var classes = obj.attr('class').split(' ');
			if ($.inArray('de77_editor', classes) > -1)
			{
				return false;
			}
			obj.addClass('de77_editor');
			obj.wrap('<div class="de77_wysiwyg"></div>');		
			this.contentEditable="true";		
			makeToolbar($(this));
		});	
	}
 };
})(jQuery);