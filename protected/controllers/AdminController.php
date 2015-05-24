<?php
class AdminController extends Controller {
	public function filters() {
		return array(
			'adminCheck - login',
		);
	}
	
	public function actionIndex() {
		return $this->render('index');
	}

	public function actionClient($id=false) {
		if( $id === false ) {
			// List clients
			$filter = new Client('search');
			$data = $filter->search(array(),array('accessed'=>CSort::SORT_DESC));
			return $this->render('clients',compact('filter','data'));
		} else {
			if( $id == 0 ) {
				// Create new client
				$client = new Client;
			} else {
				// Edit client
				$client = Client::model()->findByPk($id);
				if( is_null($client) ) {
					return $this->redirect(array('admin/client'));
				}
			}
			
			$contact = $client->Contact;
			if( is_null($contact) ) {
				$contact = new Contact;
				$contact->clientid = $client->primaryKey;
			}
			
			if( $_POST['Contact'] || $_POST['Client'] ) {
				$client->attributes = $_POST['Client'];
				$contact->attributes = $_POST['Contact'];
				$valid = $client->validate();
				$valid = $contact->validate() && $valid;
				if( $valid ) {
					$wasNewRecord = $client->isNewRecord;
					$transaction = Yii::app()->db->beginTransaction();
					try {
						if( !$wasNewRecord ) {
							$client->modified = new CDbExpression('NOW()');
						}
						$client->trySave();
						if( $wasNewRecord ) {
							$contact->clientid = $client->primaryKey;
						}
						$contact->trySave();
						f(g('Client details saved.'));
						if( $wasNewRecord ) {
							$password = Client::createPassword(8);
							$client->password = $client->getHash($password);
							if( $client->sendMail('admin/newClient',g('Welcome to Conexting, {name}',array('{name}'=>$client->name)),compact('password')) ) {
								f(g('Welcoming email was sent to the new client.'));
								$client->trySave();
								$transaction->commit();
								$this->redirect(array('admin/client'));
							} else {
								f(g('Error sending email to the client! Client password is not set.'),'error');
							}
						} else if( is_null($client->password) ) {
							$password = Client::createPassword(8);
							$client->password = $client->getHash($password);
							if( $client->sendMail('admin/newClient',g('Your Conexting account details'),compact('password')) ) {
								f(g('Notification email with login credentials was sent to the client.'));
								$client->trySave();
								$transaction->commit();
								$this->redirect(array('admin/client'));
							} else {
								f(g('Error sending email to the client! Client password is not set.'),'error');
							}
						} else {
							$transaction->commit();
							$this->redirect(array('admin/client'));
						}
					} catch( Exception $ex ) {
						f($ex->getMessage(),'error');
					}
				}
			}
			
			return $this->render('client',compact('client','contact'));
		}
	}
	
	public function actionDeleteClient($id) {
		return $this->deleteRecord(Client::model(),$id);
	}
	
	public function actionViewClient($id) {
		return $this->viewRecord(Client::model(),$id);
	}
	
	public function actionSms() {
		// List sms's
		$filter = new Sms('search');
		$data = $filter->search();
		return $this->render('smss',compact('filter','data'));
	}
	
	public function actionViewSms($id) {
		return $this->viewRecord(Sms::model(),$id);
	}
	
	public function actionVoucher($id=false) {
		if( $id === false ) {
			// List vouchers
			$filter = new Voucher('search');
			$data = $filter->search();
			return $this->render('vouchers',compact('filter','data'));
		} else {
			if( $id == 0 ) {
				// Create new voucher
				$voucher = new Voucher;
			} else {
				// Edit voucher
				$voucher = Voucher::model()->findByPk($id);
				if( is_null($voucher) ) {
					return $this->redirect(array('admin/voucher'));
				}
			}
			
			if( $_POST['Voucher'] ) {
				$voucher->attributes = $_POST['Voucher'];
				if( $voucher->validate() ) {
					$wasNewRecord = $voucher->isNewRecord;
					try {
						$voucher->trySave();
						if( $wasNewRecord ) {
							f(g('Voucher created.'));
						} else {
							f(g('Voucher data updated.'));
						}
						$this->redirect(array('admin/voucher'));
					} catch( Exception $ex ) {
						f($ex->getMessage(),'error');
					}
				}
			}
			
			return $this->render('voucher',compact('voucher'));
		}
	}
	
