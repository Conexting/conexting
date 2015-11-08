<?php
class Poll extends DeletableActiveRecord {
	public $clearVotes = false;
	
	public static function model($className=__CLASS__) {
		return parent::model('Poll');
	}
	
	public function tableName() {
		return '{{poll}}';
	}
	
	public function relations() {
		return array(
			'Wall' => array(self::BELONGS_TO,'Wall','wallid'),
			'Choices' => array(self::HAS_MANY,'PollChoice','pollid','order'=>'choice ASC')
		);
	}
	
	public function rules() {
		return array(
			array('keyword','length','max'=>31),
			array('title','length','max'=>255),
			array('question','length','max'=>255),
			array('position','numerical','integerOnly'=>true,'min'=>1),
			array('title,question,position','required'),
			array('keyword','unique','criteria'=>array(
				'condition'=>'wallid=:wallid',
				'params'=>array(':wallid'=>$this->wallid))
			,'caseSensitive'=>false),
			array('position','unique','criteria'=>array(
				'condition'=>'wallid=:wallid',
				'params'=>array(':wallid'=>$this->wallid))
			),
			array('smsdefault,allowChoices,allowVotes,closed','boolean'),
			array('smsdefault','uniqueSmsDefault','criteria'=>array('condition'=>'smsdefault=TRUE'),'message'=>g('There is already a default poll set for this wall.')),
			array('clearVotes','boolean'),
		);
	}
	
	public function uniqueSmsDefault($attribute,$params) {
		if( $this->$attribute ) {
			$validator = CValidator::createValidator('unique',$this,'wallid',$params);
			return $validator->validate($this);
		}
	}
	
	public function attributeLabels() {
		return array(
			'title'=>g('Title'),
			'question'=>g('Question'),
			'position'=>g('Position'),
			'keyword'=>g('Keyword'),
			'smsdefault'=>g('SMS default'),
			'clearVotes'=>g('Clear all votes'),
			'allowChoices'=>g('Allow multiple choices'),
			'allowVotes'=>g('Allow multiple votes'),
      'closed'=>g('Voting is closed'),
		);
	}
	
	public function getAllowChoices() {
		return !$this->limitchoices;
	}
	
	public function setAllowChoices($value) {
		$this->limitchoices = !$value;
	}
	
	public function getAllowVotes() {
		return !$this->limitvotes;
	}
	
	public function setAllowVotes($value) {
		$this->limitvotes = !$value;
	}
	
	public function getSmsPrefix() {
		if( $this->smsdefault ) {
			return '';
		} else {
			return $this->keyword;
		}
	}
	
	public function vote($choice, $senderhash) {
		$pollChoiceParams = array(
			':pollid'=>$this->primaryKey,
			':senderhash'=>$senderhash,
			':choice'=>$choice
		);
		
		// If choices are limited, delete all but the selected choice
		$deleted = 0;
		if( $this->limitchoices ) {
			$deleted += PollVote::model()->deleteAll('pollid=:pollid AND senderhash=:senderhash AND choice<>:choice',$pollChoiceParams);
		}
		// If votes are limited, delete all votes from this choice
		if( $this->limitvotes ) {
			$deleted += PollVote::model()->deleteAll('pollid=:pollid AND senderhash=:senderhash AND choice=:choice',$pollChoiceParams);
		}

		$newVote = new PollVote;
		$newVote->pollid = $this->primaryKey;
		$newVote->choice = $choice;
		$newVote->senderhash = $senderhash;
		$newVote->trySave();
		
		return $deleted;
	}
	
	public function hasVotes($senderhash,$choice) {
		$pollChoiceParams = array(
			':pollid'=>$this->primaryKey,
			':senderhash'=>$senderhash,
			':choice'=>$choice
		);

		return PollVote::model()->exists('pollid=:pollid AND senderhash=:senderhash AND choice=:choice',$pollChoiceParams);
	}
	
	public function hasOtherVotes($senderhash,$choice) {
		$pollChoiceParams = array(
			':pollid'=>$this->primaryKey,
			':senderhash'=>$senderhash,
			':choice'=>$choice
		);

		return PollVote::model()->exists('pollid=:pollid AND senderhash=:senderhash AND choice<>:choice',$pollChoiceParams);
	}
}
