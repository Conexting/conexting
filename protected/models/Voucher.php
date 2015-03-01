<?php
class Voucher extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Voucher');
	}
	
	public function tableName() {
		return '{{voucher}}';
	}
	
	public function relations() {
		return array(
			'Walls' => array(self::HAS_MANY,'Wall','voucherid'),
		);
	}
	
	public function rules() {
		return array(
			array('walllength','in','range'=>array_keys(Yii::app()->params['store']['walls'])),
			array('code','length','max'=>128),
			array('count','numerical','min'=>0,'max'=>9999),
			array('expires','safe'),
			array('active','boolean'),
			
			array('voucherid,code,wallength,expires,count','safe','on'=>'search'),
		);
	}
	
	public function attributeLabels() {
		return array(
			// --
		);
	}
	
	public function getExpirationDate() {
		return $this->getExpirationTime('j.n.Y');
	}
	
	public function getExpirationTime($format='j.n.Y H:i') {
		if( $this->expires ) {
			return date($format,$this->expires);
		} else {
			return null;
		}
	}
	
	public function setExpirationTime($value) {
		$date = strtotime($value);
		if( $date ) {
			$this->expires = $date;
		}
	}
	
	public function check() {
		if( $this->expires < time() ) {
			throw new Exception(g('Sorry, the voucher code you have entered has been expired.'));
		}
		if( !$this->active ) {
			throw new Exception(g('Sorry, the voucher code you have entered is no longer active.'));
		}
		$wallModel = Wall::model();
		$wallModel->showDeleted = true;
		if( $this->count >= $wallModel->findAllByAttributes(array('voucherid'=>$this->primaryKey)) ) {
			throw new Exception(g('Sorry, this voucher code has already been used.'));
		}
		$wallModel->showDeleted = false;
	}
}
