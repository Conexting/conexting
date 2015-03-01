<?php
class m131102_170000_wallexpires extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/wallexpires/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{wall}}','expires','DATETIME');
	}
	
	public function down() {
		$this->dropColumn('{{wall}}','expires');
	}
}
