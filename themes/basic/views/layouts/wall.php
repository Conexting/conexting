<?php
if( $this->wall->ThemeModel->font ) {
	if( preg_match('/^_/',Yii::app()->params['fonts'][$this->wall->ThemeModel->font]) ) {
		Yii::app()->clientScript->registerCssFile('http://fonts.googleapis.com/css?family='.urlencode(trim($this->wall->ThemeModel->font,"'")));
	}
}
?>
<?php $this->beginContent('application.views.layouts.base'); ?>
<div class="header hidden-phone hidden-print">
	<?php
	if( $this->wall->premium ) {
		$imgname = 'conexting_premium_small';
	} else {
		$imgname = 'conexting_small';
	}
	$brand = CHtml::image(Yii::app()->request->baseUrl."/images/$imgname.png",'Conexting');
	
	if( !Yii::app()->user->isGuest ) {
		$this->widget('bootstrap.widgets.TbNavbar',CMap::mergeArray(array(
			'brand'=>$brand
		),array(
			'items'=>$this->getNavItems(),
			'collapse'=>false,
			'fixed'=>false,
			'htmlOptions'=>array(
				'class'=>'nav engine-nav'
			)
		)));
		$brand = ''; // Do not repeat brand on second navbar
	}
	
	$this->widget('bootstrap.widgets.TbNavbar',array(
		'brand'=>$brand,
		'brandUrl'=>$this->wall->ThemeModel->disableBrandLink ? false : $this->createUrl('site/index'),
		'items'=>$this->getWallNavItems(),
		'fixed'=>false,
		'htmlOptions'=>array(
			'class'=>'nav wall-nav'
		)
	));
	?>
</div>
<div class="print-header">
	<div class="conexLogo"><?php echo CHtml::image(Yii::app()->request->baseUrl."/images/$imgname.png",'Conexting'); ?></div>
</div>
<?php if( $this->wall->ThemeModel->logoUrl ) { ?>
<div class="logo span<?php echo $this->wall->ThemeModel->logoSpan; ?> offset<?php echo floor((12 - $this->wall->ThemeModel->logoSpan) / 2); ?>">
	<?php echo CHtml::image($this->wall->ThemeModel->logoUrl,'',array('class'=>'')); ?>
</div>
<?php } ?>
<div class="header visible-phone hidden-print">
	<div class="conexLogo"><?php echo CHtml::image(Yii::app()->request->baseUrl."/images/$imgname.png",'Conexting'); ?></div>
	<?php
	$items = $this->getWallNavItems(20,false);
	if( count($items['wall']['items']) > 1 ) {
		$this->widget('bootstrap.widgets.TbMenu',array(
			'type'=>'pills',
			'items'=>$items['wall']['items'],
		));
	}
	?>
</div>
<div class="content span11 offset0">
	<?php $this->widget('bootstrap.widgets.TbAlert',array(
		'block'=>true,
		'fade'=>true,
		'closeText'=>'&times;',
	)); ?>
	<?php echo $content; ?>
</div>
<?php $this->endContent(); ?>