(function($)
{
	$.Redactor.prototype.imagemanager = function()
	{
		return {
			langs: {
				en: {
					"upload": "Upload",
					"choose": "Choose"
				}
			},
			init: function()
			{
				if (!this.opts.imageManagerJson)
				{
					return;
				}

				this.modal.addCallback('image', this.imagemanager.load);
			},
			load: function()
			{
				var $box = $('<div style="overflow: auto; height: 300px; display: none;" class="redactor-modal-tab" data-title="Choose">');
				this.modal.getModal().append($box);

				$.ajax({
					dataType: "json",
					cache: false,
					url: this.opts.imageManagerJson,
					success: $.proxy(function(data)
					{
						$.each(data, $.proxy(function(key, val)
						{
							// title
							var thumbtitle = '';
							if (typeof val.title !== 'undefined')
							{
								thumbtitle = val.title;
							}

							var img = $('<img src="' + val.thumb + '" rel="' + val.image + '" title="' + thumbtitle + '" style="width: 100px; height: 75px; cursor: pointer;" />');
							$box.append(img);
							$(img).click($.proxy(this.imagemanager.insert, this));

						}, this));

					}, this)
				});


			},
			insert: function(e)
			{
				var $el = $(e.target);

				var img = document.createElement('img');
				img.src = $el.attr('rel');
				img.alt = $el.attr('title');
				img.title = $el.attr('title');

				this.image.insert(img);
			}
		};
	};
})(jQuery);