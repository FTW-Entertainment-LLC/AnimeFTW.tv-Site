(function($)
{
	$.Redactor.prototype.smilies = function()
	{
		return {
			langs: {
				en: {
					"smilies": "<img src='https://d206m0dw9i4jjv.cloudfront.net/themes/default/smilies/=%29-20x20.png' alt='' />"
				}
			},
			init: function()
			{
				var smilies = {
					"0": {
						title: ";^2",
						code: "<img src='https://d206m0dw9i4jjv.cloudfront.net/themes/default/smilies/;%5E2-20x20.png' alt='' />",
						args: [';%5E2-20x20.png',';%5E2-128x128.png']
					},
					"1": {
						title: "^-^",
						code: "<img src='https://d206m0dw9i4jjv.cloudfront.net/themes/default/smilies/%5E-%5E-20x20.png' alt='' />",
						args: ['%5E-%5E-20x20.png','%5E-%5E-128x128.png']
					},
					"2": {
						title: "=)",
						code: "<img src='https://d206m0dw9i4jjv.cloudfront.net/themes/default/smilies/=%29-20x20.png' alt='' />",
						args: ['=%29-20x20.png','=%29-128x128.png']
					}
				};


				var that1 = this;
				var dropdown1 = {};

				$.each(smilies, function(i, s)
				{
					dropdown1[i] = { title: s.code, func: 'inline.format', args: s.args };
				});


				var button = this.button.addAfter('html', 'inline', this.lang.get('smilies'));
				this.button.addDropdown(button, dropdown1);

			}


		};
	};
})(jQuery);