<?php
class Tweet extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Tweet');
	}
	
	public function tableName() {
		return '{{tweet}}';
	}
	
	public function relations() {
		return array(
			'Message' => array(self::HAS_MANY,'Message','tweetid'),
			'Media' => array(self::HAS_MANY,'Tweetmedia','tweetid'),
			'Replies' => array(self::HAS_MANY,'Tweet','replyto'),
			'ReplyTo' => array(self::BELONGS_TO,'Tweet','replyto'),
			'RetweetOf' => array(self::BELONGS_TO,'Tweet','retweetof'),
		);
	}
}
