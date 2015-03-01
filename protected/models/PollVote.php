<?php
class PollVote extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return '{{pollvote}}';
	}
	
	public function relations() {
		return array(
			'PollChoice' => array(self::BELONGS_TO,'PollChoice','pollid,choice'),
		);
	}
	
	public function rules() {
		return array(
		);
	}
	
	public function attributeLabels() {
		return array(
		);
	}
}
