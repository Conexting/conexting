<?php
class m141129_000000_smstimestamp extends CDbMigration {
	public function up() {
		$this->addColumn('{{sms}}','timestamp','DATETIME');
		$this->addColumn('{{sms}}','error','VARCHAR(32)');
	}
	
	public function down() {
		$this->dropColumn('{{sms}}','error');
		$this->dropColumn('{{sms}}','timestamp');
	}
}
