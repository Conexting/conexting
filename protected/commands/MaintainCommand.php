<?php
class MaintainCommand extends ConsoleCommand {
	public function actionRemoveDeadWalls() {
		$transaction = Yii::app()->db->beginTransaction();
		$wallsToRemove = Wall::model()->findAll('deleted IS NULL AND dies < NOW()');
		$removedCount = 0;
		if( !empty($wallsToRemove) ) {
			echo 'Preparing to remove '.count($wallsToRemove).' dead walls'.PHP_EOL;
			foreach( $wallsToRemove as $wall ) {
				echo 'Removing wall "'.$wall->name.'"... ';
				$wall->delete();
				echo 'OK'.PHP_EOL;
				$removedCount++;
			}
			$transaction->commit();
			echo 'Removed '.$removedCount.' walls'.PHP_EOL;
		}
		$this->notifyWalls($wallsToRemove,'client/wallRemoved','Conexting wall {name} has been removed');
	}
	
	public function actionNotifyBeforeWallDies() {
		// Notify walls that are going to be removed in X days (limiting to those that have existed for at least X days)
		$wallsToNotify = Wall::model()->findAll('deleted IS NULL AND published IS NOT NULL AND DATEDIFF(dies,NOW()) = :daystoremove AND DATEDIFF(dies,created) >= :lifetime',array(
			':daystoremove'=>Yii::app()->params['cnxConfig']['notifyBeforeWallDiesDays'],
			':lifetime'=>Yii::app()->params['cnxConfig']['notifyBeforeWallDiesMinLifetime'],
		));
		$this->notifyWalls($wallsToNotify,'client/wallToBeRemoved','Conexting wall {name} is about to be removed');
	}
	
	public function actionNotifyWhenWallExpires() {
		// Notify walls that are expiring today (limiting to those that have been usable for at least X days)
		$wallsToNotify = Wall::model()->findAll('deleted IS NULL AND DATE(expires) = DATE(NOW()) AND DATEDIFF(expires,published) >= :usetime',array(
			':usetime'=>Yii::app()->params['cnxConfig']['notifyWhenWallExpiresMinUsetime']
		));
		$this->notifyWalls($wallsToNotify,'client/wallExpires','Conexting wall {name} expires today');
	}
	
	public function actionNotifyUnpublishedWallRemoved() {
		// Notify unpublished walls that are going to be removed in X days
		$wallsToNotify = Wall::model()->findAll('deleted IS NULL AND published IS NULL AND DATEDIFF(dies,NOW()) = :daystoremove',array(
			':daystoremove'=>Yii::app()->params['cnxConfig']['notifyUnpublishedWallRemovedDays'],
		));
		$this->notifyWalls($wallsToNotify,'client/wallUnpublishedToBeRemoved','Conexting wall {name} is about to be removed');
	}
	
	protected function notifyWalls($wallsToNotify,$notificationMail,$subject) {
		$notifiedCount = 0;
		$defaultLanguage = Yii::app()->language;
		if( !empty($wallsToNotify) ) {
			echo 'Notifying '.count($wallsToNotify).' walls with '.$notificationMail.PHP_EOL;
			foreach( $wallsToNotify as $wall ) {
				if( $wall->Client->language ) {
					Yii::app()->language = $wall->Client->language;
				} else {
					Yii::app()->language = $defaultLanguage;
				}
				echo 'Sending notification to wall "'.$wall->name.'" owner '.$wall->Client->email.'... ';
				if( $wall->Client->sendMail($notificationMail,g($subject,array('{name}'=>$wall->name)),compact('wall')) ) {
					echo 'OK'.PHP_EOL;
					$notifiedCount++;
				} else {
					echo 'Error sending email, notification skipped!'.PHP_EOL;
				}
			}
			echo 'Sent '.$notifiedCount.' notifications'.PHP_EOL;
		}
	}
}
