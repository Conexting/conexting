$(function(){
	var messageWindow = $('.feed');
	var currentMessage = $('.parent.message:first',messageWindow).addClass('selected',500);
	var startVisualization = function(){
		if( currentMessage.length > 0 ) {
			currentMessage.removeClass('selected',500);
			currentMessage = currentMessage.next('.parent.message');
			if( currentMessage.length === 0 ) {
				currentMessage = $('.parent.message:first',messageWindow);
			}
			currentMessage.addClass('selected',500,function(){
				messageWindow.scrollTo(currentMessage,500);
			});
		} else {
			currentMessage = $('.parent.message:first',messageWindow).addClass('selected',500);
		}
	};
	messageWindow.on('jquerychat.messageInserted',function(){
		if( currentMessage.length > 0 ) {
			messageWindow.scrollTo(currentMessage,0);
		}
	});
	
	var messageScroller = window.setInterval(startVisualization,4000);
	
	$('.pause-visualization').each(function(){
		var pauseButton = $(this);
		var menu = pauseButton.closest('.navbar');
		pauseButton.tooltip({
			title: pauseButton.attr('data-title-pause'),
			placement: 'bottom'
		});
		pauseButton.on('click',function(e){
			e.preventDefault();
			pauseButton.tooltip('destroy').attr('data-original-title',null);
			if( messageScroller ) {
				window.clearInterval(messageScroller);
				messageScroller = false;
				pauseButton.attr('title',pauseButton.attr('data-title-resume'))
					.find('i').removeClass('fa-pause').addClass('fa-play');
				pauseButton.tooltip({
					title: pauseButton.attr('data-title-resume'),
					placement: 'bottom'
				});
			} else {
				messageScroller = window.setInterval(startVisualization,4000);
				pauseButton.attr('title',pauseButton.attr('data-title-pause'))
					.find('i').removeClass('fa-pause').addClass('fa-pause');
				pauseButton.tooltip({
					title: pauseButton.attr('data-title-pause'),
					placement: 'bottom'
				});
			}
		});
	});
});
