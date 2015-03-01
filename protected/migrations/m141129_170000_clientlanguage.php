<?php
class m141129_170000_clientlanguage extends CDbMigration {
	public function up() {
		$this->addColumn('{{client}}','language','CHAR(2)');
	}
	
	public function down() {
		$this->dropColumn('{{client}}','language');
	}
}
