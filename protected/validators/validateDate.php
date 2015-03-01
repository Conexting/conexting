<?php
class validateDate extends CValidator {
	/**
	 * @param ActiveRecord $object
	 * @param string $attribute
	 */
	protected function validateAttribute($object,$attribute) {
		if( is_null($object->$attribute) ) {
			// Ok, no-op
		} else if( is_numeric($object->$attribute) ) {
			// Ok, no-op
		} else if( $object->$attribute instanceof CDbExpression ) {
			// Ok, no-op
		} else if( strtotime($object->$attribute) === false ) {
			$object->addError($attribute,g('{attribute} is not a valid date',array('{attribute}'=>$object->getAttributeLabel($attribute))));
		}
	}	
}
