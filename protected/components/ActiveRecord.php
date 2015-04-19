<?php
class ActiveRecord extends CActiveRecord {
	public static function intervalDateExpression($interval,$start='NOW()') {
		return new CDbExpression('DATE('.$start.') + INTERVAL '.$interval.' + INTERVAL 1 DAY - INTERVAL 1 SECOND');
	}
	
	public static function intervalDate($interval,$startDate=null) {
		$endOfDay = new DateTime();
		if( !is_null($startDate) && $startDate > time() ) {
			$endOfDay->setTimestamp($startDate);
		}
		$endOfDay->setTime(23, 59, 59);
		return date_add($endOfDay,date_interval_create_from_date_string($interval));
	}
	
	public function search($criteria=array(),$defaultSort=null) {
		$this->searchAttributes();
		$criteria = $this->searchCriteria(new CDbCriteria($criteria));
		$sort = $this->searchSort(new CSort(get_class($this)));
		if( !is_null($defaultSort) ) {
			$sort->defaultOrder = $defaultSort;
		}
		return new CActiveDataProvider(get_class($this),array(
			'criteria' => $criteria,
			'sort' => $sort,
		));
	}
	
	protected function searchAttributes() {
		if( $_REQUEST[get_class($this)] ) {
			$this->attributes = $_REQUEST[get_class($this)];
		}
	}
	
	/**
	 * Add search criteria for filtering a search
	 * @param CDbCriteria $criteria
	 * @return CDbCriteria 
	 */
	protected function searchCriteria($criteria) {
		foreach( $this->safeAttributeNames as $key ) {
			if( isset($_REQUEST[get_class($this)][$key]) ) {
				$criteria->compare($key,$this->$key);
			}
		}
		return $criteria;
	}
	
	/**
	 * Add sorting parameters for a search
	 * @param CSort $sort
	 * @return CSort 
	 */
	protected function searchSort($sort) {
		return $sort;
	}

	public function trySave($runValidation=true,$attributes=null) {
		if( !$this->save($runValidation,$attributes) ) {
			throw new Exception(g('Error saving {class}',array('{class}'=>get_class($this))));
		}
		return $this;
	}
	
	protected function beforeValidate() {
		$this->formatEmptyToNull();
		return parent::beforeValidate();
	}
	
	protected function beforeSave() {
		$this->formatDatesToSQL();
		return parent::beforeSave();
	}
	
	protected function afterSave() {
		$this->formatDatesToInt();
	}
	
	protected function afterFind() {
		$this->formatDatesToInt();
		return parent::afterFind();
	}
	
	private function formatDatesToSQL() {
		$table = $this->getTableSchema();
		foreach( $table->columns as $name => $column ) {
			if( $this->$name ) {
				if( $column->dbType == 'datetime' || $column->dbType == 'date' || $column->dbType == 'timestamp' ) {
					if( $this->$name instanceof CDbExpression ) {
						continue;
					} elseif( is_numeric($this->$name) ) {
						$time = $this->$name;
					} else {
						$time = strtotime($this->$name);
					}
					if( $column->dbType == 'datetime' || $column->dbType == 'timestamp' ) {
						$this->$name = date('Y-m-d H:i:s',$time);
					} else if( $column->dbType == 'date' ) {
						$this->$name = date('Y-m-d',$time);
					}
				}
			}
		}
	}
	
	private function formatDatesToInt() {
		$table = $this->getTableSchema();
		foreach( $table->columns as $name => $column ) {
			if( $this->$name ) {
				if( $column->dbType == 'datetime' || $column->dbType == 'date' || $column->dbType == 'timestamp' ) {
					$this->$name = strtotime($this->$name);
				}
			}
		}
	}
	
	private function formatEmptyToNull() {
		$table = $this->getTableSchema();
		foreach( $table->columns as $name => $column ) {
			if( $this->$name === '' ) {
				$this->$name = null;
			}
		}
	}
}
