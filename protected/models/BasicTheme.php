<?php
class BasicTheme extends Theme {
	public $description = null;
	public $backgroundColor = '#fff';
	public $backgroundImage = 'default';
	public $titleColor = '#222';
	public $messageBackgroundColor = '#fff';
	public $messageTextColor = '#222';
	public $messageLinkColor = '#81BB42';
	public $logoFile = null;
	public $logoPosition = 'center';
	public $logoSpan = '12';
	public $logoPaddingTop = '20';
	public $logoUrl = null;
	public $conversationTitle = null;
	public $_font = null;
	public $fontSize = 14;
	public $disableBrandLink = false;
	public $disableMainConversation = false;
	public $removeLogoFile = false;
	public $showUserImages = true;
	public $showTimestamps = true;
	
	private $saving = false;
	
	public function init() {
		parent::init();
		$this->conversationTitle = g('Conversation');
	}
	
	public function rules() {
		return array(
			array('description','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			array('backgroundColor,titleColor,messageBackgroundColor,messageTextColor,messageLinkColor','match','pattern'=>'/#[a-f0-9]{3,6}/i'),
			array('backgroundImage','in','range'=>$this->getBackgroundImageNames()),
			array('logoFile','file','types'=>array('jpg','png','gif'),'allowEmpty'=>true),
			array('logoPosition','in','range'=>array('left','center','right')),
			array('logoSpan','numerical','integerOnly'=>true,'max'=>12,'min'=>1),
			array('logoPaddingTop','in','range'=>array('20px','0px')),
			array('conversationTitle','length','max'=>255),
			array('font','in','range'=>array_keys(Yii::app()->params['fonts'])),
			array('fontSize','numerical'),
			array('disableBrandLink,disableMainConversation,removeLogoFile,showUserImages,showTimestamps','boolean'),
			
			array('logoUrl','safe','on'=>'dbload,dbsave'), // Just for loading from the db
			array('logoUrl,removeLogoFile','unsafe','on'=>'less'), // logoFile is not less-safe variable, only to be used in layout
			array('removeLogoFile','unsafe','on'=>'dbsave'), // These are not saved to the database
		);
	}
	
	public function attributeLabels() {
		return array(
			'description' => g('Description text'),
			'titleColor' => g('Title color'),
			'messageBackgroundColor' => g('Message background color'),
			'messageTextColor' => g('Message text color'),
			'messageLinkColor' => g('Message link color'),
			'logoUrl' => g('Image'),
			'logoFile' => g('Upload new image'),
			'logoPosition' => g('Image position'),
			'logoPaddingTop' => g('Logo top spacing'),
			'logoSpan' => g('Logo span'),
			'conversationTitle' => g('Conversation title'),
			'font' => g('Font'),
			'fontSize' => g('Font size'),
			'disableBrandLink' => g('Disable brand link'),
			'disableMainConversation' => g('Disable main conversation'),
			'showUserImages' => g('Show sender\'s profile image when available'),
			'showTimestamps' => g('Show timestamp on messages'),
		);
	}
	
	protected function getBackgroundImageNames() {
		$dir = new DirectoryIterator(Yii::app()->basePath.'/../images/bg');
		$names = array();
		foreach ($dir as $fileinfo) {
			if( $fileinfo->getExtension() == 'png' ) {
				$names[] = $fileinfo->getBasename('.png');
			}
		}
		return $names;
	}
	
	public function save() {
		if( $this->removeLogoFile ) {
			$this->logoUrl = null;
		}
		$file = CUploadedFile::getInstance($this,'logoFile');
		if( !is_null($file) ) {
			$path = $this->getPath().$file->name;
			$url = Yii::app()->request->baseUrl.'/'.$path;
			$file->saveAs($path);
			$this->logoUrl = $url;
		}
		$this->saving = true;
		parent::save();
		$this->saving = false;
	}
	
	public function setFont($value) {
		$this->_font = $value;
	}
	
	public function getFont() {
		if( $this->saving ) {
			return "'".trim($this->_font,"'")."'";
		} else {
			return trim($this->_font,"'");
		}
	}
}
