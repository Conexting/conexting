<?php
class m131011_110000_smsdefault extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/smsdefault/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{question}}','smsdefault','BOOLEAN NOT NULL DEFAULT FALSE');
		$this->addColumn('{{poll}}','smsdefault','BOOLEAN NOT NULL DEFAULT FALSE');
	}
	
	public function down() {
		$this->dropColumn('{{question}}','smsdefault');
		$this->dropColumn('{{poll}}','smsdefault');
	}
}
