<p>
	<?php t('Your Conexting wall {name} has been removed.',array('{name}'=>'<em>'.CHtml::encode($wall->name).'</em>')); ?>
</p>
<p>
	<?php t('If you wish to use this wall again, you can <a href="{url}">log in to your account</a>, undelete the wall and extend the wall use.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/index',array('utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'LogInToAccaunt')))); ?>
	<?php t('Otherwise you can ignore this email.'); ?>
</p>