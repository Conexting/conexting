<?php
t('Your Conexting wall {name} has been removed.',array('{name}'=>'"'.$wall->name.'"'));
echo PHP_EOL.PHP_EOL;
t('If you wish to use this wall again, you can log in to your account ({url}), undelete the wall and extend the wall use.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/index',array('utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'LogInToAccaunt'))));
echo PHP_EOL;
t('Otherwise you can ignore this email.');
