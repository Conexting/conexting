<?php
class m130430_160000_reseller extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/reseller/';
	}
	
	protected function getTables() {
		return array(
			'reseller',
			'coupon',
		);
	}
	
	public function up() {
		parent::up();
		$this->addColumn('{{payment}}','couponid','INTEGER UNSIGNED');
		$this->addForeignKey('{{payment}}_coupon','{{payment}}','couponid','{{coupon}}','couponid','SET NULL','CASCADE');
	}
	
	public function down() {
		$this->dropForeignKey('{{payment}}_coupon','{{payment}}');
		$this->dropColumn('{{payment}}','couponid');
		parent::down();
	}
}
