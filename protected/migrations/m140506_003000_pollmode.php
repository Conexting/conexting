<?php
class m140506_003000_pollmode extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/pollmode/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{poll}}','limitvotes','BOOLEAN DEFAULT TRUE');
		$this->addColumn('{{poll}}','limitchoices','BOOLEAN DEFAULT TRUE');
	}
	
	public function down() {
		$this->dropColumn('{{poll}}','limitvotes');
		$this->dropColumn('{{poll}}','limitchoices');
	}
}
