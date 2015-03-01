<?php
class Payment extends DeletableActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Payment');
	}
	
	public function tableName() {
		return '{{payment}}';
	}
	
	public function relations() {
		return array(
			'Contact' => array(self::BELONGS_TO,'Contact','contactid'),
			'Wall' => array(self::BELONGS_TO,'Wall','wallid'),
			// Not yet implemented: 'License' => array(self::BELONGS_TO,'License','licenseid')
		);
	}
	
	public function rules() {
		return array(
			array('title,code','length','max'=>63),
			array('amount','numerical','integerOnly'=>true),
			array('title,code,amount','required'),
			array('confirmed,paid','validateDate'),
			array('pending','boolean'),
			array('price','numerical','min'=>-99999.99,'max'=>99999.99),
			array('vat,discount','numerical','min'=>-99.99,'max'=>99.99),
			
			array('paymentid,created,paid,title,code,amount','safe','on'=>'search'),
		);
	}
	
	public function attributeLabels() {
		return array(
			'title'=>g('Title'),
			'price'=>g('Price'),
			'paid'=>g('Paid'),
			'created'=>g('Created'),
			'priceDisplay'=>g('Price'),
		);
	}
	
	public function getCreationTime() {
		return date('j.n.Y H:i',$this->created);
	}
	public function getConfirmedTime() {
		if( $this->confirmed ) {
			return date('j.n.Y H:i',$this->confirmed);
		} else {
			return null;
		}
	}
	public function getPaidTime() {
		if( $this->paid ) {
			return date('j.n.Y H:i',$this->paid);
		} else {
			return null;
		}
	}
	
	public function getNextStepUrl() {
		if( is_null($this->confirmed) ) {
			$route = 'store/pay';
		} else if( is_null($this->paid) ) {
			if( $this->pending ) {
				$route = 'store/pending';
			} else {
				$route = 'store/pay';
			}
		} else {
			$route = 'store/payments';
		}
		return Yii::app()->createAbsoluteUrl($route,array('id'=>$this->primaryKey));
	}
	
	public function getPriceDisplay() {
		return $this->price.' €';
	}
}
