<?php
if( $this->wall->ThemeModel->font ) {
	if( preg_match('/^_/',Yii::app()->params['fonts'][$this->wall->ThemeModel->font]) ) {
		Yii::app()->clientScript->registerCssFile('http://fonts.googleapis.com/css?family='.urlencode(trim($this->wall->ThemeModel->font,"'")));
	}
}
$baseUrl = Yii::app()->request->baseUrl;
$brand = CHtml::image($baseUrl.'/images/conexting_small.png','Conexting');
?><?php $this->beginContent('application.views.layouts.base'); ?>
<div class="header">
	<?php $this->widget('bootstrap.widgets.TbNavbar',CMap::mergeArray(array(
		'brand'=>CHtml::image($baseUrl.'/images/conexting_small.png','Conexting')
	),array(
		'items'=>$this->getWallNavItems(8),
		'collapse'=>false,
		'fixed'=>false,
		'htmlOptions'=>array(
			'class'=>'nav wall-nav'
		),
	))); ?>
</div>
<?php if( $this->wall->ThemeModel->logoUrl ) { ?>
<div class="logo">
	<?php echo CHtml::image($this->wall->ThemeModel->logoUrl,'',array('class'=>'')); ?>
</div>
<?php } ?>
<div class="content">
	<?php $this->widget('bootstrap.widgets.TbAlert',array(
		'block'=>true,
		'fade'=>true,
		'closeText'=>'&times;',
	)); ?>
	<?php echo $content; ?>
</div>
<?php $this->endContent(); ?>
<?php $this->jsFile('view-wall'); ?>