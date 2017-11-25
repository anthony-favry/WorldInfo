<?php session_start();

require 'Class/Autoloader.php';
spl_autoload_register(array('Autoloader', 'autoload'));

// Connexion à la BDD
$PDO = Settings::BddConnect();