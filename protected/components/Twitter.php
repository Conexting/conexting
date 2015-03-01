<?php
Yii::import('ext.yiitwitteroauth.TwitterOAuth');

class Twitter extends CApplicationComponent {
	const URL_SEARCH = 'https://api.twitter.com/1.1/search/tweets.json';
	const URL_UPDATE = 'https://api.twitter.com/1.1/statuses/update.json';
	const SEND_RETRIES = 2;
	
	public $appid;	
	public $appidWithUser;
	public $cachedir;
	public $minRefreshRate = 20;
	public $minSharedRefreshRate = 60;
	public $refreshBuffer = 40;
	public $twitterLimitWindowLength = 900;
	
	/**
	 * @var Twitterapp
	 */
	protected $app;
	/**
	 * @var Twitteruser
	 */
	protected $user = null;
	protected $insertMessageCmd;
	protected $insertTweetCmd;
	protected $insertMediaCmd;
	
	public function init() {
		parent::init();
		$this->cachedir = Yii::app()->basePath.'/twitter/cache/';
		
		$this->app = Twitterapp::model()->findByPk($this->appid);
		
		$this->insertMessageCmd = Yii::app()->db->createCommand("INSERT INTO {{message}}(messageid,wallid,tweetid,timestamp,replyto,username,text) VALUES(:messageid,:wallid,:tweetid,:timestamp,:replyto,:username,:text)");
		$this->insertTweetCmd = Yii::app()->db->createCommand("INSERT INTO {{tweet}}(tweetid,user_id,user_screen,user_name,user_image,replyto,retweetof) VALUES(:tweetid,:user_id,:user_screen,:user_name,:user_image,:replyto,:retweetof)");
		$this->insertMediaCmd = Yii::app()->db->createCommand("INSERT INTO {{tweetmedia}}(tweetmediaid,tweetid,media_url,display_url,twitter_url,sizes) VALUES(:tweetmediaid,:tweetid,:media_url,:display_url,:twitter_url,:sizes)");
	}
	
	public function setApp($app) {
		if( is_int($app) ) {
			$app = Twitterapp::model()->findByPk($app);
		}
		
		$this->app = $app;
	}
	
	public function getAppUserId() {
		if( !is_null($this->app) ) {
			return $this->app->user_id;
		}
	}
	
	public function setUser($user) {
		if( !is_null($user) ) {
			$this->setApp($this->appidWithUser);
		} else {
			$this->setApp($this->appid);
		}
		$this->user = $user;
	}
	
	public function getRefreshRate($authUser=null) {
		if( is_null($authUser) ) {
			$authUser = $this->getAuthUser();
		}
		if( is_null($authUser->limit_search) ) {
			return false;
		}
		$timeLeft = $authUser->limit_reset - time();
		if( $timeLeft < 0 ) {
			$timeLeft = $this->twitterLimitWindowLength;
		}

		if( is_null($this->user) ) {
			// Use shared twitter account
			$activeWallCount = Wall::model()->count(
				'enabletwitter=TRUE AND TIMESTAMPDIFF(SECOND,twitterrefreshed,NOW()) < :window',array(
					':window'=>$this->twitterLimitWindowLength
				)
			);
			$calculatedRate = $timeLeft / ($authUser->limit_search_remaining - $this->refreshBuffer) * $activeWallCount;
			$seconds = max(array($calculatedRate,$this->minSharedRefreshRate));
		} else {
			// Use user-specified twitter account
			$calculatedRate = $timeLeft / ($authUser->limit_search_remaining - $this->refreshBuffer);
			$seconds = max(array($calculatedRate,$this->minRefreshRate));
		}
		
		return $seconds;
	}
	
	public function send($message, $wall, $retries=self::SEND_RETRIES) {
		if( !preg_match('/\B#'.$wall->hashtag.'\b/i',$message->text) ) {
			$status = $message->text.' #'.$wall->hashtag;
		} else {
			$status = $message->text;
		}
		if( $message->username ) {
			$status = $message->username.': '.$status;
		}
		$replyTo = null;
		if( $message->ReplyTo && $message->ReplyTo->Tweet ) {
			$replyTo = $message->ReplyTo->Tweet->tweetid;
			$replyToUser = $message->ReplyTo->Tweet->user_screen;
			$status = '@'.$replyToUser.' '.$status;
		}
		// Tweets can be max 140 characters
		if( mb_strlen($status) > 140 ) {
			$status = mb_strcut($status,0,140);
		}
		$twitter = $this->getTwitter();
		$response = $twitter->post('statuses/update',array(
			'status'=>$status,
			'in_reply_to_status_id'=>$replyTo
		));
		if( $twitter->http_code == '200' ) {
			$this->saveMessage($response,$wall,$message);
		} else {
			$this->TwError($twitter,$response,'Error sending message to Twitter');
			if( $twitter->http_code == '403' && $response['errors'][0]['code'] == '187' ) {
				// Duplicate message, do not retry
				return;
			}
			// Retry
			if( $retries > 0 ) {
				$this->send($message,$wall,$retries-1);
			}
		}
	}
	
