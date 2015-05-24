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
			<p>
				<?php t('These are Premium features.'); ?>
				<?php t('You can try all these features for free.'); ?>
				<?php t('When you publish a wall with premium features you can select a Premium-product that best suits your requirements.'); ?>
			</p>
			<ul>
				<li><?php t('SMS-messages and SMS-voting'); ?></li>
				<li><?php t('Pre-moderate messages'); ?></li>
				<li><?php t('No signing in required for Twitter'); ?></li>
			</ul>
			<p>
				<?php
				$firstOption = reset(Yii::app()->params['store']['walls']);
				t('Premium-wall from {n} €.',$firstOption['price']);
				?>
				<?php t('See the <a href="{url}" target="_blank">features-page</a> for full list of features and pricing.',array('{url}'=>$this->createUrl('info/features'))); ?>
			</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal"><?php t('Ok'); ?></button>
		</div>
	<?php $this->endWidget('premiumInfo'); ?>
</div>
<?php $this->endContent(); ?>