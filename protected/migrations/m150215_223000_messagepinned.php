<?php
class m150215_223000_messagepinned extends CDbMigration {
	public function up() {
		$this->addColumn('{{message}}','pinned','DATETIME');
	}
	
	public function down() {
		$this->dropColumn('{{message}}','pinned');
	}
}
