<?php
class LoginForm extends CFormModel {
	public $name;
	public $password;
	
	public function rules() {
		return array(
			array('name,password','safe')
		);
	}
	
	public function attributeLabels() {
		return array(
			'name'=>g('Name'),
			'password'=>g('Password'),
		);
	}
}