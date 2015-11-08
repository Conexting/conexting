<?php
class MailMessage extends YiiMailMessage {
	public $layout = '//mail/layout';
	public $title = 'Conexting';
	
	public function setBody($body='',$contentType='text/html',$charset='utf-8') {
		if ($this->view !== null) {
			if (!is_array($body)) {
				$body = array('body'=>$body);
			}
			if( isset(Yii::app()->controller) ) {
				$controller = Yii::app()->controller;
			} else {
				$controller = new Controller('YiiMail');
			}
			
			list($utm_source,$utm_campaign) = explode('/',$this->view,2);

			$viewPath = Yii::getPathOfAlias(Yii::app()->mail->viewPath.'.'.$this->view).'.php';
			$htmlBody = $controller->renderInternal($viewPath,array_merge($body,array('mail'=>$this,'utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign)),true);
			$htmlLayout = Yii::app()->getBasePath().'/views'.$this->layout.'.php';
			$htmlPart = $controller->renderInternal($htmlLayout,array('subject'=>$this->subject,'content'=>$htmlBody,'title'=>$this->title,'utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign),true);
			
			$viewPath = Yii::getPathOfAlias(Yii::app()->mail->viewPath.'.'.$this->view).'.txt.php';
			$textBody = $controller->renderInternal($viewPath,array_merge($body,array('mail'=>$this)),true);
			$textLayout = Yii::app()->getBasePath().'/views'.$this->layout.'.txt.php';
			$textPart = $controller->renderInternal($textLayout,array('subject'=>$this->subject,'content'=>$textBody,'title'=>$this->title,'utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign),true);
			
			$this->message->setBody($textPart,'text/plain',$charset);
			$this->message->addPart($htmlPart,'text/html');
			return $this;
		} else {
			return $this->message->setBody($body,$contentType,$charset);
		}
	}
}