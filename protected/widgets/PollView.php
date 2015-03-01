<?php
class PollView extends CWidget {
	public $poll;
	public $myChoice;
	public $showChoiceChar = null;
	public $options = array();
	
	public function init() {
		parent::init();
		
		if( !is_array($this->myChoice) ) {
			$this->myChoice = array($this->myChoice);
		}
	}
	
	public function run() {
		if( is_null($this->showChoiceChar) ) {
			$this->showChoiceChar = $this->poll->Wall->enablesms;
		}
		return $this->render('pollview');
	}
}
