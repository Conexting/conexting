<?php
class SiteController extends Controller {
	public function actionIndex() {
		$this->pageTitle = 'Connecting OS';
		$this->render('index');
	}

	public function actionError() {
		if( $error=Yii::app()->errorHandler->error ) {
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->pageTitle = g('Error');
				$this->render('error', $error);
		}
	}
}