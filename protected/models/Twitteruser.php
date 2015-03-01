<?php
class Twitteruser extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Twitteruser');
	}
	
	public function tableName() {
		return '{{twitteruser}}';
	}
	
	public function relations() {
		return array(
		);
	}
}