	public function actionDeleteVoucher($id) {
		return $this->deleteRecord(Voucher::model(),$id);
	}
	
	public function actionViewVoucher($id) {
		return $this->viewRecord(Voucher::model(),$id);
	}
	
	public function actionPayment($id=false) {
		if( $id === false ) {
			// List payments
			$filter = new Payment('search');
			$data = $filter->search(array(
				'order'=>'created DESC'
			));
			return $this->render('payments',compact('filter','data'));
		} else {
			if( $id == 0 ) {
				// Create new payment
				$payment = new Payment;
			} else {
				// Edit payment
				$payment = Payment::model()->findByPk($id);
				if( is_null($payment) ) {
					return $this->redirect(array('admin/payment'));
				}
			}
			
			if( $_POST['Payment'] ) {
				$payment->attributes = $_POST['Payment'];
				if( $payment->validate() ) {
					$wasNewRecord = $payment->isNewRecord;
					try {
						$payment->trySave();
						if( $wasNewRecord ) {
							f(g('Payment created.'));
						} else {
							f(g('Payment data updated.'));
						}
						$this->redirect(array('admin/payment'));
					} catch( Exception $ex ) {
						f($ex->getMessage(),'error');
					}
				}
			}
			
			return $this->render('payment',compact('payment'));
		}
	}
	
	public function actionDeletePayment($id) {
		return $this->deleteRecord(Payment::model(),$id);
	}
	
	public function actionViewPayment($id) {
		return $this->viewRecord(Payment::model(),$id);
	}
	
	public function actionWall($id=false) {
		if( $id === false ) {
			// List walls
			$filter = new Wall('search');
			$data = $filter->search(array(),array('expires'=>CSort::SORT_DESC,'created'=>CSort::SORT_DESC));
			return $this->render('walls',compact('filter','data'));
		} else {
			if( $id == 0 ) {
				// Create new wall
				$wall = new Wall;
			} else {
				// Edit wall
				$wall = Wall::model()->findByPk($id);
				if( is_null($wall) ) {
					return $this->redirect(array('admin/wall'));
				}
				$wall->scenario = 'admin';
			}
			
			if( $_POST['Wall'] ) {
				$wall->attributes = $_POST['Wall'];
				if( $wall->validate() ) {
					$wasNewRecord = $wall->isNewRecord;
					try {
						$wall->trySave();
						if( $wasNewRecord ) {
							f(g('{type} created.',array('{type}'=>g('Wall'))));
						} else {
							f(g('{type} data updated.',array('{type}'=>g('Wall'))));
						}
						$this->redirect(array('admin/wall'));
					} catch( Exception $ex ) {
						f($ex->getMessage(),'error');
					}
				}
			}
			
			return $this->render('wall',compact('wall'));
		}
	}
	
	public function actionDeleteWall($id) {
		return $this->deleteRecord(Wall::model(),$id);
	}
	
	public function actionViewWall($id) {
		return $this->viewRecord(Wall::model(),$id);
	}
	
	public function filterAdminCheck($filterChain) {
		if( $this->isAdmin() ) {
			return $filterChain->run();
		} else {
			Yii::app()->user->setReturnUrl(Yii::app()->request->getUrl());
			return $this->redirect(array('admin/login'));
		}
	}
	
	public function actionLogin() {
		if( $this->isAdmin() ) {
   		return $this->redirect(array('admin/index'));
		}
	
		$login = new LoginForm;
		if( isset($_POST['LoginForm']) ) {
			$login->attributes = $_POST['LoginForm'];
			$pwd = sha1($login->password);
			foreach( Yii::app()->params['admins'] as $adminName => $adminPwd ) {
				if( $login->name === $adminName && $pwd === $adminPwd ) {
					Yii::app()->session['isAdmin'] = true;
					$this->redirect(Yii::app()->user->returnUrl);
				}
			}
			// Login failed
			f(g('Login failed, check name and password'),'error');
		}
		
		$login->password = null;
		
		$this->pageTitle = g('Admin login');
		return $this->render('login',compact('login'));
	}
	
	public function actionLogout() {
		Yii::app()->session['isAdmin'] = false;
		return $this->redirect(array('admin/login'));
	}
	
	protected function navBarItems(&$items) {
		
	}
}
