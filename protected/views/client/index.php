<?php if( $clientWallsTotal == 0 ) { ?>
	<h1><?php t('Start Connexting Now!'); ?></h1>
	<div class="text-center">
		<?php $this->widget('bootstrap.widgets.TbButton',array(
					'buttonType'=>'link',
					'type'=>'primary',
					'size'=>'large',
					'encodeLabel'=>false,
					'url'=>$this->createUrl('createWall'),
					'label'=>'<i class="fa fa-comments-o fa-2x"></i> '.g('Create your first Conexting wall'),
				)); ?>
	</div>
<?php } else { ?>
<h1><?php t('Active walls'); ?></h1>
<div class="row-fluid">
	<div class="span9">
		<div class="wallListContainer">
			<?php
			if( $data->itemCount > 0 ) {
				$this->widget('bootstrap.widgets.TbListView',array(
					'dataProvider'=>$data,
					'itemView'=>'_wallView',
					'itemsCssClass'=>'wallList',
					'summaryText'=>false,
				));
			} else if( $clientWallsTotal > 0 ) {
				echo '<p class="alert alert-info">'.g('You have currently no active Conexting walls.').'</p>';
			}
			?>
		</div>
	</div>
	<div class="span3">
		<?php $this->widget('bootstrap.widgets.TbButton',array(
			'buttonType'=>'link',
			'type'=>'primary',
			'label'=>'<i class="fa fa-comment-o fa-2x"></i><i class="fa fa-plus icon-small"></i><br />'.g('New Conexting wall'),
			'encodeLabel'=>false,
			'url'=>array('wallCreate'),
			'block'=>true,
			'size'=>'large'
		)); ?>
	</div>
</div>
<?php } ?>
