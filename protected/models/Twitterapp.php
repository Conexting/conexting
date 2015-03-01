<?php
class Twitterapp extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Twitterapp');
	}
	
	public function tableName() {
		return '{{twitterapp}}';
	}
	
	public function relations() {
		return array(
		);
	}
}
