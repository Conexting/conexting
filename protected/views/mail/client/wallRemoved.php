<p>
	<?php t('Your Conexting wall {name} has been expired for three months and has now been removed.',array('{name}'=>CHtml::link($wall->name,Yii::app()->createAbsoluteUrl('wall/index',array('wall'=>$wall->name,'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'WallName'))))); ?>
</p>
<p>
	<?php t('If you wish to use your wall again, you can <a href="{url}">log in to your account</a>, undelete the wall and extend the expiration.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/index',array('utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'LogInToAccaunt')))); ?>
	<?php t('Otherwise you can ignore this email.'); ?>
</p>