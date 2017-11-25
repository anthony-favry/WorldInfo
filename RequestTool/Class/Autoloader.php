<?php 

/**
* Autoloader
*/
class Autoloader{
	static function Autoload($class){
		require 'class/'.$class.'.php';
	}
}