$(function(){
	var toggle = function(toggler){
		var toggled = toggler.closest('fieldset').find('.toggled');
		toggled.toggle(toggler.prop('checked'));
	}
	$(document).on('click','input.toggler[type=checkbox]',function(){
		toggle($(this));
	});
	$('input.toggler[type=checkbox]').each(function(){
		toggle($(this));
	});
});
