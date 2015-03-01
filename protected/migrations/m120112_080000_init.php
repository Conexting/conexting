<?php
class m120112_080000_init extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/init/';
	}
	
	protected function getTables() {
		return array(
			'client',
			'wall',
			'wallvariable',
			'tweet',
			'tweetmedia',
			'sms',
			'message',
			'poll',
			'pollchoice',
			'question',
		);
	}
}