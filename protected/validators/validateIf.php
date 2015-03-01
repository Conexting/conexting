<?php
class validateIf extends CValidator {
	public $compare;
	public $validator;
	public $setInvalidToNull = true;
	public $params = array();
	
	protected function validateAttribute($object,$attribute) {
		if( $object{$this->compare} ) {
			$validator = CValidator::createValidator($this->validator,$object,$attribute,$this->params);
			$validator->validate($object);
		} else {
			if( $this->setInvalidToNull ) {
				$object->$attribute = null;
			}
		}
	}	
}
