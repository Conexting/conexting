////
// jQuery Poll 1.0
// author: mikko.vuorinen@conexting.com
// license: MIT
////
(function($){
	var settings = {
		url: null, // Required: e.g. "/poll/{cmd}/1"
		analytics: null, // Optional (ga-object)
		analyticsLabel: null, // Optional, label for analytics events (e.g. poll title)
		choiceSelector: '.choice',
		allowVoting: true,
		limitVotes: true,
		limitChoices: true,
		refreshRate: 20,
		strings: {
		}
	};
	
	var methods = {
		settings: function(options){
			$.extend(true,settings,options);
		},
		init: function(options){
			return this.each(function(){
				var poll = $(this);
				
				if( options ) { 
					$.extend(true,settings,options);
				}
				
				poll.data('poll',{});
				
				if( settings.allowVoting ) {
					poll.addClass('allowVoting');
					// Clicking poll gives user's vote to that choise
					var selector = settings.choiceSelector;
					if( settings.limitVotes ) {
						selector += ':not(.selected)';
					}
					poll.on('click',selector,function(){
						var choiceElement = $(this);
						var choice = choiceElement.data('choice');
						poll.append('<div class="loader"></div>');
						$.post(settings.url.replace('{cmd}','vote'),{
							choice: choice,
							time: new Date()
						},function(data,status){
							$('.loader',poll).remove();
							choiceElement.addClass('selected').find('.progress').addClass('progress-striped');
							if( settings.limitChoices ) {
								choiceElement.closest(settings.choiceSelector).siblings(settings.choiceSelector)
									.removeClass('selected').find('.progress').removeClass('progress-striped');
							}
							poll.poll('updatePoll');
							// Track analytics
							settings.analytics('send','event','PollVoted',settings.analyticsLabel);
						});
						return false;
					});
				}
				
				// Updaters for polls
				poll.poll('updatePoll');
				poll.data('poll').pollUpdater = setInterval(
					function(){poll.poll('updatePoll');},
					settings.refreshRate*1000
				);
			});
		},
		updatePoll: function(){
			$(this).each(function(){
				var poll = $(this);
				$.getJSON(settings.url.replace('{cmd}','getVotes'),{time: new Date()},function(data){
					if( data == null ) {
						return;
					}
					
					var maxResult = 1;
					$(settings.choiceSelector,poll).each(function(){
						var choice = parseInt(data[$(this).data('choice')]);
						if( choice > maxResult ) {
							maxResult = choice;
						}
					});
					$(settings.choiceSelector,poll).each(function(){
						var percent = (parseInt(data[$(this).data('choice')])*100) / maxResult;
						$('.progress .bar',this).css('width',percent+'%');
						$('.choiceCount',this).text(data[$(this).data('choice')]);
					});
				});
			});
		}
	};

	$.fn.poll = function(method){
		if( methods[method] ) {
			return methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		} else if( typeof method === 'object' || !method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error('Method ' +  method + ' does not exist on jQuery.poll');
			return this;
		}
	};
})(jQuery);
