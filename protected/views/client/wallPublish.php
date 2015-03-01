<h1><?php t('Publish wall'); ?></h1>
<?php
if( $isFree ) {
	$publishLabel = g('Publish now');
} else {
	$publishLabel = g('Purchase now');
}
?>

<div class="purchaseOptions">
	<p>
		<strong><?php t('Wall title'); ?></strong>
		<?php echo CHtml::encode($wall->displayTitle); ?>
	</p>
	<div class="row-fluid">
		<?php foreach( $options as $key => $option ) { ?>
		<div class="span4 offset1 text-center option">
			<h2><?php t($option['title']); ?></h2>
			<?php if( $wall->hasPremiumFeatures && !$wall->premium ) { ?>
			<p>
				<strong><?php t('Price'); ?>:</strong>
				<?php echo $option['price']; ?>€
			</p>
			<?php } ?>
			<p>
				<?php $this->widget('bootstrap.widgets.TbButton',array('size'=>'large','buttonType'=>'link','block'=>true,'type'=>'primary','encodeLabel'=>false,'label'=>$publishLabel,'url'=>$this->createUrl('',array('search'=>$wall->name,'option'=>$key)))); ?>
			</p>
		</div>
		<?php } ?>
	</div>
</div>

<h2><?php t('Redeem voucher'); ?></h2>
<p><?php t('If you have a voucher code, you can publish your wall by entering the code here.'); ?></p>
<?php echo CHtml::beginForm('','post',array('role'=>'form','class'=>'form form-inline')); ?>
<?php echo CHtml::textField('voucher','',array('class'=>'input-small','placeholder'=>g('Voucher code'))); ?>
<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','type'=>'primary','label'=>g('Redeem voucher'))); ?>
<?php echo CHtml::endForm(); ?>
