<?php
class Strings{
	private $strings = array(
		'error-request_save'	=> 'Une erreur est survenue lors de l\'enregistrement de votre demande, merci de réessayer plus tard',
		'error-office_number' 	=> 'le numéro de bureau',
		'error-name' 			=> 'votre nom',
		'error-category' 		=> 'la catégorie de votre problème',
		'error-detail' 			=> 'la description de votre problème',
		'request_category-one_or_many_computers' => 'Il y a un problème sur un ou plusieurs oridinateurs',
		'request_category-all_computers' => 'Il y a un problème sur tous les oridinateurs',
		'request_category-printer' => 'Il y a un problème sur l\'imprimmante',
		'request_category-other' => 'Il y a un problème non repertorié',
		'request_status-waiting' => 'en attente',
		'request_status-progress' => 'en cours',
		'request_status-solved' => 'réparé',
		'error-login' => 'Vos identifiants sont incorrects. Merci de réessayer.',
		'error-update_request_status' => 'Le statut de la demande n\'a pas pu être mis à jour',
	);

	public function Get(string $string){
		return $this->strings[$string];
	}
}
