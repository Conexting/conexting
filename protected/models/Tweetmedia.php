<?php
class Tweetmedia extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return '{{tweetmedia}}';
	}
	
	public function relations() {
		return array(
			'Tweet' => array(self::BELONGS_TO,'Tweet','tweetid')
		);
	}
	
	public function rules() {
		return array(
		);
	}
}
