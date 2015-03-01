<?php
/* @var $form TbActiveForm */
/* @var $theme BasicTheme */

Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/colorpicker.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/bootstrap-colorpicker.js');
Yii::app()->clientScript->registerScript('formColorPicker','
	$(".colorpicker").each(function(){
		$(this).colorpicker().on("changeColor",function(ev){
			$(this).val(ev.color.toHex());
			$(this).next(".add-on").css("backgroundColor",ev.color.toHex());
		}).next(".add-on").css("backgroundColor",$(this).val());
	});
');
Yii::app()->clientScript->registerScript('basicThemeRemoveImage','
	$(".removeImage").on("click",function(){
		$(this).closest(".currentImage").remove();
		$("#BasicTheme_removeLogoFile").val("1");
	});
');

Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/jquery.ui.fontSelector.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.ui.fontSelector.js');
Yii::app()->clientScript->registerScript('formFontPicker','
	$(".fontpicker").each(function(){
		$(this).fontSelector({
			fontChange: function () {
				$("span.handle").html("&#9660;");
				$("ul.fonts").slideUp();
			}
		});
	});
');
	
$theme = $wall->ThemeModel;

echo $form->textFieldRow($theme,'titleColor',array('class'=>'colorpicker','append'=>'&nbsp;'));
echo $form->textFieldRow($theme,'messageBackgroundColor',array('class'=>'colorpicker','append'=>'&nbsp;'));
echo $form->textFieldRow($theme,'messageTextColor',array('class'=>'colorpicker','append'=>'&nbsp;'));
echo $form->textFieldRow($theme,'messageLinkColor',array('class'=>'colorpicker','append'=>'&nbsp;'));
echo $form->checkBoxRow($theme,'showUserImages');
echo $form->checkBoxRow($theme,'showTimestamps');
if( $theme->logoUrl ) {
	?>
<div class="control-group currentImage">
	<div class="control-label"><?php t('Current image'); ?></div>
	<div class="controls">
		<?php echo CHtml::image($theme->logoUrl,basename($theme->logoUrl),array('class'=>'img-polaroid span6')); ?>
		<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'button','icon'=>'remove','htmlOptions'=>array('class'=>'removeImage'))); ?>
	</div>
</div>
	<?php
	echo $form->hiddenField($theme,'removeLogoFile');
}
echo $form->fileFieldRow($theme,'logoFile');
echo $form->radioButtonListRow($theme,'logoPosition',array('left'=>g('Left'),'center'=>g('Center'),'right'=>g('Right')));
echo $form->dropDownListRow($theme,'logoPaddingTop',array('20px'=>'20 px','0px'=>g('None')));
echo $form->dropDownListRow($theme,'logoSpan',array('12'=>g('Page width'),'11'=>g('Conversation width')));
echo $form->textFieldRow($theme,'conversationTitle');
echo $form->textAreaRow($theme,'description');
echo $form->dropDownListRow($theme,'font',Yii::app()->params['fonts'],array('class'=>'fontpicker'));
echo $form->dropDownListRow($theme,'fontSize',Yii::app()->params['fontSizes'],array('hint'=>g('Note that you can also zoom in and out with your browser (typically by pressing Ctrl and +/-).')));
echo $form->checkBoxRow($theme,'disableMainConversation',array('hint'=>g('Check this to hide the main conversation and use only questions and polls for interaction.')));
if( $this->isAdmin() ) {
	echo $form->checkBoxRow($theme,'disableBrandLink');
}
?>