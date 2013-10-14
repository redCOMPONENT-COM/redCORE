(function ($) {

	// Custom function to submit text field whem enter is pressed on it
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

		$('*[rel=tooltip]').tooltip({
			"animation":true,
			"html":true
		});

		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function () {
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		$(".btn-group input[checked=checked]").each(function () {
			if ($(this).val() == '') {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			} else if ($(this).val() == 0) {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
			}
		});
	})
})(jQuery);

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
