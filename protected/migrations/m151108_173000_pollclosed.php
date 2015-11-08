<?php
class m151108_173000_pollclosed extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/pollclosed/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{poll}}','closed','BOOLEAN DEFAULT FALSE');
	}
	
	public function down() {
		$this->dropColumn('{{poll}}','closed');
	}
}
