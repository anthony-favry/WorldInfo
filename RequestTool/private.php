<?php
require 'Init.php';
$strings = new Strings();
$user = new User($PDO);
$request = new Request($PDO);
$error_message = null;

if(empty($_SESSION['user']) || empty($_POST['logout']) == false){
	$_SESSION['user'] = array();
}

if(isset($_POST['send'])){
	switch($_POST['action']){
		case 'login':
			if($user->Login($_POST['login'], $_POST['password'])){
				$_SESSION['user'] = $user->GetDatas();
			}else{
				$error_message = $strings->Get('error-login');
			}
		break;
		case 'switch_status':
			if($request->UpdateStatus($_POST['request_id'], $_POST['status'], $_SESSION['user']['user_id']) == false){
				$error_message = $strings->Get('error-update_request_status');
			}
		break;
	}
}

if(empty($_SESSION['user']) == false){
	$requests = $request->GetAllRequest();
}?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title><?php echo Settings::PROJECT_NAME;?></title>
</head>
<body>
	<?php if(empty($_SESSION['user'])){?>
		<h1>Consultation d'un intervention</h1>
		<?php if(empty($error_message) == false){?>
			<p style="color:#FF0000;"><?php echo $error_message;?></p>
		<?php }?>
		<form method="POST" action="">
			<label>Utilisateur :</label>
			<input type="text" name="login" value="<?php echo (empty($_POST['login'])?'':$_POST['login']);?>" />
			<label>Mot de passe :</label>
			<input type="password" name="password" value="" />
			<input type="submit" name="send" value="Se connecter" />
			<input type="hidden" name="action" value="login" />
		</form>
	<?php }else{?>
		<form method="POST">
			<input type="submit" name="logout" value="Se déconnecter">
		</form>
		<h1>Suivi des demandes d'interventions informatiques</h1>
		<table>
			<thead>
				<tr>
					<td>Action</td>
					<td>Num</td>
					<td>Salle</td>
					<td>Description</td>
					<td>Remarque</td>
					<td>Demandée le</td>
					<td>Suivi</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($requests as $item){?>
					<tr>
						<td>
							<form method="POST" action="">
								<?php switch($item['status']){
									case Request::STATUS_WAITING :?>
										<input type="submit" name="send" value="Prendre en compte" />
										<input type="hidden" name="action" value="switch_status" />
										<input type="hidden" name="status" value="<?php echo Request::STATUS_PROGRESS;?>" />
									<?php break;
									case Request::STATUS_PROGRESS :?>
										<input type="submit" name="send" value="Le problème est résolu" />
										<input type="hidden" name="action" value="switch_status" />
										<input type="hidden" name="status" value="<?php echo Request::STATUS_SOLVED;?>" />
									<?php break;
								}?>
								<input type="hidden" name="request_id" value="<?php echo $item['request_id'];?>" />
							</form>
						</td>
						<td>
							<?php echo $item['request_id'];?>
						</td>
						<td>
							<?php echo $item['office_number'];?>
						</td>
						<td>
							<?php echo $strings->Get('request_category-'.$item['category']);?>
						</td>
						<td>
							<?php echo $item['detail'].(empty($item['computer_number'] == false)?'<br>Numéro des ordinateurs : '.$item['computer_number']:'');?>
						</td>
						<td>
							<?php echo Settings::FormatDate($item['date_creation'],Settings::DATETIME_FR);?>
						</td>
						<td>
							<?php echo $strings->get('request_status-'.$item['status']);?>
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	<?php }?>
</body>
</html>