<?php
require 'Init.php';
$strings = new Strings();
$errors = array();
$error_message = "";
$post_datas = array();
$step = 1;
$request = array();

if(empty($_GET['request_id']) == false){
	$Request = new Request($PDO);
	$request = $Request->load(intval($_GET['request_id']));
	$step = 4;
}

if(isset($_POST['send'])){
	switch($_POST['action']){
		case 'send':
			$Request = new Request($PDO);
			$_SESSION['request'] = array(
				'name' => $_POST['name'],
				'office_number' => $_POST['office_number'],
				'category' => $_POST['category'],
				'computer_number' => $_POST['computer_number'],
				'detail' => $_POST['detail']
			);
			$errors = $Request->CheckDatas($_SESSION['request']);
			if(empty($errors) == false){
				$step = 1;
			}else{
				$step = 2;
			}
		break;
		case 'edit':
			$step = 1;
		break;
		case 'save':
			if(empty($_SESSION['request']) == false){
				$Request = new Request($PDO);
				$request_id = $Request->Save($_SESSION['request']);

				if($request_id){
					$step = 3;
					$_SESSION['request'] = array();
				}else{
					$step = 1;
					$error_message = $strings->Get('error-request_save');
				}
			}
		break;
	}
}

?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title><?php echo Settings::PROJECT_NAME;?></title>
</head>
<body>
	<?php switch($step){
		case 1:?>
			<h1>Demande d'intervention</h1>
			<?php if(empty($error_message) == false){?>
				<p style="color:#FF0000;"><?php echo $error_message;?></p>
			<?php }?>
			<?php if(empty($errors) == false){?>
				<p style="color:#FF0000;">
					<?php if(count($errors) == 1){?>
						<b>La donnée suivante est manquante ou erronée :</b>
					<?php }else{?>
						<b>Les données suivantes sont manquantes ou erronées :</b>
					<?php }?>
					<ul>
						<?php foreach($errors as $error){?>
							<li style="color:#FF0000;"><?php echo $strings->Get('error-'.$error);?></li>
						<?php }?>
					</ul>
				</p>
			<?php }?>
			<form method="POST" action="">
				<br /><label>Votre nom : </label><input type="text" name="name" value="<?php echo (empty($_SESSION['request']['name'])?'':$_SESSION['request']['name'])?>" />
				<br /><label>Bureau n° : </label><input type="text" name="office_number" value="<?php echo (empty($_SESSION['request']['office_number'])?'':$_SESSION['request']['office_number'])?>" />
				<br /><label>Votre problème concerne :</label>
				<ul>
					<li>
						<input id="category-<?php echo Request::CATEGORY_ONE_OR_MANY_COMPUTERS;?>" type="radio" name="category" <?php echo (empty($_SESSION['request']['category']) == false && $_SESSION['request']['category'] == Request::CATEGORY_ONE_OR_MANY_COMPUTERS?'checked=""':'');?> value="<?php echo Request::CATEGORY_ONE_OR_MANY_COMPUTERS;?>" />
						<label for="category-<?php echo Request::CATEGORY_ONE_OR_MANY_COMPUTERS;?>">Un ou plusieurs ordinateur(s)</label>
					</li>
					<li>
						<input id="category-<?php echo Request::CATEGORY_ALL_COMPUTERS;?>" type="radio" name="category" <?php echo (empty($_SESSION['request']['category']) == false && $_SESSION['request']['category'] == Request::CATEGORY_ALL_COMPUTERS?'checked=""':'');?> value="<?php echo Request::CATEGORY_ALL_COMPUTERS;?>" />
						<label for="category-<?php echo Request::CATEGORY_ALL_COMPUTERS;?>">Tous les ordinateurs</label>
					</li>
					<li>
						<input id="category-<?php echo Request::CATEGORY_PRINTER;?>" type="radio" name="category" <?php echo (empty($_SESSION['request']['category']) == false && $_SESSION['request']['category'] == Request::CATEGORY_PRINTER?'checked=""':'');?> value="<?php echo Request::CATEGORY_PRINTER;?>" />
						<label for="category-<?php echo Request::CATEGORY_PRINTER;?>">Une imprimmante</label>
					</li>
					<li>
						<input id="category-<?php echo Request::CATEGORY_OTHER;?>" type="radio" name="category" <?php echo (empty($_SESSION['request']['category']) == false && $_SESSION['request']['category'] == Request::CATEGORY_OTHER?'checked=""':'');?> value="<?php echo Request::CATEGORY_OTHER;?>" />
						<label for="category-<?php echo Request::CATEGORY_OTHER;?>">Autre</label>
					</li>
				</ul>
				<br /><label>Eventuellement, saisissez le ou les numéro(s) d'ordinateur(s) qui vous pose(nt) problème</label>
				<br /><input type="text" name="computer_number" value="<?php echo (empty($_SESSION['request']['computer_number'])?'':$_SESSION['request']['computer_number'])?>" />
				<br /><label>Décrivez précisément votre problème</label>
				<br /><textarea name="detail"><?php echo (empty($_SESSION['request']['detail'])?'':$_SESSION['request']['detail'])?></textarea>
				<br /><input type="submit" name="send" value="Envoyer" />
				<input type="hidden" name="action" value="send" />
			</form>
		<?php break;
		case 2:?>
			<h1>Récapitulatif</h1>
			<p>Votre nom : <?php echo $_SESSION['request']['name'];?></p>
			<p>Numéro de la salle : <?php echo $_SESSION['request']['office_number'];?></p>
			<p><?php echo $strings->get('request_category-'.$_SESSION['request']['category']);?></p>
			<p><?php echo $_SESSION['request']['computer_number'];?></p>
			<p><?php echo $_SESSION['request']['detail'];?></p>
			<form method="POST" action="">
				<input type="submit" name="send" value="Je confirme ma demande d'intervention">
				<input type="hidden" name="action" value="save" />
			</form>
			<form method="POST" action="">
				<input type="submit" name="send" value="Modifier ma demande d'intervention">
				<input type="hidden" name="action" value="edit" />
			</form>
		<?php break;
		case 3:?>
			<h1>Votre demande a bien été enregistrée sous le numéro : <?php echo $request_id;?></h1>
			<p>Vous pouvez suivre l'état de votre demande en cliquant <a href="?request_id=<?php echo $request_id;?>">ici</a></p>
		<?php break;
		case 4:
			if(empty($request)){?>
				<h1>La demande numéro <?php echo $_GET['request_id'];?> n'existe pas !</h1>
			<?php }else{?>
				<h1>La demande numéro <?php echo $request['request_id'];?> est <i><?php echo $strings->get('request_status-'.$request['status']);?></i></h1>
				<p>Cette page se met à jour régulièrement.</p>
				<script>
					setTimeout(function(){
						location.reload();
					}, 60000);
				</script>
			<?php }?>
		<?php break;
	}?>
</body>
</html>