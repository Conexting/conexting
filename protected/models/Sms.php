<?php
class Sms extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Sms');
	}
	
	public function tableName() {
		return '{{sms}}';
	}
	
	public function relations() {
		return array(
			'Message' => array(self::HAS_MANY,'Message','smsid'),
		);
	}
	
	public function rules() {
		return array(
		);
	}
}
