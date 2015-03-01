<h2><?php t('Wall details'); ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$record,
  'nullDisplay'=>'<span class="muted">-</span>',
	'attributes'=>array(
    'name',
  ),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Edit wall'),
	'type'=>'buttonLink',
	'size'=>'small',
	'url'=>$this->createUrl('admin/wall',array('id'=>$record->primaryKey))
)); ?>
