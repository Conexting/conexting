<?php
require_once(dirname(__FILE__).'/lessphp/lessc.inc.php');
require_once(dirname(__FILE__).'/cssmin.php');

class YiiLess extends CApplicationComponent {
	public $paths = array();
	/**
	 * @var lessc
	 */
	public $_lessc = null;
	
	public function create() {
		return new lessc;
	}
	
	public function getLessc() {
		if( is_null($this->_lessc) ) {
			$this->_lessc = $this->create();
		}
		return $this->_lessc;
	}
	
	public function init() {
		parent::init();
		foreach( $this->paths as $out => $less ) {
			$outFile = $out.'.css';
			$outMinFile = $out.'.min.css';
			if( !is_file($outFile) || $less['modified'] > filemtime($outFile) ) {
				$this->getLessc()->compileFile($less['file'],$outFile);
			}
			if( !is_file($outMinFile) || $less['modified'] > filemtime($outMinFile) ) {
				//$minCss = CssMin::minify(file_get_contents($outFile));
				// Minifier breaks CSS, do not minify
				$minCss = file_get_contents($outFile);
				file_put_contents($outMinFile,$minCss);
			}
		}
	}
}
