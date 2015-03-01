<?php
class Client extends DeletableActiveRecord {
	public $password_new;
	public $password_confirm;
	
	public static function model($className=__CLASS__) {
		return parent::model('Client');
	}
	
	public function tableName() {
		return '{{client}}';
	}
	
	/**
	 * Add sorting parameters for a search
	 * @param CSort $sort
	 * @return CSort 
	 */
	protected function searchSort($sort) {
		$sort->attributes = array(
			'WallCount'=>array(
				'asc' => '(SELECT count(w.wallid) FROM {{wall}} as w WHERE w.clientid = t.clientid AND deleted IS NULL) ASC',
				'desc' => '(SELECT count(w.wallid) FROM {{wall}} as w WHERE w.clientid = t.clientid AND deleted IS NULL) DESC',
			),
			'*'
		);
		return $sort;
	}
	
	public function relations() {
		return array(
			'Walls' => array(self::HAS_MANY,'Wall','clientid'),
			'Contact' => array(self::HAS_ONE,'Contact','clientid'),
			'WallCount' => array(self::STAT,'Wall','clientid'),
		);
	}
	
	public function rules() {
		return array(
			array('name','length','max'=>127),
			array('email','length','max'=>255),
			array('email','email'),
			array('password','safe','on'=>'login'),
			array('password_new','length','min'=>4,'max'=>255,'on'=>'change-password'),
			array('password_confirm','compare','compareAttribute'=>'password_new','on'=>'change-password','message'=>g('Re-type the new password at {attribute}')),
			array('name,email','unique','on'=>array('signup','insert','update','edit')),
			array('name,email','required'),
			array('email,name','exists','on'=>array('login')),
			array('email','unsafe','on'=>array('edit')),
			array('clientid,name,email','safe','on'=>'search'),
		);
	}
	
	public function attributeLabels() {
		return array(
			'name'=>g('Name'),
			'email'=>g('Email'),
			'password'=>g('Password'),
			'password_new'=>g('New password'),
			'password_confirm'=>g('Confirm password'),
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
	public function getAccessTime() {
		if( $this->accessed ) {
			return date('j.n.Y H:i',$this->accessed);
		} else {
			return null;
		}
	}
	
	public function getStr() {
		return $this->name.' ('.$this->email.')';
	}
	
	public function sendMail($view,$subject,$params=array()) {
		$mail = new MailMessage;
		$mail->subject = $subject;
		$mail->view = $view;
		$mail->setBody(array_merge(array('client'=>$this),$params));
		$mail->setTo(array($this->email));
		$mail->from = Yii::app()->params['fromEmail'];
		return Yii::app()->mail->send($mail);
	}
	
	public function validatePassword($password) {
		if( is_null($this->password) ) {
			return is_null($password) || $password === '';
		} else {
			return $this->password === self::getHash($password);
		}
	}
	
	public static function getHash($password) {
		return sha1($password.'ssgoi213095hsdf+0k1');
	}
	
	public static function createPassword($length=6) {
		$password = '';
    srand((float) microtime() * 10000000);
		for( $i = 0; $i < $length; $i++ ) {
			$password .= chr(rand(97,122));
		}
    return $password;
	}
	
	public function getLoginUrlHash() {
		return sha1($this->primaryKey.':'.$this->email.':'.$this->password);
	}
	
	public static function getClientFromLoginHash($hash) {
		return Client::model()->find(
			"SHA1(CONCAT(clientid,':',email,':',IFNULL(password,'')))=:hash",
			array(':hash'=>$hash)
		);
	}
}
