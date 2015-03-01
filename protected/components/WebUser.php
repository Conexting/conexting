<?php
class WebUser extends CWebUser {
	private $_client = null;
	private $_nickname = null;

	public function getClient() {
		if( !$this->isGuest ) {
			if( is_null($this->_client) ) {
				$this->_client = Client::model()->findByPk($this->getId());
				if( !is_null($this->_client) ) {
					$this->_client->saveAttributes(array('accessed'=>new CDbExpression('NOW()')));
				}
			}
		}
		return $this->_client;
	}
	
	public function setNickname($value) {
		// Nickname is stored for 7 days in client cookie
		$this->_nickname = $value;
		setcookie('nickname',$value,time()+60*60*24*7,'/');
	}
	
	public function getNickname() {
		if( isset($_COOKIE['nickname']) ) {
			$this->_nickname = $_COOKIE['nickname'];
		}
		if( is_null($this->_nickname) ) {
			return g('Guest');
		}
		return $this->_nickname;
	}
	
	public function getSenderHash() {
		if( isset($_COOKIE['cnxsender']) ) {
			$sender = $_COOKIE['cnxsender'];
		} else {
			if( !$this->isGuest ) {
				$sender = sha1('cnxsender_client_'.$this->getId());
			} else {
				$sender = sha1('cnxsender_guest_'.Yii::app()->session->sessionID);
				$_COOKIE['cnxsender'] = $sender;
			}
		}
		return $sender;
	}
	
	public function getKeywordChoices($getAll=false) {
		$choices = Yii::app()->params['smsKeywords'];
		if( $getAll ) {
			foreach( Yii::app()->params['smsKeywordsForClients'] as $key => $label ) {
				$choices[$key] = $label;
			}
		} else if( !is_null($this->client) ) {
			if( array_key_exists($this->client->name,Yii::app()->params['smsKeywordsForClients']) ) {
				$choices = $choices + Yii::app()->params['smsKeywordsForClients'][$this->client->name];
			}
		}
		return $choices;
	}
}
