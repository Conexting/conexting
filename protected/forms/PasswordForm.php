<?php
class PasswordForm extends CFormModel {
	public $password;
	
	public function rules() {
		return array(
			array('password','safe')
		);
	}
	
	public function attributeLabels() {
		return array(
			'password'=>g('Password'),
		);
	}
}