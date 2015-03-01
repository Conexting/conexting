<p>
	<?php t('Your Conexting wall {name} has been expired in {date} and will not receive any new messages.',array('{date}'=>date('j.n.Y',$wall->expires),'{name}'=>CHtml::link($wall->name,Yii::app()->createAbsoluteUrl('wall/index',array('wall'=>$wall->name,'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'WallName'))))); ?>
</p>
<p>
	<?php t('The contents of the wall will still be online for {days} days until {date}.',array('{days}'=>$wall->removedInDays,'{date}'=>date('j.n.Y',$wall->dies))); ?>
	<?php t('If you would like to continue using the wall, you can <a href="{url}">extend the wall use</a>.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/extendWall',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'ExtendWallUse')))); ?>
	<?php t('Otherwise you can ignore this email.'); ?>
</p>