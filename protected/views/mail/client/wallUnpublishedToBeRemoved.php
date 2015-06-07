<p>
	<?php t('You have not published your Conexting wall {name}.',array('{name}'=>'<em>'.CHtml::encode($wall->name).'</em>')); ?>
	<?php t('The wall will be removed on {date} if it is not published or updated before that date.',array('{date}'=>$wall->dyingDate)); ?>
</p>
