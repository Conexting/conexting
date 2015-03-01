<?php
class m131019_160000_wallindexing extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/wallindexing/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{wall}}','index','BOOLEAN NOT NULL DEFAULT TRUE');
	}
	
	public function down() {
		$this->dropColumn('{{wall}}','index');
	}
}
