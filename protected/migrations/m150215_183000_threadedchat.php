<?php
class m150215_183000_threadedchat extends CDbMigration {
	public function up() {
		$this->addColumn('{{wall}}','threaded','BOOLEAN NOT NULL DEFAULT TRUE');
	}
	
	public function down() {
		$this->dropColumn('{{wall}}','threaded');
	}
}
