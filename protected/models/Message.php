<?php
class Message extends DeletableActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Message');
	}
	
	public function tableName() {
		return '{{message}}';
	}
	
	public function relations() {
		return array(
			'Wall' => array(self::BELONGS_TO,'Wall','wallid'),
			'Replies' => array(self::HAS_MANY,'Message','replyto'),
			'ReplyTo' => array(self::BELONGS_TO,'Message','replyto'),
			'Tweet' => array(self::BELONGS_TO,'Tweet','tweetid'),
			'Sms' => array(self::BELONGS_TO,'Sms','messageid'),
		);
	}
	
	public function rules() {
		return array(
		);
	}
	
	public function getIsotime() {
		return date('c',$this->timestamp);
	}
	
	public function toArray($question=null) {
		$msgData = array(
			'messageid'=>$this->messageid,
			'text'=>$this->text,
			'username'=>$this->username,
			'replyto'=>$this->replyto,
			'timestamp'=>$this->timestamp,
			'approved'=>$this->approved,
			'deleted'=>$this->deleted,
			'pinned'=>$this->pinned,
			'adminmessage'=>$this->adminmessage ? true : false,
		);

		if( !is_null($question) && $question !== true ) {
			$msgData['text'] = preg_replace($question->getReplacePattern(),'',$msgData['text'],1);
		}

		$images = array();
		if( $this->Tweet ) {
			if( !$this->isAnonymous ) {
				$msgData['twitter_username'] = $this->Tweet->user_screen;
			}
			$msgData['userimage'] = $this->Tweet->user_image;
			foreach( $this->Tweet->Media as $media ) {
				$images[] = array(
					'id'=>$media->tweetmediaid,
					'media_url'=>$media->media_url,
					'display_url'=>$media->display_url,
					'twitter_url'=>$media->twitter_url,
					'sizes'=>explode(',',$media->sizes)
				);
			}
		}
		$msgData['images'] = $images;
		
		return $msgData;
	}
	
	public function getIsAnonymous() {
		if( $this->Tweet ) {
			if( $this->Wall->TwitterUser ) {
				return $this->Tweet->user_id == $this->Wall->TwitterUser->userid;
			} else {
				return $this->Tweet->user_id == Yii::app()->twitter->appUserId;
			}
		} else {
			return true;
		}
	}
	
	public function getIsPinned() {
		return !is_null($this->pinned);
	}
	
	public function pin() {
		$this->saveAttributes(array('pinned'=>new CDbExpression('NOW()')));
	}
	
	public function unpin() {
		$this->saveAttributes(array('pinned'=>null));
	}
	
	public function approve() {
		$this->saveAttributes(array('approved'=>new CDbExpression('NOW()')));
	}
}
