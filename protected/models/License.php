<?php
class License extends DeletableActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('License');
	}
	
	public function tableName() {
		return '{{license}}';
	}
	
	public function relations() {
		return array(
			'Client' => array(self::BELONGS_TO,'Client','clientid'),
		);
	}
	
	public function rules() {
		return array(
			array('clientid','exist','className'=>'Client','allowEmpty'=>false),
			array('expires','safe'),
			
			array('clientid,expires','safe','on'=>'search'),
		);
	}
	
	public function attributeLabels() {
		return array(
			// --
		);
	}
}
