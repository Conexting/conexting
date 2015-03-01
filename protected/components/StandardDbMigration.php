<?php
abstract class StandardDbMigration extends CDbMigration {
	public function up() {
		$this->createTables($this->getTables());
		$this->insertData($this->getTables());
		return true;
	}

	public function down() {
		$this->deleteData(array_reverse($this->getTables(),true));
		$this->dropTables(array_reverse($this->getTables(),true));
		return true;
	}
	
	protected abstract function getDir();
	
	protected function getTables() {
		return array();
	}
	
	protected function createTables($tables=array()) {
		echo 'Creating tables:'.PHP_EOL;
		$extra = "ENGINE=InnoDB COLLATE = 'utf8_swedish_ci'";
		$createdTables = array();
		try {
			foreach( $tables as $tableName ) {
				echo "Loading table schema for $tableName... ";
				$tableSchema = require("{$this->getDir()}{$tableName}.php");
				echo 'OK. Creating table... '.PHP_EOL;
				$this->createTable('{{'.$tableName.'}}',$tableSchema,$extra);
				$createdTables[] = $tableName;
				echo 'OK'.PHP_EOL;

				$filename = "{$this->getDir()}{$tableName}_keys.php";
				if( file_exists($filename) ) {				
					$keys = require($filename);
					echo "Creating ".count($keys)." keys for $tableName: ";
					foreach( $keys as $column => $params ) {
						$name = "{{{$tableName}}}_".strtr($column,',','_');
						echo "$name... ".PHP_EOL;
						$this->addForeignKey($name,'{{'.$tableName.'}}',$column,
							'{{'.$params[0].'}}',$params[1],$params[2]);
					}
					echo 'OK'.PHP_EOL;
				}
			}
		} catch(Exception $ex) {
			echo PHP_EOL."Error: ".$ex->getMessage().PHP_EOL;
			echo "Dropping all created tables".PHP_EOL;
			$this->dropTables(array_reverse($createdTables,true));
			echo PHP_EOL;
			throw $ex;
		}
		echo 'Tables created.'.PHP_EOL;
	}
	
	protected function insertData($tables=array()) {
		echo 'Inserting data:'.PHP_EOL;
		foreach( $tables as $tableName ) {
			echo "Reading data for table $tableName... ";
			$filename = "{$this->getDir()}{$tableName}_data.php";
			if( file_exists($filename) ) {
				$rows = require($filename);
				echo 'OK. Inserting '.count($rows).' rows: ';
				foreach( $rows as $i => $row ) {
					echo ($i+1).'... '.PHP_EOL;
					$this->insert('{{'.$tableName.'}}',$row);
				}
				echo 'OK'.PHP_EOL;
			} else {
				echo 'no data, skipping.'.PHP_EOL;
			}
		}
		echo 'Data inserted.'.PHP_EOL;
	}
	
	protected function dropTables($tables=array()) {
		echo 'Dropping tables:'.PHP_EOL;
		foreach( $tables as $tableName ) {
			echo "Dropping table $tableName... ".PHP_EOL;
			$this->dropTable('{{'.$tableName.'}}');
			echo 'OK'.PHP_EOL;
		}
		echo 'Tables dropped.'.PHP_EOL;
	}
	
	protected function deleteData($tables=array()) {
		// TODO
	}
}
