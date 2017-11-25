<?php 
class User{
	const TABLE_NAME = 'user';

	private $user = array();
	private $PDO = null;

	/**
	 * Constructeur
	 * @param PDO
	 */
	public function __construct($PDO){
		$this->PDO = $PDO;
	}

	/**
	 * Connexion de l'utilisateur
	 * @param string Identifiant
	 * @param string Mot de passe
	 * @return boolean
	 */
	public function Login($login, $password){
		$return = false;

		if(empty($login) || empty($password)){
			return $return;
		}

		// $password = password_hash($password, PASSWORD_BCRYPT);

		$request = ' SELECT * FROM `'.self::TABLE_NAME.'` WHERE `login` = :login LIMIT 1';

		$params = array(
			':login' => $login
		);
		$req = $this->PDO->prepare($request);
		if(!$req->execute($params)){
			return $return;
		}
		$user = $req->fetch(PDO::FETCH_ASSOC);

		if(password_verify($password, $user['password'])){
			$this->user = $user;
			$return = true;
		}

		return $return;
	}

	/*
	 * Return les informations de l'utilisateur
	 * @return array
	 */
	public function GetDatas(){
		return $this->user;
	}
}