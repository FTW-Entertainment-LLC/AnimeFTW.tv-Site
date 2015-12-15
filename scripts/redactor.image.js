(function($)
{
	$.Redactor.prototype.insertimage = function()
	{
		return {
			langs: {
				en: {
					"insertimage": "Image",
					"image-html-code": "Insert an Image"
				}
			},
			getTemplate: function()
			{
				return String()
				+ '<div class="modal-section" id="redactor-modal-image-insert">'
					+ '<section>'
						+ '<label>' + this.lang.get('image-html-code') + '</label>'
						+ '<textarea id="redactor-insert-image-area" style="height: 60px;"></textarea>'
					+ '</section>'
					+ '<section>'
						+ '<button id="redactor-modal-button-action">Insert</button>'
						+ '<button id="redactor-modal-button-cancel">Cancel</button>'
					+ '</section>'
				+ '</div>';
			},
			init: function()
			{
				var button = this.button.addAfter('line', 'insertimage', this.lang.get('insertimage'));
				this.button.addCallback(button, this.insertimage.show);
			},
			show: function()
			{
				this.modal.addTemplate('image', this.insertimage.getTemplate());

				this.modal.load('image', this.lang.get('insertimage'), 700);

				// action button
				this.modal.getActionButton().text(this.lang.get('insertimage')).on('click', this.insertimage.insert);
				this.modal.show();

				$('#redactor-insert-image-area').focus();

			},
			insert: function()
			{
				var data = $('#redactor-insert-image-area').val();

				if (!data.match(/<iframe|<video/gi))
				{

					// parse if it is link on youtube & vimeo
					data = '<img src="' + data + '" alt="" title="" //>';
				}

				this.modal.close();
				this.placeholder.remove();

				// buffer
				this.buffer.set();

				// insert
				this.air.collapsed();
				this.insert.html(data);

			}

		};
	};
})(jQuery);