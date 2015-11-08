$(function(){
	var waitOnMessage = 7000; // How long to show one message before moving to next
	
	var messageWindow = $('.feed');
	var currentMessage = $('.parent.message:first',messageWindow).addClass('selected',500);
	var newMessages = [];
	
	var selectNextMessage = function(){
		if( currentMessage.length > 0 ) {
			currentMessage.removeClass('selected',500);
		}
		
		if( newMessages.length > 0 ) {
			// Select most recently added message
			currentMessage = $('#message_' + newMessages.pop());
		} else {
			// Select next message
			currentMessage = currentMessage.next('.parent.message');
		}
		
		if( currentMessage.length === 0 ) {
			// Select first message
			currentMessage = $('.parent.message:first',messageWindow);
		}

		currentMessage.stop(true,true).addClass('selected',500,function(){
			messageWindow.scrollTo(currentMessage,500);
		});
	};
	
	var messageScroller = window.setInterval(selectNextMessage,waitOnMessage);
	
	$('.pause-visualization').each(function(){
		var pauseButton = $(this);
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
				messageScroller = window.setInterval(selectNextMessage,waitOnMessage);
				pauseButton.attr('title',pauseButton.attr('data-title-pause'))
					.find('i').removeClass('fa-pause').addClass('fa-pause');
				pauseButton.tooltip({
					title: pauseButton.attr('data-title-pause'),
					placement: 'bottom'
				});
			}
		});
	});
	
	// Handle added and removed messages
	messageWindow.on('jquerychat.messageInserted',function(event, messageid){
		newMessages.push(messageid);
		// If message was added to above, need to adjust scroll
		if(currentMessage.length > 0 ) {
			messageWindow.scrollTo(currentMessage);
		}
	});
	messageWindow.on('jquerychat.messageDeleted',function(event, messageid){
		// If message was removed from above, need to adjust scroll
		if(currentMessage.length > 0 ) {
			messageWindow.scrollTo(currentMessage);
		}
	});
});
