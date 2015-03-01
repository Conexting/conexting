<?php
class m130313_170000_store extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/store/';
	}
	
	protected function getTables() {
		return array(
			'contact',
			'payment',
		);
	}
}
