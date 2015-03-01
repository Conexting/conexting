<?php
class ClientController extends Controller {
	public function filters() {
		return array(
			'setLanguage',
			'accessControl',
		);
	}
	
	public function accessRules() {
		return array(
			array('allow','users'=>array('@')),
			array('allow','actions'=>array('login','signup','forgottenPassword')),
			array('deny','users'=>array('*'))
		);
	}
	
	public function actionIndex() {
		$wallModel = Wall::model();
		$wallModel->showDeleted = true;
		$clientWallsTotal = $wallModel->count('clientid=:clientid',array(
			':clientid'=>Yii::app()->user->client->primaryKey
		));
		$wallModel->showDeleted = false;
		
		$data = new CActiveDataProvider($wallModel,array(
			'criteria'=>array(
				'condition'=>'clientid=:clientid AND (expires IS NULL OR expires > NOW())',
				'order'=>'',
				'params'=>array(
					':clientid'=>Yii::app()->user->client->primaryKey
				),
			),
		));
		$this->pageTitle = g('My walls');
		return $this->render('index',compact('data','clientWallsTotal'));
	}
	
	public function actionAccount() {
		return $this->render('account');
	}
	
	public function actionAccountEdit() {
		$client = Yii::app()->user->client;
		$contact = $client->Contact;
		if( is_null($contact) ) {
			$contact = new Contact;
			$contact->clientid = $client->primaryKey;
		}
		$client->scenario = 'edit';
		
		if( $_POST['Contact'] || $_POST['Client'] ) {
			$client->attributes = $_POST['Client'];
			$contact->attributes = $_POST['Contact'];
			$valid = $client->validate();
			$valid = $contact->validate() && $valid;
			if( $valid ) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$client->trySave();
					$contact->trySave();
					$transaction->commit();
					f(g('Account details have been updated.'));
					$this->redirect(array('client/account'));
				} catch( Exception $ex ) {
					f($ex->getMessage(),'error');
				}
			}
		}
		
		$this->pageTitle = g('Account settings');
		return $this->render('accountEdit',compact('client','contact'));
	}
	
	public function actionWalls($showDeleted=false) {
		// List this client's walls
		$wallModel = Wall::model();
		$wallModel->showDeleted = $showDeleted;
		$data = new CActiveDataProvider($wallModel,array(
			'criteria'=>array(
				'condition'=>'clientid=:clientid',
				'params'=>array(
					':clientid'=>Yii::app()->user->client->primaryKey
				),
			),
			'sort'=>array(
				'defaultOrder'=>array(
					'dies'=>CSort::SORT_DESC,
				)
			)
		));
		$this->pageTitle = g('My walls');
		return $this->render('walls',compact('data','showDeleted'));
	}
	
	public function actionWallCreate($from=false) {
		return $this->editWall(false,false,false,$from);
	}
	
	public function actionWallSettings($search, $retrieve=false, $from=false) {
		return $this->editWall($search,$retrieve,false,$from);
	}
	
	public function actionWallCopy($search, $retrieve=false) {
		return $this->editWall(false,$retrieve,$search);
	}
	
	protected function editWall($search=false, $retrieve=false, $copyFromWall=false, $from=false) {
		if( isset($_POST['cancel']) ) {
			return $this->redirectFrom($from);
		}
		
		if( $copyFromWall ) {
			$copyFrom = $this->getWallByName($copyFromWall);
		} else {
			$copyFrom = null;
		}
		
		if( $search ){
			$wall = $this->getWallByName($search,'index');
		} else {
			$wall = new Wall();
			$wall->clientid = Yii::app()->user->client->primaryKey;
		}

		if( $_POST['Wall'] ) {
			$wall->attributes = $_POST['Wall'];
			if( isset($_POST['sign-in-twitter']) ) {
				Yii::app()->session['WallCache'] = $_POST['Wall'];
				return $this->redirect(array($this->id.'/signInWithTwitter','wallname'=>$wall->name));
			} else if( isset($_POST['sign-out-twitter']) ) {
				Yii::app()->session['WallCache'] = $_POST['Wall'];
				return $this->redirect(array($this->id.'/disconnectTwitter','wallname'=>$wall->name));
			} else if( $wall->validate() ) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					if( !$wall->isPublished || $wall->isNewRecord ) {
						// Unpublished walls get more lifetime when saved
						$wall->dies = Wall::intervalDateExpression(Yii::app()->params['cnxConfig']['unpublishedWallLifetime']);
					}
					if( is_null($copyFrom) ) {
						$wall->theme = current(array_keys(Yii::app()->params['themes']));
					} else {
						$wall->theme = $copyFrom->theme;
					}
					$wall->trySave();
					if( !is_null($copyFrom) ) {
						$wall->ThemeModel->attributes = $copyFrom->getVars();
						$wall->ThemeModel->save();
					}
					$transaction->commit();
					f(g('Wall <em>{name}</em> settings have been updated.',array('{name}'=>CHtml::encode($wall->name))),'success');
					return $this->redirectFrom($from);
				} catch( Exception $ex ) {
					$transaction->rollback();
					f($ex->getMessage(),'error');
				}
			}
		} else if( $retrieve ) {
			$wall->attributes = Yii::app()->session['WallCache'];
		}
		
		return $this->render('wallSettings',compact('wall'));
	}
	
	public function actionWallDelete($search,$from=false) {
		$wall = $this->getWallByName($search,$from);
		
		$wall->delete();
		if( !Yii::app()->request->isAjaxRequest ) {
			$undoUrl = $this->createUrl('wallUndelete',array('search'=>$wall->name,'id'=>$wall->primaryKey,'from'=>$from));
			$undoLink = CHtml::link('<i class="fa fa-undo"></i> '.g('Undo'),$undoUrl);
			f(g('Wall {name} has been removed.',array('{name}'=>$wall->name)).' '.$undoLink,'success');
			return $this->redirectFrom($from);
		}
	}
	
	public function actionWallHide($search,$from=false) {
		return $this->wallToggleHidden($search,true,$from);
	}
	
	public function actionWallShow($search,$from=false) {
		return $this->wallToggleHidden($search,false,$from);
	}
	
	protected function wallToggleHidden($search,$hidden,$from=false) {
		$wall = $this->getWallByName($search,$from);
		if( $wall->isPublished ) {
			$wall->saveAttributes(array(
				'hidden'=>$hidden
			));
			if( $hidden ) {
				f(g('Wall <em>{name}</em> has been hidden',array('{name}'=>CHtml::encode($wall->displayTitle))));
			} else {
				f(g('Wall <em>{name}</em> is now visible',array('{name}'=>CHtml::encode($wall->displayTitle))));
			}
		}
		return $this->redirectFrom($from,$wall);
	}
	
	public function actionWallUndelete($search,$id,$from=false) {
		$wallModel = Wall::model();
		$wallModel->showDeleted = true;
		$wall = $wallModel->findByAttributes(array(
			'name'=>$search,
			'wallid'=>$id,
			'clientid'=>Yii::app()->user->client->primaryKey
		));
		if( is_null($wall) ) {
			return $this->redirectFrom($from);
		}
		
		// Check for existing walls with same name
		$wallModel->showDeleted = false;
		$existingWall = $wallModel->findByAttributes(array(
			'name'=>$search
		));
		if( !is_null($existingWall) ) {
			f(g('Wall with the name {name} exists, cannot undelete your wall.',array('{name}'=>$existingWall->name)),'error');
			return $this->redirectFrom($from);
		}
		
		$wall->undelete();
		
		// If undeleted wall has died, extend lifetime so that it doesn't die again at the end of the day
		if( $wall->dies < time() ) {
			$wall->saveAttributes(array(
				'dies'=>Wall::intervalDateExpression(Yii::app()->params['cnxConfig']['unpublishedWallLifetime'])
			));
		}
		
		f(g('Wall <em>{name}</em> has been undeleted and is available again.',array('{name}'=>$wall->name)),'success');
		return $this->redirectFrom($from);
	}
	
	public function actionWallTheme($search,$from=false) {
		if( isset($_POST['cancel']) ) {
			return $this->redirectFrom($from);
		}
		
		$wall = $this->getWallByName($search,$from);
		
		if( $_POST[get_class($wall->ThemeModel)] ) {
			$wall->ThemeModel->attributes = $_POST[get_class($wall->ThemeModel)];
			if( $wall->ThemeModel->validate() ) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$wall->ThemeModel->save();
					$transaction->commit();
					f(g('Wall <em>{name}</em> theme has been updated.',array('{name}'=>CHtml::encode($wall->name))),'success');
					return $this->redirectFrom($from);
				} catch( Exception $ex ) {
					$transaction->rollback();
					f($ex->getMessage(),'error');
				}
			}
		}
		
		$this->pageTitle = g('Wall theme');
		return $this->render('wallTheme',compact('wall'));
	}
	
	public function actionWallPublish($search, $from='index', $option=false) {
		$wall = $this->getWallByName($search,$from);
		
		if( isset($_POST['cancel']) ) {
			return $this->redirectFrom($from,$wall);
		}
		
		if( !$wall->isPublished && !$wall->hasPremiumFeatures ) {
			// Publishing non-premium wall for the firs time
			$isFree = true;
		} else if($wall->isExpired && !$wall->premium) {
			// Extending expired non-premium wall -> free
			$isFree = true;
		} else {
			// Otherwise wall must be purchased
			$isFree = false;
		}
		
		$options = Yii::app()->params['store']['walls'];
		foreach( array_keys($options) as $key ) {
			$options[$key]['expires'] = Wall::intervalDate($options[$key]['length'],$wall->expires);
			$options[$key]['dies'] = Wall::intervalDate($options[$key]['removedAfter'],$wall->expires);
		}
		
		if( $_POST['voucher'] ) {
			$voucher = Voucher::model()->findByAttributes(array('code'=>$_POST['voucher']));
			
			if( !is_null($voucher) ) {
				f(g('Voucher accepted, please fill in your details to redeem your free Conexting wall.'),'success');
				return $this->redirect(array('store/wall','search'=>$wall->name,'vouchercode'=>$voucher->code,'returnUrl'=>$this->createUrl('',array('search'=>$search,'from'=>$from))));
			} else {
				f(g('Voucher code <em>{code}</em> could not be found, please check the code you entered.',array('{code}'=>CHtml::encode($_POST['voucher']))),'error');
			}
		}
		
		if( $option && array_key_exists($option,$options) ) {
			if( $isFree ) {
				$wall->publish($options[$option]['length'],$options[$option]['removedAfter']);
				return $this->redirect(array('wall/index','wall'=>$wall->name));
			} else {
				return $this->redirect(array('store/wall','search'=>$wall->name,'option'=>$option,'returnUrl'=>$this->createUrl('',array('search'=>$search,'from'=>$from))));
			}
		}
		
		if( !$wall->isPublished ) {
			$this->pageTitle = g('Publish wall');
		} else if( !$wall->isExpired && !$wall->premium ) {
			$this->pageTitle = g('Upgrade wall to Premium');
		} else {
			$this->pageTitle = g('Extend wall use');
		}
		
		return $this->render('wallPublish',compact('wall','options','isFree'));
	}
	
	public function actionWallUpgrade($search, $from='index', $option=false) {
		return $this->actionWallPublish($search, $from, $option);
	}
	
	public function actionSignInWithTwitter($wallname,$search=false,$denied=false) {
		$wall = $this->getWallByName($wallname,'index');
		
		Yii::app()->twitter->setApp(Yii::app()->twitter->appidWithUser);
		if( $search == 'complete' ) {
			if( !$denied ) {
				$twUser = Yii::app()->twitter->accessToken($_REQUEST['oauth_verifier']);
				if( $twUser ) {
					$wall->saveAttributes(array(
						'twitteruser' => $twUser->primaryKey
					));
				}
			}
			return $this->redirect(array($this->id.'/wallSettings','search'=>$wallname,'retrieve'=>true));
		} else {
			$url = Yii::app()->twitter->requestToken($this->createAbsoluteUrl($this->route,array('wallname'=>$wallname,'search'=>'complete')));
			return $this->redirect($url);
		}
	}
	
	public function actionDisconnectTwitter($wallname) {
		$wall = $this->getWallByName($wallname,'index');
		
		$wall->saveAttributes(array(
			'twitteruser' => null
		));
		
		return $this->redirect(array($this->id.'/walls','search'=>$wallname,'retrieve'=>true));
	}
	
	public function actionSignup() {
		$client = new Client('signup');
		
		if( $_POST['Client'] ) {
			$client->attributes = $_POST['Client'];
			if( $client->validate() ) {
				$password = Client::createPassword(8);
				$client->password = $client->getHash($password);
				if( $client->sendMail('client/signup',g('Your Conexting account details'),compact('password')) ) {
					f(g('Your account has been created! Check your email to complete the registration.'),'success');
					$client->trySave();
					return $this->redirect(array('site/index'));
				} else {
					f(g('Error sending email! Please contact us to create your account.'),'error');
				}
			}
		}
		
		$this->pageTitle = g('Sign up');
		return $this->render('signup',compact('client'));
	}
	
	public function actionLogin() {
		$client = new Client('login');
		$returnUrl = Yii::app()->user->returnUrl;
		if( $_POST['Client'] ) {
			$client->attributes = $_POST['Client'];
			$identity = new UserIdentity($client->email,$client->password);
			if( $identity->authenticate() ) {
				Yii::app()->user->login($identity,Yii::app()->params['clientLoginLifetime']);
				f(g('You are now logged in as <strong>{name}</strong>.',array('{name}'=>$identity->username)),'success');
				return $this->redirect($returnUrl);
			} else {
				f(g('Invalid login credentials, please verify that you typed the correct info.'),'error');
			}
		} else if( preg_match('/urllogin=([^&]*)/',$returnUrl,$regs) ) {
			$client = Client::getClientFromLoginHash($regs[1]);
			if( !is_null($client) ) {
				$identity = new UserIdentity($client->name,null);
				$identity->setClient($client);
				f(g('You are now logged in as <strong>{name}</strong>.',array('{name}'=>$client->name)),'success');
				Yii::app()->user->login($identity);
			}
			return $this->redirect(preg_replace('/urllogin=([^&]*)/','',$returnUrl));
		}
		
		$this->pageTitle = g('Log in');
		return $this->render('login',compact('client'));
	}
	
	public function actionLogout() {
		Yii::app()->user->logout();
		return $this->redirect(array('site/index'));
	}
	
	public function actionChangePassword() {
		Yii::app()->user->client->scenario = 'change-password';
		
		if( $_POST['Client'] ) {
			Yii::app()->user->client->attributes = $_POST['Client'];
			if( Yii::app()->user->client->validate(array('password_new','password_confirm')) ) {
				Yii::app()->user->client->password = Client::getHash(Yii::app()->user->client->password_new);
				Yii::app()->user->client->trySave();
				f(g('Your account password has been changed.'));
				return $this->redirect(array('client/index'));
			}
		}
		
		$this->pageTitle = g('Change password');
		return $this->render('changePassword',compact('client'));
	}
	
	public function actionForgottenPassword() {
		$client = new Client('search');
		
		if( $_POST['Client'] ) {
			$client->attributes = $_POST['Client'];
			if( $client->validate(array('email')) ) {
				$foundClient = Client::model()->findByAttributes(array('email'=>$client->email));
				if( !is_null($foundClient) ) {
					$client = $foundClient;
					$loginHash = $client->getLoginUrlHash();
					$url = $this->createAbsoluteUrl('client/changePassword',array('urllogin'=>$loginHash));
					$client->sendMail('client/forgottenPassword',g('Conexting password reset'),compact('url'));
					f(g('Password has been sent to <em>{address}</em>',array('{address}'=>CHtml::encode($client->email))),'success');
					return $this->redirect(array('site/index'));
				} else {
					f(g('Email address you provided was not found, check the email address.'),'error');
				}
			} else {
				f($client->getError('email'),'error');
			}
		}
		
		$this->pageTitle = g('Forgot your password?');
		return $this->render('forgottenPassword',compact('client'));
	}
	
	private function getWallByName($name, $redirectFrom=false) {
		$wall = Wall::model()->findByAttributes(array(
			'name'=>$name,
			'clientid'=>Yii::app()->user->client->primaryKey
		));
		if( is_null($wall) && $redirectFrom !== false ) {
			return $this->redirectFrom($redirectFrom);
		}
		return $wall;
	}
	
	private function redirectFrom($from,$wall=null) {
		if( $from == 'walls' ) {
			return $this->redirect(array('walls'));
		} else if( $from == 'wall' && !is_null($wall) ) {
			return $this->redirect(array('wall/index','wall'=>$wall->name));
		} else {
			return $this->redirect(array('index'));
		}
	}
}
