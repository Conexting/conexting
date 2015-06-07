<?php
class NotificationTestForm extends CFormModel {
	public $notification;
	public $to;
	
	public function rules() {
		return array(
			array('notification','safe'),
			array('to','email'),
			array('notification,to','required')
		);
	}
	
	public function attributeLabels() {
		return array(
			'notification'=>g('Notification'),
			'email'=>g('Email')
		);
	}
}