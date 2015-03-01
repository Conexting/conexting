<h2><?php t('SMS details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
		'timestamp'=>array(
			'value'=>date('j.n.Y',$sms->timestamp)
		),
    'operator',
    'source',
		'destination',
		'keyword',
		'header',
		'text',
		'binary',
  ),
)); ?>
