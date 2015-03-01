<?php
$baseUrl = Yii::app()->request->baseUrl;
$this->cssFile('engine');
?><?php $this->beginContent('application.views.layouts.base'); ?>
<div class="header">
	<?php $this->widget('bootstrap.widgets.TbNavbar',CMap::mergeArray(array(
		'brand'=>CHtml::image($baseUrl.'/images/conexting_small.png','Conexting')
	),array(
		'collapse'=>false,
		'items'=>$this->getNavItems(),
		'htmlOptions'=>array(
			'class'=>'nav'
		),
		'fixed'=>false,
	))); ?>
</div>
<div class="content span10 offset1">
	<?php $this->widget('bootstrap.widgets.TbAlert'); ?>
	<?php echo $content; ?>
	<?php $this->beginWidget('bootstrap.widgets.TbModal',array(
		'id'=>'premiumInfo',
		'options'=>array(
			'header'=>g('Premium social wall features')
		),
	)); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h3><?php t('Premium features'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php t('For a complete list of features see:'); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal"><?php t('Ok'); ?></button>
		</div>
	<?php $this->endWidget('premiumInfo'); ?>
</div>
<?php $this->endContent(); ?>