	public function delete($message) {
		if( $message->Tweet ) {
			if( $this->getAuthUserId() == $message->Tweet->user_id ) {
				$twitter = $this->getTwitter();
				$response = $twitter->post('statuses/destroy/'.$message->Tweet->tweetid);
				if( $twitter->http_code == '200' ) {
					return $message->Tweet->delete();
				} elseif( $twitter->http_code == '404' ) {
					$this->TwError($twitter,$response,'Message to be deleted was not found.',CLogger::LEVEL_INFO);
				} else {
					$this->TwError($twitter,$response,'Error deleting message from Twitter');
					return false;
				}
			}
		}
	}

	/**
	 * @param Wall $wall 
	 */
	public function refreshFeed($wall) {
		// Check if feed should be refreshed from twitter
		$cachefile = $this->cachedir."{$wall->hashtag}.json";
		$authUser = $this->getAuthUser();
		if( !file_exists($cachefile) OR (time()-filemtime($cachefile)) > $this->getRefreshRate($authUser) ) {
			$params = array(
				'count'=>'100',
				'include_entities'=>'true',
				'q'=>'#'.$wall->hashtag
			);
			if( file_exists($cachefile) ) {
				$cache = json_decode(file_get_contents($cachefile));
				$params['since_id'] = $cache->max_id_str;
			}
			// Fetch the feed from twitter and rebuild cache
			$twitter = $this->getTwitter();
			$response = $twitter->get('search/tweets',$params);
			if( $twitter->http_code == '200' ) {
				// Update twitter limit usage
				$authUser->limit_search = $twitter->http_headers['x-rate-limit-limit'];
				$authUser->limit_search_remaining = $twitter->http_headers['x-rate-limit-remaining'];
				$authUser->limit_reset = $twitter->http_headers['x-rate-limit-reset'];
				$authUser->limit_refreshed = $twitter->http_headers['date'];
				
				$authUser->save();
				// Save response to cache
				file_put_contents($cachefile,json_encode($response),LOCK_EX);
				// Save fetched statuses to database
				$this->saveMessages($response['statuses'],$wall);
				$wall->saveAttributes(array(
					'twitterrefreshed'=>new CDbExpression('NOW()')
				));
				$wall->refresh();
			} else {
				$this->TwError($twitter,$response,'Error fetching messages from Twitter',CLogger::LEVEL_WARNING);
			}
		}
	}
	
	public function requestToken($callback) {
		$twitter = new TwitterOAuth($this->app->oauth_consumer_key,$this->app->oauth_consumer_secret);
		$credentials = $twitter->getRequestToken($callback);
		Yii::app()->session['tw_oauth'] = $credentials;
		$redirectUrl = $twitter->getAuthorizeURL($credentials).'&force_login=true';
		return $redirectUrl;
	}
	
	public function accessToken($verifier) {
		if( !isset(Yii::app()->session['tw_oauth']) ) {
			return false;
		}
		$twitter = new TwitterOAuth(
			$this->app->oauth_consumer_key,
			$this->app->oauth_consumer_secret,
			Yii::app()->session['tw_oauth']['oauth_token'],
			Yii::app()->session['tw_oauth']['oauth_token_secret']
		);
		$credentials = $twitter->getAccessToken($verifier);
		// Create or update twitter user credentials
		$twUser = Twitteruser::model()->findByPk($credentials['user_id']);
		if( is_null($twUser) ) {
			$twUser = new Twitteruser;
			$twUser->userid = $credentials['user_id'];
		}
		$twUser->screen_name = $credentials['screen_name'];
		$twUser->oauth_token = $credentials['oauth_token'];
		$twUser->oauth_token_secret = $credentials['oauth_token_secret'];
		$twUser->trySave();
		return $twUser;
	}
	
	protected function saveMessages($tweets, $wall) {
		foreach( $tweets as $tweetData ) {
			$this->saveMessage($tweetData,$wall);
		}
	}
	
