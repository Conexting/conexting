<?php
class m140211_120000_questionkeywordanywhere extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/questionkeywordanywhere/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{question}}','keywordanywhere','BOOLEAN NOT NULL DEFAULT FALSE');
	}
	
	public function down() {
		$this->dropColumn('{{question}}','keywordanywhere');
	}
}
