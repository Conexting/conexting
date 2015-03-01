<?php
class m140114_160000_pollkeyword extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/pollkeyword/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{poll}}','keyword','VARCHAR(31) NOT NULL');
		$this->execute('UPDATE {{poll}} SET keyword=position');
	}
	
	public function down() {
		$this->dropColumn('{{poll}}','keyword');
	}
}
