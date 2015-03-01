<?php
class m130410_120000_accesstime extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/accesstime/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{client}}','accessed','DATETIME');
	}
	
	public function down() {
		$this->dropColumn('{{client}}','accessed');
	}
}
