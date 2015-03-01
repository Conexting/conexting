<?php
class Controller extends CController {
	public $layout = 'engine';
	
	public $pageDescription = null;
	public $pageKeywords = null;
	
	public function filters() {
		return array(
			'setLanguage'
		);
	}
	
	public function isAdmin() {
		return Yii::app()->session['isAdmin'];
	}
	
	public function init() {
		date_default_timezone_set('Europe/Helsinki');
		mb_internal_encoding('UTF-8');
		
		Yii::app()->theme = 'engine';
	}
	
	public function filterSetLanguage($filterChain) {
		if( $this->route === Yii::app()->errorHandler->errorAction ) {
			// Do not set language if we are in error handler
			return $filterChain->run();
		}
		
		if( isset($_GET['language']) && $this->isValidLanguage($_GET['language']) ) {
			// Set language as per request
			Yii::app()->language = $_GET['language'];
			// Save language setting to cookie and client
			if( Yii::app()->request->cookies['language'] != Yii::app()->language ) {
				Yii::app()->request->cookies['language'] = new CHttpCookie('language',Yii::app()->language,array('expire'=>time()+60*60*24*60));
			}
			if( !Yii::app()->user->isGuest && !is_null(Yii::app()->user->client) && Yii::app()->user->client->language != Yii::app()->language ) {
				Yii::app()->user->client->saveAttributes(array('language'=>Yii::app()->language));
			}
			$filterChain->run();
		} else if( isset(Yii::app()->request->cookies['language']) && $this->isValidLanguage(Yii::app()->request->cookies['language']->value) ) {
			// Select language from a cookie
			return $this->redirectToLanguage(Yii::app()->request->cookies['language']->value);
		} else if( !Yii::app()->user->isGuest && !is_null(Yii::app()->user->client) && $this->isValidLanguage(Yii::app()->user->client->language) ) {
			// Select language from the client
			return $this->redirectToLanguage(Yii::app()->user->client->language);
		} else {
			// Select first language from the list
			return $this->redirectToLanguage(array_shift(array_keys(Yii::app()->params['languages'])));
		}
	}
	
	public function createAbsoluteDefaultUrl($route, $params = array(), $schema = '', $ampersand = '&') {
		$params['language'] = null;
		return parent::createAbsoluteUrl($route, $params, $schema, $ampersand);
	}
	
	public function createDefaultUrl($route, $params = array(), $ampersand = '&') {
		$params['language'] = null;
		return parent::createUrl($route, $params, $ampersand);
	}
	
	public function beforeRender($view) {
		Yii::app()->clientScript->registerCoreScript('jquery.ui');
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/jquery-ui.css');
		return parent::beforeRender($view);
	}
	
	public function getNavItems() {
		$items = array();
		
		if( Yii::app()->user->isGuest ) {
			$items['client-login'] = array(
				'class'=>'bootstrap.widgets.TbMenu',
				'htmlOptions'=>array('class'=>'pull-right'),
				'items'=>array(
					array('label'=>g('Log in'),'url'=>array('client/index'),'icon'=>'hand-right'),
				)
			);
		} else {
			$items['client'] = array(
				'class'=>'bootstrap.widgets.TbMenu',
				'items'=>array(
					array('label'=>g('Home'),'url'=>array('client/index'),'icon'=>'home'),
					array('label'=>g('All walls'),'url'=>array('client/walls'),'icon'=>'list'),
					array('label'=>g('Account'),'url'=>array('client/account'),'icon'=>'user'),
				),
			);
			$items['client-login'] = array(
				'class'=>'bootstrap.widgets.TbMenu',
				'htmlOptions'=>array('class'=>'pull-right'),
				'items'=>array(
					array('label'=>g('Logout'),'url'=>array('client/logout'),'icon'=>'off'),
				)
			);
		}
		
		$items['language'] = array(
			'class'=>'bootstrap.widgets.TbMenu',
			'htmlOptions'=>array('class'=>'pull-right'),
			'encodeLabel'=>false,
			'items'=>array(),
		);
		foreach( Yii::app()->params['languages'] as $language => $label ) {
			$items['language']['items'][] = array(
				'label'=>CHtml::image(Yii::app()->baseUrl.'/images/flags/'.$language.'.png',$label),
				'url'=>$this->getUrlToLanguage($language),
			);
		}
		
		if( $this->isAdmin() ) {
			$items[] = array('htmlOptions'=>array('class'=>'divider-vertical'));
			$items['admin'] = array(
				'class'=>'bootstrap.widgets.TbMenu',
				'htmlOptions'=>array('class'=>'pull-right'),
				'items'=>array(
					array('label'=>'Admin','url'=>'#','items'=>array(
						array('label'=>g('Clients'),'url'=>array('admin/client'),'icon'=>'book'),
						array('label'=>g('Walls'),'url'=>array('admin/wall'),'icon'=>'list'),
						array('label'=>g('Vouchers'),'url'=>array('admin/voucher'),'icon'=>''),
						array('label'=>g('Payments'),'url'=>array('admin/payment'),'icon'=>''),
						'---',
						array('label'=>g('Logout'),'url'=>array('admin/logout'),'icon'=>'off'),
					)),
				),
			);
		}
		
		return $items;
	}
	
