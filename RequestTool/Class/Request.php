<?php 
class Request{
	const TABLE_NAME = 'request';

	// Catégorie des demandes
	const CATEGORY_ONE_OR_MANY_COMPUTERS = 'one_or_many_computers';
	const CATEGORY_ALL_COMPUTERS = 'all_computers';
	const CATEGORY_PRINTER = 'printer';
	const CATEGORY_OTHER = 'other';

	// Statut des demandes
	const STATUS_WAITING = 'waiting';
	const STATUS_PROGRESS = 'progress';
	const STATUS_SOLVED = 'solved';

	private $PDO = null;

	// Données obligatoires pour soumettre une demande
	private $required_data = array(
		'office_number',
		'name',
		'category',
		'detail'
	);

	/**
	 * Constructeur
	 * @param PDO
	 */
	public function __construct($PDO){
		$this->PDO = $PDO;
	}

	/**
	 * Vérification des données
	 * @param array $datas Tableau de données
	 * @return array Tableau d'erreur (vide si les données sont bonnes)
	 */
	public function CheckDatas(array $datas){
		$return = array();
		foreach($this->required_data as $required_data){
			if(empty($datas[$required_data])){
				$return[] = $required_data;
			}else if($required_data == 'category'){
				if(!in_array($datas[$required_data], array(self::CATEGORY_ONE_OR_MANY_COMPUTERS, self::CATEGORY_ALL_COMPUTERS, self::CATEGORY_PRINTER, self::CATEGORY_OTHER))){
					$return[] = $required_data;
				}
			}
		}
		return $return;
	}

	/**
	 * Enregistrement de la demande en BDD
	 * @param array $datas Tableau de données
	 * @param int Identifiant de la demande insérée en bdd ou 0 si l'enregistrement a échoué
	 */
	public function Save(array $datas){
		$request = ' INSERT INTO `'.self::TABLE_NAME.'` (`office_number`, `name`, `category`, `computer_number`, `detail`, `status`) VALUES (:office_number, :name, :category, :computer_number, :detail, :status) ';

		$params = array(
			':office_number' => $datas['office_number'],
			':name' => $datas['name'],
			':category' => $datas['category'],
			':computer_number' => (empty($datas['computer_number'])?'':$datas['computer_number']),
			':detail' => $datas['detail'],
			':status' => self::STATUS_WAITING
		);
		$req = $this->PDO->prepare($request);
		if(!$req->execute($params)){
			return false;
		}

		return $this->PDO->lastInsertId();
	}

	/**
	 * Chargement d'une demande
	 * @param integer Identifiants de la requête
	 * @return array Talbeau des données de la demande ou tableau vide
	 */
	public function Load($request_id){
		$return = array();

		if(is_int($request_id) == false){
			return $return;
		}

		$request = ' SELECT * FROM `'.self::TABLE_NAME.'` WHERE `request_id` = :request_id LIMIT 1';

		$params = array(
			':request_id' => $request_id
		);

		$req = $this->PDO->prepare($request);
		if(!$req->execute($params)){
			return $return;
		}
		$return = $req->fetch(PDO::FETCH_ASSOC);

		return $return;
	}

	/**
	 * Retourne toutes les demandes
	 * return array
	 */
	public function GetAllRequest(){
		$return = array();

		$request = ' SELECT * FROM `'.self::TABLE_NAME.'` ORDER BY `date_creation` DESC ';
		$req = $this->PDO->prepare($request);
		if(!$req->execute()){
			return $return;
		}

		while($row = $req->fetch(PDO::FETCH_ASSOC)){
			$return[] = $row;
		}

		return $return;
	}

	/**
	 * Mise à jour du statut d'une demande
	 * @param int Identifiant de la demande
	 * @param string Nouveau status
	 * @param int Identifiant de l'utilisateur qui met à jour le statut
	 * @return boolean
	 */
	public function UpdateStatus($request_id, $status, $user_id){
		$return = false;

		if(!in_array($status, array(self::STATUS_WAITING, self::STATUS_PROGRESS, self::STATUS_SOLVED))){
			return $return;
		}

		$request = ' UPDATE `'.self::TABLE_NAME.'` SET `status` = :status, `user_id` = :user_id, `date_status` = NOW() WHERE `request_id` = :request_id LIMIT 1';

		$params = array(
			':status' => $status,
			':request_id' => intval($request_id),
			':user_id' => intval($user_id)
		);

		$req = $this->PDO->prepare($request);
		if(!$req->execute($params)){
			var_dump('test');
			return $return;
		}

		$return = true;

		return $return;
	}
}