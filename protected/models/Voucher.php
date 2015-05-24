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
			array('walllength,wallremovedafter','length','max'=>32),
			array('name,code','length','max'=>128),
			array('wallsmscredit,count,countperclient','numerical','min'=>0,'max'=>9999),
			array('expirationTime','safe'),
			array('active','boolean'),
			array('name,code,walllength,wallremovedafter,expirationTime','required'),
			
			array('voucherid,code,wallength,expires,count','safe','on'=>'search'),
		);
	}
	
	public function attributeLabels() {
		return array(
			'name'=>g('Voucher name'),
			'code'=>g('Voucher code'),
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
	
	public function getWallCount($clientId=false) {
		$wallModel = Wall::model();
		$wallModel->showDeleted = true;
		$attributes = array('voucherid'=>$this->primaryKey);
		if( $clientId ) {
			$attributes['clientid'] = $clientId;
		}
		$count = $wallModel->countByAttributes($attributes);
		$wallModel->showDeleted = false;
		return $count;
	}
	
	public function check($clientId) {
		if( $this->expires < time() ) {
			throw new Exception(g('Sorry, the voucher code you have entered has been expired.'));
		}
		if( !$this->active ) {
			throw new Exception(g('Sorry, the voucher code you have entered is not currently active.'));
		}
		if( $this->count <= $this->wallCount ) {
			throw new Exception(g('Sorry, this voucher code has already been used.'));
		}
		if( $this->countperclient <= $this->getWallCount($clientId) ) {
			throw new Exception(g('Sorry, you have already used this voucher code.'));
		}
	}
}
