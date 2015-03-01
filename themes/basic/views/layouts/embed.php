<?php
if( $this->wall->ThemeModel->font ) {
	if( preg_match('/^_/',Yii::app()->params['fonts'][$this->wall->ThemeModel->font]) ) {
		Yii::app()->clientScript->registerCssFile('http://fonts.googleapis.com/css?family='.urlencode(trim($this->wall->ThemeModel->font,"'")));
	}
}
?>
<?php $this->beginContent('application.views.layouts.base'); ?>
<div class="header">
	<div class="conexLogo"><?php
	if( $this->wall->premium ) {
		$imgname = 'conexting_premium_small';
	} else {
		$imgname = 'conexting_small';
	}
	if( !$this->wall->ThemeModel->disableBrandLink ) {
		echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl."/images/$imgname.png",'Conexting'),$this->createUrl('site/index'));
	} else {
		echo CHtml::image(Yii::app()->request->baseUrl."/images/$imgname.png",'Conexting');
	}
	?></div>
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
