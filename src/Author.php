<?php

class Author {

	public $name;
	
	public $email;
	
	public $homepage;
	
	public function __construct(array $options=array()) {
		$this->setOptions($options);
	}
	
	public function setOptions(array $options) {
		foreach($options as $key => $value) {
			$this->setOption($key, $value);
		}
		return $this;
	}
	
	public function setOption($key, $value=null) {
		switch($key) {
			case 'name':
				$this->name = (string) $value;
				break;
				
			case 'email':
				$this->email = (string) $value;
				break;
				
			case 'homepage':
				$this->homepage = (string) $value;
				break;
				
			default:
				throw new Exception('Unknown option: ' . $key);
		}
		return $this;
	}
	
}