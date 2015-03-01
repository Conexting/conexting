<?php
class m130411_133000_walltitle extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/walltitle/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{wall}}','title','VARCHAR(255)');
	}
	
	public function down() {
		$this->dropColumn('{{wall}}','title');
	}
}
