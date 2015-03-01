<?php
class Theme extends CFormModel {
	const WALL_DIR = 'images/wall';
	public $conversationTitle;
	
	/**
	 * Create a theme instance for a wall
	 * @param Wall $wall 
	 */
	public static function create($wall) {
		$class = ucfirst($wall->theme).'Theme';
		$theme = new $class;
		$theme->wall = $wall;
		
		$currentScenario = $theme->scenario;
		$theme->scenario = 'dbload';
		$theme->attributes = $wall->getVars();
		$theme->scenario = $currentScenario;
		
		return $theme;
	}
	
	public $wall;
	
	public function save() {
		$currentScenario = $this->scenario;
		$this->scenario = 'dbsave';
		foreach( $this->safeAttributeNames as $attr ) {
			$var = $this->wall->getVar($attr);
			$var->value = $this->$attr;
			$var->trySave();
		}
		$this->scenario = $currentScenario;
	}
	
	public function getPath() {
		$dir = self::WALL_DIR.'/'.$this->wall->primaryKey;
		if( !is_dir($dir) ) {
			mkdir($dir);
		}
		return $dir.'/';
	}
	
	public function lessVariables() {
		$vars = array();
		$currentScenario = $this->scenario;
		$this->scenario = 'less';
		foreach( $this->safeAttributeNames as $attr ) {
			if( $this->$attr ) {
				$vars[$attr] = $this->$attr;
			}
		}
		$this->scenario = $currentScenario;
		return $vars;
	}
}
