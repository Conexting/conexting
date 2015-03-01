<?php
class m141129_160000_walllifetime extends CDbMigration {
	public function up() {
		$this->addColumn('{{wall}}','dies','DATETIME');
		$this->execute('UPDATE {{wall}} SET dies = expires + INTERVAL 3 MONTH');
		$this->alterColumn('{{wall}}','dies','DATETIME NOT NULL');
		$this->addColumn('{{wall}}','hidden','BOOLEAN NOT NULL DEFAULT FALSE');
		$this->addColumn('{{wall}}','smscredit','INTEGER UNSIGNED NOT NULL DEFAULT 0');
		$this->addColumn('{{client}}','smscredit','INTEGER UNSIGNED NOT NULL DEFAULT 0');
		$this->alterColumn('{{wall}}','published','DATETIME');
	}
	
	public function down() {
		$this->alterColumn('{{wall}}','published','BOOLEAN NOT NULL DEFAULT FALSE');
		$this->dropColumn('{{wall}}','smscredit');
		$this->dropColumn('{{client}}','smscredit');
		$this->dropColumn('{{wall}}','dies');
		$this->dropColumn('{{wall}}','hidden');
	}
}
