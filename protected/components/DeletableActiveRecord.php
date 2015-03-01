<?php
class DeletableActiveRecord extends ActiveRecord {
	public $showDeleted = false;
	
	public function delete() {
		$this->saveAttributes(array('deleted'=>new CDbExpression('NOW()')));
	}
	
	public function undelete() {
		$this->saveAttributes(array('deleted'=>null));
	}
	
	public function defaultScope() {
		if( $this->showDeleted ) {
			return array();
		} else {
			return array(
				'condition'=>$this->getTableAlias(false,false).".deleted IS NULL"
			);
		}
	}
}