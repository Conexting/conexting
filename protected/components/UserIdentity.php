<?php
class UserIdentity extends CUserIdentity {
	private $id;
	
	public function getId() {
		return $this->id;
	}
	
	public function setClient($client) {
		$this->id = $client->primaryKey;
		$this->username = $client->name;
	}

	public function authenticate() {
		$client = Client::model()->find('UPPER(name)=UPPER(:name)',array(':name'=>$this->username));
		if( is_null($client) ) {
			$client = Client::model()->find('UPPER(email)=UPPER(:email)',array(':email'=>$this->username));
		}
		
		$this->id = $client->primaryKey;
		
		if( !is_null($client) ) {
			$this->username = $client->name;
			return $client->validatePassword($this->password);
		}
		
		return false;
	}
}
