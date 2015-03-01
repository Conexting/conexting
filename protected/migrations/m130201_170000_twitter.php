<?php
class m130201_170000_twitter extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/twitter/';
	}
	
	protected function getTables() {
		return array(
			'twitterapp',
		);
	}
}