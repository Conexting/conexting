$(function(){
	$('.hide-menu').each(function(){
		var hideButton = $(this);
		var hideButtonParent = $('.hide-menu').parent();
		var menu = hideButton.closest('.navbar');
		hideButton.tooltip({
			title: hideButton.attr('data-title-hide'),
			placement: 'bottom'
		});
		$(this).on('click',function(e){
			e.preventDefault();
			hideButton.tooltip('destroy').attr('data-original-title',null);
			if( menu.is(':visible') ) {
				menu.hide('blind',function(){
					hideButton.detach()
						.appendTo(menu.parent())
						.attr('title',hideButton.attr('data-title-show'))
						.find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
					hideButton.tooltip({
						title: hideButton.attr('data-title-show'),
						placement: 'bottom'
					});
				});
			} else {
				hideButton.detach()
					.appendTo(hideButtonParent)
					.find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
				menu.show('blind',function(){
					hideButton.tooltip({
						title: hideButton.attr('data-title-hide'),
						placement: 'bottom'
					});
				});
			}
		});
	});
});
