<?php
class ChatView extends CWidget {
	public $showMore = true;
	public $showMsg = true;
	public $showImages = true;
	public $showSearch = false;
	public $prependMsg = false;
	public $options = array();
	public $showPinnedBox = false;
	
	public function init() {
		parent::init();
	}
	
	public function run() {
		return $this->render('chatview');
	}
}
