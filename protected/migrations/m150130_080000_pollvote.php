<?php
class m150130_080000_pollvote extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/pollvote/';
	}
	
	protected function getTables() {
		return array(
			'pollvote',
		);
	}
}
