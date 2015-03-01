<?php
class m130522_170000_adminmessage extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/adminmessage/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{message}}','adminmessage','BOOLEAN NOT NULL DEFAULT FALSE');
	}
	
	public function down() {
		$this->dropColumn('{{message}}','adminmessage');
	}
}
