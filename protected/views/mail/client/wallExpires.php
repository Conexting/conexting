<p>
	<?php t('Your Conexting wall {name} is expiring today at {time}.',array(
			'{name}'=>CHtml::link($wall->name,Yii::app()->createAbsoluteUrl('wall/index',array(
				'wall'=>$wall->name,
				'utm_medium'=>'email',
				'utm_source'=>$utm_source,
				'utm_campaign'=>$utm_campaign,
				'utm_content'=>'WallName'
			))),
			'{time}'=>$wall->getExpirationTime('H:i')
		)
	); ?>
	<?php t('No new messages or votes can be received after the wall has expired.'); ?>
	<?php t('The contents of the wall will be online until {date}.',array('{date}'=>$wall->dyingDate)); ?>
</p>
<p>
	<?php
	if( $wall->premium ) {
		$action = g('extend your Premium wall');
		$utm_content = 'ExtendWallUse';
	} else {
		$action = g('upgrade your wall to Premium');
		$utm_content = 'UpgradeToPremium';
	}
	t('If you would like to continue using the wall, you can {action}.',array(
		'{action}'=>CHtml::link($action,Yii::app()->createAbsoluteUrl('client/wallPublish',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>$utm_content)))
	));
	
	if( !$wall->premium ) {
		echo ' ';
		t('You can extend your Free wall without upgrading to Premium after it has been expired.');
	}
	?>
	<?php t('Otherwise you can ignore this email.'); ?>
</p>