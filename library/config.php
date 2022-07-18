<?php

namespace hobotix;

final class Config {

	public function __construct($config){
		$this->loadPHP($config);

		foreach (config_data() as $key => $value){
			$this->{$key} = $value;			
		}
	}

	public static function loadPHP($config){
		if (file_exists($file = dirname(__FILE__) . '/../configs/php/' . $config . '.php')){
			require_once($file);
		} else {

			echoLine('Could not load php config ' . $config);
			die();	

		}
	}

	public static function loadJSON($config){
		if (file_exists($file = dirname(__FILE__) . '/../configs/json/' . $config . '.json')){
			$json = file_get_contents($file);	
			return json_decode($json, true); 
		}

		return false;
	}

	//alias
	public function gs($setting){
		return $this->getSetting($setting);		
	}

	public function getSetting($setting){
		return $this->{$setting};				
	}
}