<?php
class StoreController extends Controller {
	public function filters() {
		return array(
			'setLanguage',
			'accessControl',
		);
	}
	
	public function accessRules() {
		return array(
			array('allow','users'=>array('@')),
			array('deny','users'=>array('*'))
		);
	}
	
	public function actionWall($search, $option=false, $returnUrl=false, $vouchercode=false) {
		if( $returnUrl === false ) {
			$returnUrl = array('client/index');
		}		
		
		$wall = Wall::model()->find('UPPER(name) LIKE UPPER(:name)',array(
			':name'=>$search
		));
		
		if( is_null($wall) ) {
			f(g('Could not find wall with name {name}',array('{name}'=>CHtml::encode($search))),'error');
			return $this->redirect($returnUrl);
		}
		
		if( !$vouchercode ) {
			$options = Yii::app()->params['store']['walls'];
			if( !array_key_exists($option,$options) ) {
				f(g('Invalid purchase option'),'error');
				return $this->redirect($returnUrl);
			}
		}
		
		$client = Yii::app()->user->client;
		$contact = $client->Contact;
		if( is_null($contact) ) {
			$contact = new Contact;
			$contact->clientid = $client->primaryKey;
		}
		
		if( $_POST['Contact'] ) {
			$contact->attributes = $_POST['Contact'];
			if( $contact->validate() ) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$contact->modified = new CDbExpression('NOW()');
					$contact->trySave();
					
					if( $vouchercode ) {
						$voucher = Voucher::model()->findByAttributes(array('code'=>$vouchercode));
						if( is_null($voucher) ) {
							throw new Exception(g('Voucher code <em>{code}</em> could not be found, please check the code you entered.',array('{code}'=>CHtml::encode($vouchercode))));
						}
						$voucher->check(Yii::app()->user->client->primaryKey);
						$transaction->commit();
						return $this->redirect(array($this->id.'/redeem','search'=>$wall->name,'vouchercode'=>$voucher->code,'returnUrl'=>$returnUrl));
					} else {
						$payment = new Payment;
						$payment->wallid = $wall->primaryKey;
						$payment->contactid = $contact->primaryKey;
						$payment->created = new CDbExpression('NOW()');
						$payment->title = $options[$option]['title'];
						$payment->code = $option;
						$payment->amount = 1;
						$payment->price = $options[$option]['price'];
						$payment->vat = Yii::app()->params['store']['vat'];
						$payment->trySave();
						$transaction->commit();
						return $this->redirect(array($this->id.'/pay','id'=>$payment->primaryKey,'returnUrl'=>$returnUrl));
					}
				} catch( Exception $ex ) {
					f($ex->getMessage(),'error');
				}
			}
		}
		
		if( $vouchercode ) {
			$this->pageTitle = g('Redeem Premium-wall');
		} else {
			$this->pageTitle = g('Purchase Premium-wall');
		}
		return $this->render('wall',compact('contact','returnUrl'));
	}
	
	public function actionRedeem($search, $vouchercode, $returnUrl=false) {
		if( $returnUrl === false ) {
			$returnUrl = array('store/payments');
		}
		
		$wall = Wall::model()->find('UPPER(name) LIKE UPPER(:name)',array(
			':name'=>$search
		));
		
		if( is_null($wall) ) {
			f(g('Could not find wall with name {name}',array('{name}'=>CHtml::encode($search))),'error');
			return $this->redirect($returnUrl);
		}
		
		$voucher = Voucher::model()->findByAttributes(array('code'=>$vouchercode));
		if( is_null($voucher) ) {
			f(g('Voucher code <em>{code}</em> could not be found, please check the code you entered.',array('{code}'=>CHtml::encode($vouchercode))),'error');
			return $this->redirect($returnUrl);
		}
		
		if( isset($_POST['cancel']) ) {
			return $this->redirect($returnUrl);
		} else	if( isset($_POST['redeem']) ) {
			$wall->publish($voucher->walllength,$voucher->wallremovedafter,true,$voucher->primaryKey,$voucher->wallsmscredit);
			return $this->redirect(array('wall/index','wall'=>$wall->name));
		}
		
		$contact = Yii::app()->user->client->Contact;
		$wall->Voucher = $voucher;
		return $this->render('redeem',compact('wall','voucher','contact'));
	}
	
	public function actionLicense() {
		// TODO
	}
	
	public function actionPayments($id=false) {
		if( $id ) {
			$payment = $this->getPayment($id);
			$this->pageTitle = g('Payment');
			return $this->render('pay',compact('payment'));
		} else {
			$payments = new CActiveDataProvider('Payment',array(
				'criteria'=>array(
					'condition'=>'contactid=:contactid AND paid IS NOT NULL',
					'params'=>array(
						':contactid'=>Yii::app()->user->client->Contact->primaryKey
					),
					'order'=>'created DESC'
				)
			));
			$pending = new CActiveDataProvider('Payment',array(
				'criteria'=>array(
					'condition'=>'contactid=:contactid AND paid IS NULL',
					'params'=>array(
						':contactid'=>Yii::app()->user->client->Contact->primaryKey
					),
					'order'=>'created DESC'
				)
			));
			$this->pageTitle = g('Account purchase history');
			return $this->render('payments',compact('payments','pending'));
		}
	}
	
	public function actionPay($id, $returnUrl=false) {
		$payment = $this->getPayment($id);
		$option = Yii::app()->params['store']['walls'][$payment->code];
		
		if( !$payment->confirmed || !$payment->paid ) {
			if( isset($_POST['cancel']) ) {
				$payment->delete();
				if( $returnUrl === false ) {
					$returnUrl = array('store/payments');
				}
				return $this->redirect($returnUrl);
			} else	if( isset($_POST['pay']) ) {
				$module = $this->getPaymentModule();
				$SVMurlset = new Verkkomaksut_Module_Rest_Urlset(
					$this->createAbsoluteUrl($this->id.'/payOk'),
					$this->createAbsoluteUrl($this->id.'/payCancel'),
					$this->createAbsoluteUrl($this->id.'/payNotify'),
					$this->createAbsoluteUrl($this->id.'/payPending')
				);
				$contact = $payment->Contact;
				$SVMcontact = new Verkkomaksut_Module_Rest_Contact(
					$contact->forname,
					$contact->surname,
					$contact->Client->email,
					$contact->street,
					$contact->zipcode,
					$contact->zip,
					$contact->country,
					$contact->phone,
					$contact->mobile,
					$contact->organization
				);
				$SVMpayment = new Verkkomaksut_Module_Rest_Payment_E1($payment->primaryKey,$SVMurlset,$SVMcontact);
				$SVMpayment->addProduct(
					$payment->title,
					$payment->primaryKey,
					$payment->amount,
					$payment->price,
					$payment->vat,
					$payment->discount,
					Verkkomaksut_Module_Rest_Product::TYPE_NORMAL
				);
				if( in_array(Yii::app()->locale,array("fi_FI", "en_US", "sv_SE")) ) {
					$SVMpayment->setLocale(Yii::app()->locale);
				}
				try {
					$result = $module->processPayment($SVMpayment);
					$payment->saveAttributes(array('confirmed'=>new CDbExpression('NOW()')));
					$this->redirect($result->getUrl());
				} catch( Exception $ex ) {
					f($ex->getMessage(),'error');
				}
			}
		}
		
		$this->pageTitle = g('Confirm purchase');
		$contact = $payment->Contact;
		return $this->render('pay',compact('payment','option','contact'));
	}
	
	public function actionPayOk() {
		$payment = $this->getPaymentFromResponse();
		$client = $payment->Contact->Client;
		
		if( $payment->paid ) {
			f(g('The payment has already been completed.'),'warning');
			return $this->redirect(array('store/payments'));
		}
		
		try {
			$transaction = Yii::app()->db->beginTransaction();
			$payment->saveAttributes(array('paid'=>new CDbExpression('NOW()')));
			if( $payment->Wall ) {
				if( !array_key_exists($payment->code,Yii::app()->params['store']['walls']) ) {
					throw new Exception(g('Invalid wall option selected, please contact support.'));
				}
				$option = Yii::app()->params['store']['walls'][$payment->code];
				$payment->Wall->publish($option['length'],$option['removedAfter'],true,null,$option['smscredit']);
			} else if( $payment->License ) {
				if( !array_key_exists($payment->code,Yii::app()->params['store']['licenses']) ) {
					throw new Exception(g('Invalid license option selected, please contact support.'));
				}
				$option = Yii::app()->params['store']['licenses'][$payment->code];
				$license = new License;
				$license->clientid = $client->primaryKey;
				$license->wallRemovedAfter = $option['wallRemovedAfter'];
				$license->expires = License::intervalDateExpression($option['length']);
				$license->trySave();
				$client->saveAttributes(array('smscredit'=>$client->smscredit + $option['smscredit']));
			} else {
				throw new Exception(g('Could not find Wall or License associated with this payment, please contact support.'));
			}
			f(g('Purchase completed.'),'success');
			$transaction->commit();
		} catch( Exception $ex ) {
			f(g('Error processing payment, please contact us for support').': '.$ex->getMessage(),'error');
			return $this->redirect(array('client/index'));
		}
		
		if( $client->sendMail('store/paid',g('Conexting wall purchased'),compact('payment')) ) {
			f(g('Confirmation of your purchase has been sent to your email address.'),'success');
		} else {
			f(g('Your purchase is successful, but confirmation email could not be sent to your email address. Please contact us if you require confirmation of purchase.'),'warning');
		}

		if( $payment->Wall ) {
			return $this->redirect(array('wall/index','wall'=>$payment->Wall->name));
		} else if( $payment->License ) {
			return $this->redirect(array('client/index'));
		}
		
		return $this->redirect(array('client/index'));
	}
	public function actionPayCancel() {
		$payment = $this->getPaymentFromResponse();
		$payment->saveAttributes(array('confirmed'=>null));
		Yii::app()->user->setFlash('error',g('Payment cancelled'));
		return $this->redirect(array($this->id.'/pay','id'=>$payment->primaryKey));
	}
	public function actionPayNotify() {
		$payment = $this->getPaymentFromResponse();
		$payment->saveAttributes(array('pending'=>false,'paid'=>new CDbExpression('NOW()')));
		// This action has no view
	}
	public function actionPayPending() {
		$payment = $this->getPaymentFromResponse();
		$payment->saveAttributes(array('pending'=>true));
		return $this->redirect(array($this->id.'/pay','id'=>$payment->primaryKey));
	}
	
	public function actionConfirmed($id) {
		$payment = $this->getPayment($id);
		$this->pageTitle = g('Confirmed purchase');
		return $this->render('confirmed',compact('payment'));
	}
	
	protected function getPayment($id) {
		$payment = Payment::model()->findByPk($id);
		if( is_null($payment) ) {
			throw new CHttpException(404,g('Payment could not be found'));
		}
		// Check that current client has permission to access this payment
		if( Yii::app()->user->client->primaryKey != $payment->Contact->Client->primaryKey ) {
			$this->redirect(array($this->id.'/index'));
		}
		return $payment;
	}
	
	protected function getPaymentModule() {
		Yii::import('ext.suomenverkkomaksut.*');
		return new Verkkomaksut_Module_Rest(
			Yii::app()->params['store']['merchantId'],
			Yii::app()->params['store']['merchantSecret']
		);
	}
	
	protected function getPaymentFromResponse() {
		$module = $this->getPaymentModule();
		if( $module->confirmPayment($_GET['ORDER_NUMBER'],$_GET['TIMESTAMP'],$_GET['PAID'],$_GET['METHOD'],$_GET['RETURN_AUTHCODE']) ) {
			$payment = Payment::model()->findByPk($_GET['ORDER_NUMBER']);
			if( is_null($payment) ) {
				throw new Exception(Yii::t('ui','Cannot find payment information, please contact the service provider'),0);
			}
			return $payment;
		} else {
			throw new Exception('Invalid payment',0);
		}
	}
}
