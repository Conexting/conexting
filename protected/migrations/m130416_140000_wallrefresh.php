<?php
class m130416_140000_wallrefresh extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/wallrefresh/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{wall}}','twitterrefreshed','DATETIME');
	}
	
	public function down() {
		$this->dropColumn('{{wall}}','twitterrefreshed');
	}
}
