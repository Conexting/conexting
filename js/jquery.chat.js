////
// jQuery Chat 1.0
// author: mikko.vuorinen@uta.fi
// license: MIT
////
(function($){
	var settings = {
		url: null, // Required: e.g. "/chat/{cmd}/1"
		analytics: function(){}, // Optional (ga-object)
		isAdmin: false,
		isQueue: false,
		feedSelector: '.feed',
		imagesSelector: '.images',
		searchSelector: '.search',
		pinnedSelector: '.pinnedFeed',
		searchRefreshTimeout: 300, // 300 milliseconds
		refreshRate: 20,
		messageCount: 20,
		maxMessages: 0, // 0 = unlimited
		maxLength: 140,
		showUserImages: true,
		showTimestamps: true,
		allowReply: true,
		threaded: true,
		highlightNewAfter: new Date()-2*60*1000, // 2 minutes ago
		highlightNewDuration: 2*60*1000, // 2 minutes
		highlightBackgroundColor: '#fafa77',
		strings: {
			noComments: 'No messages yet',
			noMoreComments: 'No more messages available',
			sendError: 'Error sending message.',
			reply: 'reply',
			cancelReply: 'cancel',
			nicknameInput: 'Insert your nickname',
			msgPlaceholder: 'Write new message',
			msgMaxLengthReached: 'Maximum message length reached',
			admin: {
				pinMessage: 'Pin this message',
				unpinMessage: 'Unpin message',
				removeMessage: 'Remove message',
				restoreMessage: 'Restore message',
				approveMessage: 'Approve message',
				confirmRemoveMessage: 'Delete this message permanently?'
			}
		}
	};
	
	var linkPattern = /(http(?:s)?:\/\/(?:.+?))(\. |, | |\)|\(|\")/ig;
	var linkAtEndPattern = /(http(?:s)?:\/\/(?:\S+?))(\.?)$/ig;
	var hashtagPattern = /#([\wäö]+)/ig;
	var twitterUserPattern = /@([A-Za-z0-9_]{1,15})/g;
	
	var methods = {
		settings: function(options){
			$.extend(true,settings,options);
		},
		init: function(options){
			return this.each(function(){
				var chat = $(this);
				
				if( options ) { 
					$.extend(true,settings,options);
				}
				
				chat.data('chat',{});
				$('.readmore',chat).attr('rel',settings.messageCount);
				$('.nickname-modal',chat).modal({
					show: false
				}).on('click','.nickname-save',function(){
					var username = $.trim($('.nickname-modal input',chat).val());
					if( username.length === 0 ) {
						// Do not close if the nickname is empty
					} else {
						$('.my_username',chat).text(username);
						chat.chat('registerUser');
						$('.nickname-modal',chat).modal('hide');
					}
				});
				
				chat.on('keypress','input.messageText',function(event){
					// Enter-key sends the message
					if( event.keyCode === '13' ) {
						event.preventDefault();
						chat.chat('sendMessage');
					}
				}).on('keyup','input.messageText',function(event){
					// Check maximum length
					var mentionLength = 0;
					var parentMessage = $(this).closest('.message');
					if( parentMessage.length > 0 ) {
						// Sender account mention is required when replying using twitter
						mentionLength = parentMessage.children('.sender-account').text().length + 2;
					}
					var messageLength = $(this).val().length;
					var usernameLength = $('.my_username',chat).text().length;
					if( messageLength + usernameLength + mentionLength > settings.maxLength  ) {
						event.preventDefault();
						$(this).val($(this).val().substr(0,settings.maxLength-usernameLength-mentionLength));
						chat.chat('showInfo',settings.strings.msgMaxLengthReached,'warning');
					}
				}).on('click','button.send',function(){
					chat.chat('sendMessage');
					return false;
				}).on('click','.message a.reply',function(){
					// Clicking 'reply' moves message input under the message that user replies to
					if( $(this).text() === settings.strings.reply ) {
						$('.message a.reply',chat).text(settings.strings.reply).removeClass('cancel-reply');
						if( $('.msg-placeholder',chat).length > 0 ) {
							$('.msg',chat).remove().appendTo($(this).closest('.message'));
						} else {
							$('.msg',chat)
								.replaceWith($('<p class="msg-placeholder">'+settings.strings.msgPlaceholder+'</p>'))
								.appendTo($(this).closest('.message'));
						}
						$('.msg .messageText',chat).focus();
						$(this).text(settings.strings.cancelReply).addClass('cancel-reply');
					} else {
						$('.msg-placeholder').replaceWith($('.msg'));
						$(this).text(settings.strings.reply).removeClass('cancel-reply');
						$('.msg .messageText').focus();
					}
					return false;
				}).on('click','.msg-placeholder',function(){
					// Clicking placeholder (original message input) cancels reply
					$('a.cancel-reply',chat).click();
					$('.msg input',chat).focus();
					return false;
				}).on('click','.readmore',function(){
					// "Read more" -link loads older messages
					var offset = parseInt($(this).attr('rel'));
					if( settings.maxMessages > 0 ) {
						settings.maxMessages += offset;
					}
					chat.chat('updateFeed',offset);
					offset += settings.messageCount;
					$(this).attr('rel',offset);
					return false;
				});
				
				if( settings.isAdmin ) {
					// Admin can remove, restore and highlight messages
					chat.on('click','.admin-delete-message',function(){
						var message = $(this).closest('.message');
						var messageid = message.data('id');
						var teksti = message.children('.message-content').text();
						var viesti = '"'+teksti+'"\n\n'+settings.strings.admin.confirmRemoveMessage;
						if( !confirm(viesti) ) {
							return false;
						}
						message.prepend('<div class="loader"></div>');
						$.post(settings.url.replace('{cmd}','deleteMessage'),{
							messageid: messageid,
							time: new Date()
						},function(data){
							if( data.error ) {
								$('.loader',message).remove();
								alert(data.error);
							} else {
								message.remove();
								settings.analytics('send','event','Chat','AdminMessageRemoved');
							}
						});
						return false;
					}).on('click','.admin-restore-message',function(){
						var message = $(this).closest('.message');
						var messageid = message.data('id');
						message.prepend('<div class="loader"></div>');
						$.post(settings.url.replace('{cmd}','restoreMessage'),{
							messageid: messageid,
							time: new Date()
						},function(data){
							if( data.error ) {
								$('.loader',message).remove();
								alert(data.error);
							} else {
								message.remove();
								settings.analytics('send','event','Chat','AdminMessageRestored');
							}
						});
						return false;
					}).on('click','.admin-pin-message',function(){
						var message = $(this).closest('.message');
						var messageid = message.data('id');
						message.prepend('<div class="loader"></div>');
						$.post(settings.url.replace('{cmd}','pinMessage'),{
							messageid: messageid,
							time: new Date()
						},function(data){
							$('.loader',message).remove();
							if( data.error ) {
								alert(data.error);
							} else if(data.message) {
								var parentFeed = $(settings.pinnedSelector,chat);
								if( parentFeed.length === 0 ) {
									parentFeed = $(settings.feedSelector,chat);									
								}
								message.data('pinned',data.message.pinned);
								message.detach();
								chat.chat('insertMessageElement',message,parentFeed);
								message.addClass('pinned');
								settings.analytics('send','event','Chat','AdminMessagePinned');
							}
						});
						return false;
					}).on('click','.admin-unpin-message',function(){
						var message = $(this).closest('.message');
						var messageid = message.data('id');
						message.prepend('<div class="loader"></div>');
						$.post(settings.url.replace('{cmd}','unpinMessage'),{
							messageid: messageid,
							time: new Date()
						},function(data){
							$('.loader',message).remove();
							if( data.error ) {
								alert(data.error);
							} else if(data.message) {
								message.removeClass('pinned');
								message.data('pinned',data.message.pinned);
								message.detach();
								chat.chat('insertMessageElement',message,$(settings.feedSelector,chat));
								settings.analytics('send','event','Chat','AdminMessageUnpinned');
							}
						});
						return false;
					}).on('mouseenter','.admin-icon',function(){
						$(this).closest('.message').addClass('admin-hover');
					}).on('mouseleave','.admin-icon',function(){
						$(this).closest('.message').removeClass('admin-hover');
					});
					// Admin can approve messages
					if( settings.isQueue ) {
						chat.on('click','.admin-approve-message',function(){
							var message = $(this).closest('.message');
							var messageid = message.data('id');
							message.prepend('<div class="loader"></div>');
							$.post(settings.url.replace('{cmd}','approveMessage'),{
								messageid: messageid,
								time: new Date()
							},function(data){
								if( data.error ) {
									$('.loader',message).remove();
									alert(data.error);
								} else {
									message.remove();
									settings.analytics('send','event','Chat','AdminMessageApproved');
								}
							});
							return false;
						});
					}
					
					// Admin sends admin-messages
					$('.msg').addClass('adminmessage');
				}
				
				// If username is not set, prompt for username
				if( $('.my_username',chat).length > 0 && $('.my_username',chat).text() === '' ) {
					chat.chat('changeUsername');
				}
				chat.on('click','.change-username',function(){
					chat.chat('changeUsername');
					return false;
				});
				
				// Updaters for chat
				chat.chat('updateFeed');
				chat.data('chat').lastUpdate = null;
				chat.data('chat').feedUpdater = setInterval(
					function(){chat.chat('updateFeed');},
					settings.refreshRate*1000
				);
					
				// Messages are shown/hidden when search term is changed
				chat.on('keyup',settings.searchSelector,function(){
					var searchInput = $(this);
					clearTimeout(chat.data('chat').searchUpdater);
					chat.data('chat').searchUpdater = setTimeout(
						function(){
							if( searchInput.val().length > 0 ) {
								chat.chat('filterFeed',new RegExp(searchInput.val(),'i'),searchInput.data('content'));
							} else {
								chat.chat('filterFeed',false);
							}
						},
						settings.searchRefreshTimeout
					);
				});
				
			});
		},
		insertMessage: function(message){
			var chat = this;
			
			if( message === null ) {
				return this;
			}
			if( $('#message_'+message.messageid,chat).length > 0 ) {
				// The message already exists
				return this;
			}
			
			var feed = $(settings.feedSelector,chat);
			
			var newMessage = $('<div class="message" id="message_'+message.messageid+'"></div>');
			newMessage.data('id',message.messageid);
			var text = message.text;
			
			// Create links
			text = text.replace(linkPattern,'<a href="$1" rel="nofollow">$1</a>$2');
			text = text.replace(linkAtEndPattern,'<a href="$1" rel="nofollow">$1</a>$2');
			// Link hashtags to twitter
			text = text.replace(hashtagPattern,'<a href="http://twitter.com/search?q=%23$1">#$1</a>');
			// Link twitter usernames
			text = text.replace(twitterUserPattern,'<a href="http://twitter.com/$1">@$1</a>');
			
			if( message.twitter_username ) {
				text = '<a href="http://www.twitter.com/'+message.twitter_username+'" class="sender username">@'+message.username+'</a>: <span class="message-content">'+text+'</span>';
			} else if( message.username ) {
				text = '<span class="sender username">'+message.username+'</span>: <span class="message-content">'+text+'</span>';
			} else {
				text = '<span class="message-content">'+text+'</span>';
			}
			if( settings.showUserImages && message.userimage ) {
				newMessage.addClass('withimage');
				text = '<img class="userimage" src="'+message.userimage+'" alt="'+message.username+'" />'+text;
			}
			
			newMessage.data('replyto',message.replyto);
			if( settings.allowReply && settings.threaded ) {
				// Append reply-butto
				text += ' <a href="#" class="reply command">'+settings.strings.reply+'</a>';
			}
			
			newMessage.data('timestamp',message.timestamp);
			if( settings.showTimestamps ) {
				// Append send time to text
				var time = $.timeago(new Date(message.timestamp*1000));
				text += '<span class="time"> - <abbr class="timeago" title="'+ISODateString(new Date(message.timestamp*1000))+'">'+time+'</abbr></span>';
			}

			text = text.replace(/\s*(.*)\s*/,'$1');
			var newMessageContent = $('<p></p>').appendTo(newMessage);
			newMessageContent.html(text);
			if( settings.isAdmin ) {
				if( !message.deleted ) {
					newMessageContent.prepend('<a class="admin-delete-message admin-icon" rel="tooltip" title="'+settings.strings.admin.removeMessage+'" href="#"><i class="fa fa-trash"></i></a>');
					newMessageContent.prepend('<a class="admin-pin-message admin-icon" rel="tooltip" title="'+settings.strings.admin.pinMessage+'" href="#"><i class="fa fa-star-o"></i></a>');
					newMessageContent.prepend('<a class="admin-unpin-message admin-icon" rel="tooltip" title="'+settings.strings.admin.unpinMessage+'" href="#"><i class="fa fa-star"></i></a>');
				} else {
					newMessageContent.prepend('<a class="admin-restore-message admin-icon" rel="tooltip" title="'+settings.strings.admin.restoreMessage+'" href="#"><i class="fa fa-share-square-o"></i></a>');
				}
				
				if( settings.isQueue && !message.approved ) {
					newMessageContent.prepend('<a class="admin-approve-message admin-icon" rel="tooltip" title="'+settings.strings.admin.approveMessage+'" href="#"><i class="fa fa-check-square-o fa-2x"></i></a>');
				}
			}
			
			// If there are images in the message, add them to the end of the message
			if( message.images ) {
				if( message.images.length > 0 ) {
					newMessageContent.append('<div class="clear"></div>');
					$.each(message.images,function(key,image){
						if( $('#messagemedia_'+image.id).length === 0 ) {
							var src = image.media_url;
							if( $.inArray('thumb',image.sizes) > -1 ) {
								src += ':thumb';
							}
							var img = '<img alt="'+message.text+'" src="'+src+'"></img>';
							$('<a id="messagemedia_'+image.id+'" href="'+image.twitter_url+'">'+img+'</a>').appendTo(newMessageContent);
						}
					});
					newMessageContent.append('<div class="clear"></div>');
				}
			}
			
			// Mark pinned messages
			var parentFeed = feed;
			newMessage.data('pinned',message.pinned);
			if( message.pinned ) {
				newMessage.addClass('pinned');
				var pinnedFeed = $(settings.pinnedSelector,chat);
				if( pinnedFeed.length > 0 ) {
					parentFeed = pinnedFeed;
				}
			}
			
			chat.chat('insertMessageElement',newMessage,parentFeed);
			
			// Mark admin-messages
			if( message.adminmessage ) {
				newMessage.addClass('adminmessage');
			}
			
			newMessage.hide().fadeIn('slow');
			var highlightDate = null;
			if( message.approved ) {
				highlightDate = new Date(message.approved*1000);
			} else {
				highlightDate = new Date(message.timestamp*1000);
			}
			
			if( highlightDate > settings.highlightNewAfter ) {
				var originalBg = newMessage.css('backgroundColor');
				newMessage.css('backgroundColor',settings.highlightBackgroundColor).animate({backgroundColor:originalBg},settings.highlightNewDuration,'easeOutQuint');
			}
			
			// Ensure feed length is within limits
			if( settings.maxMessages > 0 ) {
				if( $('.feed .message.parent',chat).length > settings.maxMessages ) {
					$('.feed .message.parent:last',chat).fadeOut('slow',function(){
						$(this).remove();
					});
				}
			}
			
			return this;
		},
		updateFeed: function(offset){
			var chat = this;
			return this.each(function(){
				var feed = $(settings.feedSelector,this);
				
				if( feed.children().length === 0 || offset > 0 ) {
					feed.append('<div class="loader"/></div>');
				}
				
				var sinceId = false;
				if( !offset ) {
					var newestComment = $('.feed .message.parent:first',chat);
					if( newestComment.length > 0 ) {
						sinceId = newestComment.data('id');
					}
				}
				
				$.getJSON(settings.url.replace('{cmd}','getFeed'),{
					count: settings.messageCount,
					offset: offset,
					since: sinceId,
					lastUpdate: chat.data('chat').lastUpdate,
					cache: $.now()
				},function(data){
					$('.loader',feed).remove();
					chat.data('chat').lastUpdate = data.lastUpdate;
					if( data === null || data.inserted.length === 0 ) {
						if( feed.children().length === 0 ) {
							feed.prepend('<p class="no-comments">'+settings.strings.noComments+'</p>');
							$('.readmore',chat).remove();
						} else if( offset > 0 ) {
							// There's no more messages to read
							$('.readmore',chat).replaceWith(settings.strings.noMoreComments);
						}
					} else {
						$('.no-comments',feed).remove();
					}
					$.each(data.inserted,function(key,message){
						chat.chat('insertMessage',message);
					});
					$.each(data.deleted,function(key,messageid){
						$('#message_'+messageid,chat).remove();
					});
					chat.chat('pinMessages',data.pinned);
					$('abbr.timeago').timeago();
				});
			});
		},
		filterFeed: function(pattern,contentSelector){
			return this.each(function(){
				var chat = $(this);
				$('.message',chat).each(function(){
					$(this).removeClass('filteredOut filteredIn');
					if( pattern ) {
						if( $(contentSelector,this).text().match(pattern) ) {
							$(this).addClass('filteredIn');
						} else {
							$(this).addClass('filteredOut');
						}
					}
				});
			});
		},
		sendMessage: function(){
			return this.each(function(){
				var chat = $(this);
				
				var message = $('.msg input.messageText',this).val().trim();
				if( message.length === 0 ) {
					return chat; // Do not send empty messages
				}
				
				var username = $('.my_username',this).text();
				var parent = $('.msg',this).closest('.message');
				var replyTo = '';
				if( parent.length > 0 ) {
					replyTo = parent.data('id');
				}
				
				$('.msg input',chat).attr('disabled','true');
				// Show loading animation
				$('.msg',chat).append('<div class="loader"></div>');
				// Send message
				$.post(settings.url.replace('{cmd}','sendMessage'),{
					message: message,
					reply_to: replyTo,
					username: username,
					time: new Date()
				},function(data,status){
					// Stop loading animation
					$('.msg .loader',chat).remove();
					if( data.error ) {
						chat.chat('showInfo',settings.strings.sendError,'error');
					} else {
						$('.msg input.messageText',chat).val('');
						$('.no-comments',$(settings.feedSelector,chat)).remove();
						if( data.message ) {
							chat.chat('insertMessage',data.message);
						}
						// Track analytics
						settings.analytics('send','event','Chat','MessageSent');
					}
					if( data.info ) {
						chat.chat('showInfo',data.info,'info');
					}
					$('.msg input',chat).removeAttr('disabled').focus();
					$('.message a.reply',chat).text(settings.strings.reply).removeClass('cancel-reply');
					$('.msg-placeholder',chat).replaceWith($('.msg',chat));
				}).error(function(){
					$('.msg .loader',chat).remove();
					chat.chat('showInfo',settings.strings.sendError,'error');
					$('.msg input',chat).removeAttr('disabled').focus();
				});
				
				return chat;
			});
		},
		pinMessages: function(pins){
			return this.each(function(){
				var chat = $(this);
				var feed = $(settings.feedSelector,this);
				var pinnedParent = $(settings.pinnedSelector,chat);
				if( pinnedParent.length === 0 ) {
					pinnedParent = feed;
				}
				
				$('.message.pinned',chat).each(function(){
					var message = $(this);
					if( !pins[message.data('id')] ) {
						message.removeClass('pinned',400,function(){
							message.data('pinned',null);
							message.detach();
							chat.chat('insertMessageElement',message,feed);
						});
					}
				});
				for( var messageid in pins ){
					var message = $('#message_'+messageid,chat);
					message.addClass('pinned',400,function(){
						message.data('pinned',pins[messageid]);
						message.detach();
						chat.chat('insertMessageElement',message,pinnedParent);
					});
				};
			});
		},
		insertMessageElement: function(newMessage,feed){
			// Clear old parent/child classes
			newMessage.removeClass('child parent');
			// Search for place to append
			var parent = null;
			// If the message is reply to existing message, append to that (if using threaded chat)
			if( settings.threaded && newMessage.data('replyto') && !newMessage.data('pinned') ) {
				var parentMessage = $('#message_'+newMessage.data('replyto'));
				// Do not show replies on pinned messages
				if( parentMessage.length > 0 && !parentMessage.data('pinned') ) {
					// Replied message is in the feed
					parent = parentMessage;
					newMessage.addClass('child');
				} else {
					// Replied message is not in the feed
					newMessage.addClass('parent');
				}
			} else {
				// Message is not a reply
				newMessage.addClass('parent');
			}
			
			var messageSelector = '.message:not(.pinned)';
			var messageTimeKey = 'timestamp';
			
			// Insert message to the right place according to timestamp
			if( parent === null ) {
				parent = feed;
				
				var firstUnpinnedMessage = [];
				
				if( newMessage.data('pinned') ) {
					messageSelector = '.message.pinned';
					messageTimeKey = 'pinned';
					firstUnpinnedMessage = $('.message:not(.pinned):first',parent);
				}
				
				var messageTime = newMessage.data(messageTimeKey);
				var nextMessage = $(messageSelector + ':first',parent);
				var nextTime = parseInt(nextMessage.data(messageTimeKey));
				
				if( settings.isQueue ) {
					// Messages in queue are in reverse order (oldest first)
					while( nextMessage.length > 0 && messageTime > nextTime ) {
						nextMessage = nextMessage.next(messageSelector);
						nextTime = parseInt(nextMessage.data(messageTimeKey));
					}
				} else {
					while( nextMessage.length > 0 && messageTime < nextTime ) {
						nextMessage = nextMessage.next(messageSelector);
						nextTime = parseInt(nextMessage.data(messageTimeKey));
					}
				}
				if( nextMessage.length > 0 ) {
					newMessage.insertBefore(nextMessage);
				} else if( firstUnpinnedMessage.length > 0 ) {
					newMessage.insertBefore(firstUnpinnedMessage);
				} else {
					parent.append(newMessage);
				}
			} else {
				// Replies are ordered oldest first
				messageTime = newMessage.data(messageTimeKey);
				var prevMessage = $(messageSelector+':last',parent);
				var prevTime = parseInt(prevMessage.data(messageTimeKey));
				while( prevMessage.length > 0 && messageTime > prevTime ) {
					prevMessage = prevMessage.prev(messageSelector);
					prevTime = parseInt(prevMessage.data(messageTimeKey));
				}
				if( prevMessage.length > 0 ) {
					newMessage.insertAfter(prevMessage);
				} else {
					parent.append(newMessage);
				}
			}
			
			feed.trigger('jquerychat.messageInserted');
		},
		changeUsername: function(){
			var chat = this;
			var dialog = $('.nickname-modal',chat);
			$('input',dialog).val($('.my_username',chat).text());
			dialog.modal('show');
		},
		registerUser: function(){
			var username = $('.my_username',this).text();
			$.post(settings.url.replace('{cmd}','registerUser'),{
				username: username,
				time: new Date()
			},function(data,status){
				$('.my_username',this).text(data.username);
				// Track analytics
				settings.analytics('send','event','Chat','NicknameChanged');
			});
		},
		showInfo: function(message,category){
			var info = this.find('.msginfo');
			var infoText = $('<span></span>').text(message).addClass(category).appendTo(info);
			infoText.fadeTo(3000,0.9,function(){
				infoText.fadeTo(1500,0,function(){
					infoText.remove();
				});
			});
		}
	};
	
	// From https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference:Global_Objects:Date
	function ISODateString(d){  
		function pad(n){return n<10 ? '0'+n : n;}  
		return d.getUTCFullYear()+'-'  
			+ pad(d.getUTCMonth()+1)+'-'  
			+ pad(d.getUTCDate())+'T'  
			+ pad(d.getUTCHours())+':'  
			+ pad(d.getUTCMinutes())+':'  
			+ pad(d.getUTCSeconds())+'Z';
	}

	$.fn.chat = function(method){
		if( methods[method] ) {
			return methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		} else if( typeof method === 'object' || !method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error('Method ' +  method + ' does not exist on jQuery.chat');
			return this;
		}
	};
})(jQuery);
