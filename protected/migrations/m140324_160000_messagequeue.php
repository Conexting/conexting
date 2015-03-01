<?php
class m140324_160000_messagequeue extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/messagequeue/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{message}}','approved','DATETIME');
		$this->addColumn('{{wall}}','premoderated','BOOLEAN NOT NULL DEFAULT FALSE');
	}
	
	public function down() {
		$this->dropColumn('{{message}}','approved');
		$this->dropColumn('{{wall}}','premoderated');
	}
}