	/**
	 * @var array $tweetData
	 * @var Wall $wall
	 */
	protected function saveMessage($tweetData,$wall,$message=null) {
		$transaction = Yii::app()->db->beginTransaction();
		// Check if tweet already exists
		$tweet = Tweet::model()->findByPk($tweetData['id_str']);
		if( is_null($tweet) ) {
			// Save tweet to db
			$tweetParams = array(
				':tweetid'=>$tweetData['id_str'],
				':user_id'=>$tweetData['user']['id_str'],
				':user_screen'=>$tweetData['user']['screen_name'],
				':user_name'=>$tweetData['user']['name'],
				':user_image'=>$tweetData['user']['profile_image_url'],
				':replyto'=>null,
				':retweetof'=>null,
			);
			if( array_key_exists('in_reply_to_status_id_str',$tweetData) ) {
				$tweetParams[':replyto'] = $tweetData['in_reply_to_status_id_str'];
			}
			if( array_key_exists('retweeted_status',$tweetData) ) {
				$tweetParams[':retweetof'] = $tweetData['retweeted_status']['id_str'];
			}
			$this->insertTweetCmd->execute($tweetParams);
			$tweet = Tweet::model()->findByPk($tweetData['id_str']);
			// Save tweet media to db
			if( array_key_exists('entities',$tweetData) ) {
				if( array_key_exists('media',$tweetData['entities']) ) {
					foreach( $tweetData['entities']['media'] as $tweetMedia ) {
						$media = Tweetmedia::model()->findByPk($tweetMedia['id_str']);
						if( is_null($media) ) {
							$this->insertMediaCmd->execute(array(
								':tweetmediaid'=>$tweetMedia['id_str'],
								':tweetid'=>$tweet->tweetid,
								':media_url'=>$tweetMedia['media_url'],
								':display_url'=>$tweetMedia['display_url'],
								':twitter_url'=>$tweetMedia['expanded_url'],
								':sizes'=>implode(',',array_keys($tweetMedia['sizes']))
							));
						}
					}
				}
			}
		}
		if( is_null($message) ) {
			// Check if message already exists
			$messageModel = Message::model();
			$messageModel->showDeleted = true;
			$message = $messageModel->findByAttributes(array(
				'tweetid'=>$tweet->tweetid,
				'wallid'=>$wall->wallid
			));
			$messageModel->showDeleted = false;
		}
		if( is_null($message) ) {
			// Save new message
			$replyTo = null;
			if( $tweet->ReplyTo && $tweet->ReplyTo->Message ) {
				$replyTo = $tweet->ReplyTo->Message->messageid;
			}
			
			// If tweet is prepended by hashtag, remove it from message
			$text = preg_replace('/^\s*'.$wall->twitter.'\s*/','',$tweetData['text']);
			
			$this->insertMessageCmd->execute(array(
				':messageid'=>null,
				':wallid'=>$wall->wallid,
				':tweetid'=>$tweet->tweetid,
				':timestamp'=>date('y-m-d H:i:s',strtotime($tweetData['created_at'])),
				':replyto'=>$replyTo,
				':username'=>$tweet->user_screen,
				':text'=>$text
			));
		} else {
			// Link message to the tweet
			$message->saveAttributes(array('tweetid'=>$tweet->tweetid));
		}
		$transaction->commit();
	}
	
	protected function getAuthUser() {
		if( is_null($this->user) ) {
			return $this->app;
		} else {
			return $this->user;
		}
	}
	
	protected function getAuthUserId() {
		if( is_null($this->user) ) {
			return $this->app->user_id;
		} else {
			return $this->user->userid;
		}
	}
	
	/**
	 * Create Twitter API connection
	 * @return \TwitterOAuth
	 */
	public function getTwitter() {
		$authUser = $this->getAuthUser();
		return new TwitterOAuth(
			$this->app->oauth_consumer_key,
			$this->app->oauth_consumer_secret,
			$authUser->oauth_token,
			$authUser->oauth_token_secret
		);
	}
	
	/**
	 * Log a Twitter error message to application log
	 * @param \TwitterOAuth $twitter
	 * @param string $msg
	 */
	protected function TwError($twitter,$response,$msg='',$level=CLogger::LEVEL_ERROR) {
		$msg .= PHP_EOL.'Url: '.$twitter->url;
		$msg .= PHP_EOL.'Status code: '.$twitter->http_code;
		$msg .= PHP_EOL.print_r($response,true);
		Yii::log($msg,$level);
	}
}
