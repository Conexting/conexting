<p>
	<?php t('Your Conexting wall {name} has been expired since {date} and will be removed in {days} days.',array('{name}'=>CHtml::link($wall->name,Yii::app()->createAbsoluteUrl('wall/index',array('wall'=>$wall->name,'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'WallName'))),'{date}'=>$wall->expirationDate,'{days}'=>Yii::app()->params['cnxConfig']['notifyBeforeWallDiesDays'])); ?>
</p>
<p>
	<?php t('If you would like to still use the wall, you can <a href="{url}">extend the wall use</a>.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/wallPublish',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'ExtendWallUse')))); ?>
	<?php t('Otherwise you can ignore this email.'); ?>
</p>