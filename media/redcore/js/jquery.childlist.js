;(function ($, window, document, undefined) {

	// Create the defaults once
	var pluginName = "childlist";

	var defaults = {
		parentSelector : '.js-childlist-parent',
		parentVarName      : null,
		parentOnChange : true,
		childSelector  : '.js-childlist-child',
		ajaxUrl        : null
	};

	// The actual plugin constructor
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;

		// Initialise selectors
		this.theForm        = this.element;

		// Fields
		this.parentField = $(this.options.parentSelector);
		this.childField  = $(this.options.childSelector);

		// Initial values
		this.parentValue = this.getFieldValue(this.options.parentSelector);
		this.childValue  = this.getFieldValue(this.options.childSelector);

		// Selector values
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype = {
		init: function () {
			var self = this;

			self.parentField.change(function(e) {

				self.parentValue  = self.getFieldValue(self.parentField);

				// Execute AJAX query
				$.ajax({
					url: self.options.ajaxUrl,
					type: "POST",
					dataType: "json",
					cache: false,
					data: self.options.parentVarName + "=" + self.parentValue,
					success: function(data){
						var options = "";
						if (data !== 'false')
						{
							data.each(function(item) {
								options += '<option value="' + item['value'] + '"';
								if (item['value'] === self.childValue)
								{
									options += ' selected="selected"';
								}
								options += ' >' + item['text'] + '</option>';
							});
						}

						// Fill the child select
						self.childField.empty().append(options);
						self.childField.trigger('liszt:updated');
					}
				});
			});
		},
		getFieldValue: function (element) {
			var value = null;

			if ($(element).is('select')) {
				var option = $(element).find('option:selected');
				value = option.val();
			} else {
				value = $(element).val();
			}

			return value;
		},
		isFieldActive: function (element) {
			var self = this;

			var value = self.getFieldValue(element);

			return option.val() != '';
		},
		resetField: function (element) {
			$(element).val('');
			$(element).trigger('liszt:updated');
		},
		submitForm: function() {
			this.theForm.submit();
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);
