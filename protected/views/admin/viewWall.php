<h2><?php t('Wall details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
    'name',
		'title',
		array(
			'name'=>'created',
			'value'=>$record->creationTime,
		),
		array(
			'name'=>'modified',
			'value'=>$record->modificationTime,
		),
		array(
			'name'=>'published',
			'value'=>$record->publishingTime,
		),
		array(
			'name'=>'expires',
			'value'=>$record->expirationTime,
		),
		array(
			'name'=>'dies',
			'value'=>$record->dyingTime,
		),
		array(
			'name'=>'premium',
			'value'=>$record->premium ? g('Yes') : g('No')
		),
		array(
			'name'=>'hidden',
			'value'=>$record->hidden ? g('Yes') : g('No')
		),
		array(
			'name'=>'premoderated',
			'value'=>$record->premoderated ? g('Yes') : g('No')
		),
		array(
			'name'=>'sms',
			'value'=>$record->sms.' '.g('{n} pcs left',$record->smscredit)
		),
		array(
			'name'=>'hashtag',
			'value'=>$record->enabletwitter ? $record->twitter.' ('.($record->TwitterUser ? '@'.$record->TwitterUser->screen_name : g('shared')).')' : null,
		),
		array(
			'header'=>g('Content'),
			'value'=> g('{n} message|{n} messages',count($record->Messages))
				.', '.g('{n} question|{n} questions',count($record->Questions))
				.', '.g('{n} poll|{n} polls',count($record->Polls))
		)
  ),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Edit wall'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/wall',array('id'=>$record->primaryKey))
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Test notifications'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/wallNotify',array('id'=>$record->primaryKey))
)); ?>
