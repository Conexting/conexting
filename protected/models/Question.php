<?php
class Question extends DeletableActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model('Question');
	}
	
	public function tableName() {
		return '{{question}}';
	}
	
	public function relations() {
		return array(
			'Wall' => array(self::BELONGS_TO,'Wall','wallid'),
		);
	}
	
	public function rules() {
		return array(
			array('keyword','length','max'=>31),
			array('title','length','max'=>255),
			array('question','length','max'=>255),
			array('position','numerical','integerOnly'=>true,'min'=>1),
			array('keyword','unique','criteria'=>array(
				'condition'=>'wallid=:wallid',
				'params'=>array(':wallid'=>$this->wallid))
			,'caseSensitive'=>false),
			array('position','unique','criteria'=>array(
				'condition'=>'wallid=:wallid',
				'params'=>array(':wallid'=>$this->wallid))
			),
			array('smsdefault,keywordanywhere','boolean'),
			array('smsdefault','uniqueSmsDefault','message'=>g('There is already a default question set for this wall.')),
			array('keyword,title,question','required'),
		);
	}
	
	public function uniqueSmsDefault($attribute,$params) {
		if( $this->$attribute ) {
			$params['criteria'] = array(
				'condition'=>'wallid=:wallid',
				'params'=>array(':wallid'=>$this->wallid)
			);
			$validator = CValidator::createValidator('unique',$this,'smsdefault',$params);
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
		);
	}
	
	public function getMysqlPattern() {
		$pattern = '[[:space:],.]*'.$this->keyword.'[[:space:],.]+';
		if( !$this->keywordanywhere ) {
			$pattern = '^'.$pattern;
		}
		return $pattern;
	}
	
	public function getPattern() {
		$pattern = '[\s,\.]*'.$this->keyword.'[\s,\.]+';
		if( !$this->keywordanywhere ) {
			$pattern = '^'.$pattern;
		}
		return "/$pattern/i";
	}
	
	public function getReplacePattern() {
		$pattern = '^\s*'.$this->keyword.'\s+';
		return "/$pattern/i";
	}
	
	public function getSmsPrefix() {
		if( $this->smsdefault ) {
			return '';
		} else {
			return $this->keyword;
		}
	}
}
