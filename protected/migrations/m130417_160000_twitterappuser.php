<?php
class m130417_160000_twitterappuser extends StandardDbMigration {
	protected function getDir() {
		return dirname(__FILE__).'/twitterappuser/';
	}
	
	protected function getTables() {
		return array(
		);
	}
	
	public function up() {
		$this->addColumn('{{twitterapp}}','user_id','INTEGER UNSIGNED');
		$this->updateApp(Yii::app()->params['cnxConfig']['twitter']['messageApp']);
		$this->updateApp(Yii::app()->params['cnxConfig']['twitter']['loginApp']);
	}
	
	public function down() {
		$this->dropColumn('{{twitterapp}}','user_id');
	}
	
	private function updateApp($conf) {
		$this->update('{{twitterapp}}',array('user_id'=>$conf['userId']),'appid='.$conf['id']);
	}
}