	public function renderJSON($object) {
		header('Content-Type: application/json');
		echo CJSON::encode($object);
	}
	
	public function cssFile($file,$theme=false,$cssUrl='/css/',$cssPath='/css/',$extension='.css') {
		$fileUrl = $this->getVersionedFileUrl($file,$theme,$cssUrl,$cssPath,$extension);
		if( $fileUrl ) {
			Yii::app()->clientScript->registerCssFile($fileUrl);
		}
	}
	
	public function jsFile($file,$theme=false,$jsUrl='/js/',$jsPath='/js/',$extension='.js') {
		$fileUrl = $this->getVersionedFileUrl($file,$theme,$jsUrl,$jsPath,$extension);
		if( $fileUrl ) {
			Yii::app()->clientScript->registerScriptFile($fileUrl);
		}
	}
	
	protected function getVersionedFileUrl($file,$theme,$fileUrl,$filePath,$extension) {
		if( $theme ) {
			$url = Yii::app()->theme->baseUrl.$fileUrl.$file.$extension;
			$path = Yii::app()->theme->basePath.$filePath.$file.$extension;
		} else {
			$url = Yii::app()->request->baseUrl.$fileUrl.$file.$extension;
			$path = Yii::app()->basePath.'/..'.$filePath.$file.$extension;
		}
		if(file_exists($path) ) {
			$timestamp = filemtime($path);
			return "$url?ver=$timestamp";
		} else {
			return false;
		}
	}
	
	protected function deleteRecord($model,$id) {
		$record = $model->findByPk($id);
		if( is_null($record) ) {
			return $this->redirect(array($this->id.'/'.lcfirst(get_class($model))));
		}

		if( $_POST[get_class($record)] ) {
			$record->deleted = new CDbExpression('NOW()');
			$record->trySave(false);
			f(g('{object} {name} has been deleted.',array('{name}'=>$record->name,'{object}'=>g(get_class($record)))));
			$this->redirect(array($this->id.'/'.lcfirst(get_class($model))));
		}

		return $this->render('delete'.get_class($record),compact('record'));
	}
  
	protected function viewRecord($model,$id) {
		$record = $model->findByPk($id);
		if( is_null($record) ) {
			return $this->redirect(array($this->id.'/'.lcfirst(get_class($model))));
		}
    
		return $this->render('view'.get_class($record),compact('record'));
  }
	
	protected function isValidLanguage($language) {
		return array_key_exists($language,Yii::app()->params['languages']);
	}
	
	protected function redirectToLanguage($language) {
		return $this->redirect($this->getUrlToLanguage($language));
	}
	
	protected function getUrlToLanguage($language) {
		$params = $this->actionParams;
		$params['language'] = $language;
		return $this->createUrl('',$params);
	}
}
