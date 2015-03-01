<?php
t('Your Conexting wall {name} has been expired for three months and has now been removed.',array('{name}'=>'"'.$wall->name.'"'));
echo PHP_EOL.PHP_EOL;
t('If you wish to use your wall again, you can log in to your account ({url}), undelete the wall and extend the expiration.',array('{url}'=>Yii::app()->createAbsoluteUrl('client/index',array('utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'LogInToAccaunt'))));
echo PHP_EOL;
t('Otherwise you can ignore this email.');
