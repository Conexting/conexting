<?php
class m141128_220000_license extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/license/';
	}
	
	protected function getTables() {
		return array(
			'license',
			'voucher'
		);
	}
	
	public function up() {
		parent::up();
		$this->dropColumn('{{client}}','credits');
		$this->dropForeignKey('{{payment}}_coupon','{{payment}}');
		$this->dropColumn('{{payment}}','couponid');
		$this->dropColumn('{{payment}}','claimed');
		$this->dropTable('{{coupon}}');
		$this->dropTable('{{reseller}}');
		$this->addColumn('{{wall}}','voucherid','INTEGER UNSIGNED');
		$this->addColumn('{{payment}}','wallid','INTEGER UNSIGNED');
		$this->addForeignKey('{{wall}}_voucherid','{{wall}}','voucherid','{{voucher}}','voucherid','SET NULL','CASCADE');
		$this->addForeignKey('{{payment}}_wallid','{{payment}}','wallid','{{wall}}','wallid','SET NULL','CASCADE');
	}
	
	public function down() {
		throw new Exception('This migration cannot be undone, sorry');
	}
}
