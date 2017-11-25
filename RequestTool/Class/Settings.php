<?php 

class Settings
{
	const PROJECT_NAME 	= 'WorldInfo';

	// BDD
	const BDD_HOST 		= 'localhost';
	const BDD_USER	 	= 'root';
	const BDD_PASSWORD 	= '';
	const BDD_NAME 		= 'request_tool';

	// Format des dates
	const DATETIME_FR = 'd/m/Y H:i:s';

	/**
	 * Connection à la base de donnée
	 */
	static public function BddConnect(){
		return new PDO('mysql:host='.self::BDD_HOST.';dbname='.self::BDD_NAME, self::BDD_USER, self::BDD_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
	}

	/**
	 * Formatage des dates
	 * @param string date
	 * @param string Format souhaité
	 * @return string Date formaté
	 */
	static public function FormatDate($date, $format){
		$date = new DateTime($date);
		return $date->format($format);
	}
}