<?php
class m150524_223000_vouchercountperclient extends CDbMigration {
	public function up() {
		$this->addColumn('{{voucher}}','countperclient','INTEGER NOT NULL DEFAULT 1');
	}
	
	public function down() {
		$this->dropColumn('{{voucher}}','countperclient');
	}
}
