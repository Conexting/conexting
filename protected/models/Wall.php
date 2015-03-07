<?php
class Wall extends DeletableActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Wall');
	}
	
	protected $_thememodel;

	public function tableName() {
		return '{{wall}}';
	}
	
	public function relations() {
		return array(
			'Client' => array(self::BELONGS_TO,'Client','clientid'),
			'Messages' => array(self::HAS_MANY,'Message','wallid'),
			'Polls' => array(self::HAS_MANY,'Poll','wallid','order'=>'position ASC'),
			'Questions' => array(self::HAS_MANY,'Question','wallid','order'=>'position ASC'),
			'Variables' => array(self::HAS_MANY,'WallVariable','wallid','index'=>'name'),
			'TwitterUser' => array(self::BELONGS_TO,'Twitteruser','twitteruser'),
		);
	}
	
	public function rules() {
		return array(
			// Name restrictions
			array('name','length','max'=>63,'encoding'=>'UTF-8'),
			array('name','match','pattern'=>'/^[\w\-\+\._]+$/i','message'=>g('Do not use special characters or spaces in {attribute}')),
			array('name','match','pattern'=>'/^('.implode('|',Yii::app()->params['illegalWallNames']).')$/i','not'=>true,'message'=>g('Illegal name, please choose another name')),
			
			array('title','length','max'=>255),
			//array('theme','length','max'=>31),
			array('description','safe'),
			array('password,adminpassword,hashtag,streamprovider,streamid','length','max'=>127),
			array('hashtag','length','max'=>127),
			array('name','required'),
			array('name','unique'),
			array('enabletwitter,enablesms,enablestream,index,premoderated,threaded','boolean'),
			
			array('hashtag','validateIf','compare'=>'enabletwitter','validator'=>'required'),
			array('hashtag','validateIf','compare'=>'enabletwitter','validator'=>'unique'),
			
			array('smskeyword','validateIf','compare'=>'enablesms','validator'=>'required'),
			array('smskeyword','in','range'=>array_keys(Yii::app()->user->getKeywordChoices())),
			array('smsprefix','length','max'=>31),
			array('smsprefix','match','pattern'=>'/^[\w\-\.]*$/i','message'=>g('Do not use special characters or spaces in {attribute}')),
			array('smskeyword','checkUnprefixedWalls'),
			array('smsprefix','requireIfSharedKeyword'),
			array('smsprefix','uniqueWith','with'=>'smskeyword','caseSensitive'=>false,'message'=>g('{attribute} "{value}" is already reserved for this keyword, please choose another prefix or keyword')),
			
			array('streamprovider,streamid','validateIf','compare'=>'enablestream','validator'=>'required'),
			
			array('name','validateIf','compare'=>'isPublished','validator'=>'unsafe','setInvalidToNull'=>false), // Name cannot be changed after wall has been published
			
			// Only for admin
			array('premium,paid,hidden','boolean','on'=>'admin'),
			array('expirationTime,dyingTime','safe','on'=>'admin'),
		);
	}
	
	public function uniqueWith($attribute,$params) {
		$params['criteria'] = array(
			'condition'=>'UPPER('.$params['with'].')=UPPER(:value)',
			'params'=>array(':value'=>$this->{$params['with']})
		);
		unset($params['with']);
		$validator = CValidator::createValidator('unique',$this,$attribute,$params);
		return $validator->validate($this);
	}
	
	public function requireIfSharedKeyword($attribute,$params) {
		if( $this->enablesms ) {
			if( in_array($this->smskeyword,Yii::app()->params['smsKeywords']) ) {
				$validator = CValidator::createValidator('required',$this,$attribute,$params);
				return $validator->validate($this);
			}
		}
	}
	
	public function checkUnprefixedWalls($attribute,$params) {
		if( $this->enablesms ) {
			if( $this->smsprefix ) {
				$params['criteria'] = array('condition'=>'smsprefix IS NULL');
				$params['message'] = g('{attribute} "{value}" is already reserved, please choose another keyword.');
			} else {
				$params['message'] = g('{attribute} "{value}" is already reserved, please choose another keyword or use a prefix.');
			}
			$validator = CValidator::createValidator('unique',$this,$attribute,$params);
			return $validator->validate($this);
		}
	}
	
	public function afterValidate() {
		$this->checkPremiumFeatures();
		parent::afterValidate();
	}
	
	public function checkPremiumFeatures() {
		if( $this->isPublished && !$this->premium ){
			if( $this->enabletwitter && !$this->twitteruser ) {
				$this->addError('TwitterUser',g('You must sign in with Twitter account to use Twitter connection or upgrade the wall to Premium'));
				return false;
			}
			if( $this->premoderated ) {
				$this->addError('premoderated',g('{name} is a premium feature.',array('{name}'=>$this->getAttributeLabel('premoderated'))).' '.g('Please upgrade to Premium-wall to use this feature.'));
			}
			if( $this->enablesms ) {
				$this->addError('enablesms',g('{name} is a premium feature.',array('{name}'=>$this->getAttributeLabel('enablesms'))).' '.g('Please upgrade to Premium-wall to use this feature.'));
			}
		}
	}
	
	public function getHasPremiumFeatures() {
		return $this->premoderated
			|| $this->enablesms
			|| ($this->enabletwitter && !$this->twitteruser);
	}
	
	public function getShowPremiumFeatures() {
		return $this->premium
			|| (!$this->isPublished && $this->hasPremiumFeatures);
	}
	
	public function attributeLabels() {
		return array(
			'name'=>g('Name'),
			'title'=>g('Title'),
			'displayTitle'=>g('Wall title'),
			'theme'=>g('Theme'),
			'password'=>g('Visitor password'),
			'adminpassword'=>g('Wall admin password'),
			'published'=>g('Wall published time'),
			'isPublished'=>g('Wall is published and open to visitors'),
			'enabletwitter'=>g('Enable Twitter connection'),
			'enablesms'=>g('Enable messaging and voting via SMS (mobile text messages)'),
			'enablestream'=>g('Enable webcast stream on the conversation web site'),
			'streamprovider'=>g('Stream service provider'),
			'streamid'=>g('Stream identifier / username'),
			'smskeyword'=>g('Keyword'),
			'smsprefix'=>g('Prefix'),
			'hashtag'=>g('Hashtag'),
			'index'=>g('Allow indexing'),
			'created'=>g('Creation time'),
			'creationTime'=>g('Creation time'),
			'expires'=>g('Use time expires'),
			'dies'=>g('Visible until'),
			'expirationTime'=>g('Use time expires'),
			'TwitterUser'=>g('Twitter account'),
			'premoderated'=>g('Pre-moderate messages'),
			'threaded'=>g('Threaded convesation'),
		);
	}
	
	public function getCreationTime($format='j.n.Y H:i') {
		return date($format,$this->created);
	}
	public function getModificationTime($format='j.n.Y H:i') {
		if( $this->modified ) {
			return date($format,$this->modified);
		} else {
			return null;
		}
	}
	public function getExpirationTime($format='j.n.Y H:i') {
		if( $this->expires ) {
			return date($format,$this->expires);
		} else {
			return null;
		}
	}
	public function getDyingTime($format='j.n.Y H:i') {
		if( $this->dies ) {
			return date($format,$this->dies);
		} else {
			return null;
		}
	}
	public function getCreationDate() {
		return $this->getCreationTime('j.n.Y');
	}
	public function getModificationDate() {
		return $this->getModificationTime('j.n.Y');
	}
	public function getExpirationDate() {
		return $this->getExpirationTime('j.n.Y');
	}
	public function getDyingDate() {
		return $this->getDyingTime('j.n.Y');
	}
	public function setExpirationTime($value) {
		$date = strtotime($value);
		if( $date ) {
			$this->expires = $date;
		}
	}
	public function setDyingTime($value) {
		$date = strtotime($value);
		if( $date ) {
			$this->dies = $date;
		}
	}
	
	public function getSms() {
		if( $this->enablesms ) {
			$sms = $this->smskeyword;
			if( $this->smsprefix ) {
				$sms .= ' '.$this->smsprefix;
			}
			return $sms;
		}
		return null;
	}
	public function getTwitter() {
		if( $this->enabletwitter ) {
			return '#'.$this->hashtag;
		}
		return null;
	}
	
	public function getSmsCurrentNumber() {
		if( !is_null($this->smsnumber) ) {
			return $this->smsnumber;
		} else {
			return Yii::app()->params['defaultSmsNumber'];
		}
	}
	
	public function getSmsDefaultQuestion() {
		if( $this->enablesms ) {
			foreach( $this->Questions as $question ) {
				if( $question->smsdefault ) {
					return $question;
				}
			}
		}
		return null;
	}
	
	public function getUrl() {
		return Yii::app()->createAbsoluteUrl('wall/index',array('wall'=>$this->name,'language'=>null));
	}
	
	public function getIsExpired() {
		return $this->expires != null && $this->expires < time();
	}
	
	public function getRemovedInDays() {
		if( is_null($this->deleted) ) {
			$dateDies = new DateTime();
			$dateDies->setTimestamp($this->dies);
			$now = new DateTime();
			return $now->diff($dateDies)->days;
		} else {
			return false;
		}
	}
	
	public function getIsPublished() {
		return !is_null($this->published);
	}
	
	public function getThemeModel() {
		if( is_null($this->_thememodel) ) {
			$this->_thememodel = Theme::create($this);
		}
		return $this->_thememodel;
	}
	
	public function getVar($name) {
		if( !array_key_exists($name,$this->Variables) ) {
			$var = new WallVariable;
			$var->wallid = $this->primaryKey;
			$var->name = $name;
			return $var;
		} else {
			return $this->Variables[$name];
		}
	}
	
	public function getVars() {
		$vars = array();
		foreach( $this->Variables as $var ) {
			$vars[$var->name] = $var->value;
		}
		return $vars;
	}
	
	public function getDisplayTitle() {
		if( $this->title ) {
			return $this->title;
		} else {
			return $this->name;
		}
	}
	
	public function publish($length, $removedAfter, $premium=false, $voucherid=null, $smscredit=0) {
		if( !$this->isPublished ) {
			$extended = false;
			$start = 'NOW()';
		} else {
			$extended = true;
			$start = 'expires';
		}
		
		$attributes = array(
			'expires'=>Wall::intervalDateExpression($length,$start),
			'dies'=>Wall::intervalDateExpression($removedAfter,$start),
			'voucherid'=>$voucherid,
			'premium'=>$premium,
			'smscredit'=>$smscredit
		);
		
		if( !$extended ) {
			$attributes['published'] = new CDbExpression('NOW()');
		}
		
		$this->saveAttributes($attributes);
		
		$this->refresh();
		if( $extended ) {
			$msg = g('Your Conexting wall use time has been extended until {datetime}',array('{datetime}'=>date('j.n.Y H:i',$this->expires)));
		} else {
			$msg = g('Your Conexting wall has now been published!').' '.g('You can share this url and strart using the wall.');
		}
		f($msg,'success');
	}
}
