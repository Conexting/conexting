<?php
class WallVariable extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('WallVariable');
	}
	
	public function tableName() {
		return '{{wallvariable}}';
	}
	
	public function relations() {
		return array(
			'Wall' => array(self::BELONGS_TO,'Wall','wallid'),
		);
	}
}
