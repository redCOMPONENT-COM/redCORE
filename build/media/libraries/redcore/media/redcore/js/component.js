(function ($) {

	// Custom function to submit text field when enter is pressed on it
	$.fn.extend({
		enterSubmits: function() {
			var field = $(this);

			// Key pressed?
			field.keydown(function(event) {
				// Key is enter?
				if (event.which === 13) {
					// Submit parent form
					field.closest("form").submit();
				}
			});
		  return $(this);
		}
	});

	$(document).ready(function () {

		// Auto submit search fields
		$('.js-enter-submits').enterSubmits();

		$('*[rel=tooltip]').each(function () {
            if ($(this).tooltip){
                $(this).tooltip({
                    "animation":true,
                    "html":true
                });
            }
        });

        // Old Joomla tooltip
        $('*[rel=tooltip]').each(function () {
            if ($(this).tooltip){
                $(this).tooltip({
                    "animation":true,
                    "html":true
                });
            }
        });

        $('.hasTooltip').each(function () {
            if ($(this).tooltip){
                $(this).tooltip({
                    "animation":true,
                    "html":true
                });
            }
        });

        rRadioGroupButtonsSet();
        rRadioGroupButtonsEvent();

        if (typeof Joomla == 'object')
        {
            /**
             * Generic submit form
             *
             * Needed for frontend since Joomla does not have it in the frontend
             */
            Joomla.submitform = function(task, form) {
                if (typeof(form) === 'undefined') {
                    form = document.getElementById('adminForm');
                }

                if (typeof(task) !== 'undefined' && task !== "") {
                    form.task.value = task;
                }

                // Submit the form.
                if (typeof form.onsubmit == 'function') {
                    form.onsubmit();
                }
                if (typeof form.fireEvent == "function") {
                    form.fireEvent('submit');
                }
                form.submit();
            };
        }

        // Quick temporary fix for 3.6.4
        var dataApiDropdownEventHandler = $._data(document, 'events').click.filter(function (el) {
            return el.namespace === 'bs.data-api.dropdown' && el.selector === undefined
        });

        if (dataApiDropdownEventHandler[0] != null) {
            dataApiDropdownEventHandler[0].namespace = 'data-api.dropdown';
        }
    });

    // add color classes to chosen field based on value
    $('select[class^="chzn-color"], select[class*=" chzn-color"]').on('liszt:ready', function(){
        var select = $(this);
        var cls = this.className.replace(/^.(chzn-color[a-z0-9-_]*)$.*/, '\1');
        var container = select.next('.chzn-container').find('.chzn-single');
        container.addClass(cls).attr('rel', 'value_' + select.val());
        select.on('change click', function()
        {
            container.attr('rel', 'value_' + select.val());
        });

    });
})(jQuery);

function rRadioGroupButtonsSet (selector) {
    selector = typeof(selector) != 'undefined' ? selector : '';
    // Turn radios into btn-group
    jQuery(selector + ' .radio.btn-group label')
        .removeClass('btn')
        .removeClass('btn-default')
        .addClass('btn')
        .addClass('btn-default');

    jQuery(selector + " .btn-group label:not(.active)").click(function () {
        var label = jQuery(this);
        var input = jQuery('#' + label.attr('for'));

        if (!input.prop('checked')) {
            label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
            if (input.val() == '') {
                label.addClass('active btn-primary');
            } else if (input.val() == 0) {
                label.addClass('active btn-danger');
            } else {
                label.addClass('active btn-success');
            }
        }
    });
}

function rRadioGroupButtonsEvent (selector) {
    selector = typeof(selector) != 'undefined' ? selector : '';
    jQuery(selector + " .btn-group input[checked=checked]").each(function () {
        if (jQuery(this).val() == '') {
            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
        } else if (jQuery(this).val() == 0) {
            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
        } else {
            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
        }

    });
}

/**
 * listItemTask with form element id as parameter
 *
 * @param id   The item id
 * @param task The task name
 * @param f    The form element id
 * @return
 */
function listItemTaskForm(id, task, f) {

	f = document.getElementById(f);

	if (typeof(f) === 'undefined') {
		f = document.getElementById('adminForm');
	}

	var cb = f[id];

	if (cb) {

		for (var i = 0; true; i++) {
			var cbx = f['cb' + i];

			if (!cbx) {
				break;
			}

			cbx.checked = false;
		}

		cb.checked = true;
		f.boxchecked.value = 1;
		Joomla.submitform(task, f);
	}

	return false;
}
