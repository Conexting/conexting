<?php
t('Your Conexting wall {name} has soon been expired for three months and is about to be removed in {days} days.',array('{name}'=>'"'.$wall->name.'"','{days}'=>Yii::app()->params['wallRemovalNotificationDays']));
echo PHP_EOL.PHP_EOL;
t('If you would like to continue using the wall, you can extend the wall use using the following url: {url}',array('{url}'=>Yii::app()->createAbsoluteUrl('client/extendWall',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'ExtendWallUse'))));
echo PHP_EOL;
t('Otherwise you can ignore this email.');
