<p>
	<?php t('Your Conexting wall {name} has soon been expired for three months and is about to be removed in {days} days.',array('{name}'=>CHtml::link($wall->name,Yii::app()->createAbsoluteUrl('wall/index',array('wall'=>$wall->name,'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'WallName'))),'{days}'=>Yii::app()->params['wallRemovalNotificationDays'])); ?>
</p>
<p>
	<?php t('If you would like to continue using the wall, you can <a href="{url}">extend the wall use</a>.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/extendWall',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'ExtendWallUse')))); ?>
	<?php t('Otherwise you can ignore this email.'); ?>
</p>