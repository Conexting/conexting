<?php
class PollChoice extends ActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function tableName() {
		return '{{pollchoice}}';
	}
	
	public function relations() {
		return array(
			'Poll' => array(self::BELONGS_TO,'Poll','pollid'),
			'PollVotes' => array(self::HAS_MANY,'PollVote','pollid,choice'),
			'PollVoteCount' => array(self::STAT,'PollVote','pollid,choice')
		);
	}
	
	public function rules() {
		return array(
			array('text','length','max'=>255),
			array('choice','numerical','integerOnly'=>true,'min'=>1),
			array('choice','unique','criteria'=>array(
				'condition'=>'pollid=:pollid',
				'params'=>array(':pollid'=>$this->pollid))
			),
		);
	}
	
	public function attributeLabels() {
		return array(
			'text'=>g('Choice {char}',array('{char}'=>$this->char))
		);
	}
	
	public function getVoteCount() {
		return $this->votes + $this->PollVoteCount;
	}
	
	public function getChar() {
		return self::getChoiceChar($this->choice);
	}
	
	public static function getChoiceNum($choice) {
		if( is_numeric($choice) ) {
			return $choice;
		} else {
			$tbl = array_flip(self::getCharTable());
			return $tbl[strtoupper($choice)];
		}
	}
	
	public static function getChoiceChar($choice) {
		if( is_numeric($choice) ) {
			$tbl = self::getCharTable();
			return $tbl[$choice];
		} else {
			return $choice;
		}
	}
	
	public static function getCharTable() {
		return array(
			1=>'A',
			2=>'B',
			3=>'C',
			4=>'D',
			5=>'E',
			6=>'F',
			7=>'G',
			8=>'H',
			9=>'I',
			10=>'J',
		);
	}
}
