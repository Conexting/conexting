<?php
class Contact extends DeletableActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Contact');
	}
	
	public function tableName() {
		return '{{contact}}';
	}
	
	public function relations() {
		return array(
			'Client' => array(self::BELONGS_TO,'Client','clientid'),
			'Payments' => array(self::HAS_MANY,'Payment','contactid'),
		);
	}
	
	public function rules() {
		return array(
			array('forname,surname','length','max'=>127),
			array('street','length','max'=>255),
			array('zipcode,zip,phone,mobile','length','max'=>32),
			array('organization','length','max'=>64),
			array('country','in','range'=>array_keys(Yii::app()->params['countries'])),

			array('forname,surname,street,zipcode,zip,country','required'),
		);
	}
	
	public function attributeLabels() {
		return array(
			'forname'=>g('Forname'),
			'surname'=>g('Surname'),
			'street'=>g('Street'),
			'zipcode'=>g('Zip code'),
			'zip'=>g('Zip'),
			'phone'=>g('Phone'),
			'mobile'=>g('Mobile phone'),
			'organization'=>g('Organization'),
			'country'=>g('Country'),
		);
	}
	
	public function getCreationTime() {
		return date('j.n.Y H:i',$this->created);
	}
	public function getModificationTime() {
		if( $this->modified ) {
			return date('j.n.Y H:i',$this->modified);
		} else {
			return null;
		}
	}
	
	public function getCountryName() {
		return Yii::app()->params["countries"][$this->country];
	}
	
	public function getName() {
		return $this->forname.' '.$this->surname;
	}
	
	public function getPaymentSum() {
		$sum = 0;
		foreach( $this->Payments as $payment ) {
			$sum += $payment->total;
		}
		return $sum;
	}
}
