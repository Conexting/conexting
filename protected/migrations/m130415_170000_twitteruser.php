<?php
class m130415_170000_twitteruser extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/twitteruser/';
	}
	
	protected function getTables() {
		return array(
			'twitteruser'
		);
	}
	
	public function up() {
		parent::up();
		$this->addColumn('{{wall}}','twitteruser','INTEGER UNSIGNED');
		$this->addForeignKey('{{wall}}_twitteruser','{{wall}}','twitteruser','{{twitteruser}}','userid');
	}
	
	public function down() {
		$this->dropForeignKey('{{wall}}_twitteruser','{{wall}}');
		$this->dropColumn('{{wall}}','twitteruser');
		parent::down();
	}
}
