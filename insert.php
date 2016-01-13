<?php

use App\Database;

require_once "vendor/autoload.php";

$db = Database::getDb();

$results['error'] = false;
$results['message'] = "";

if(!empty($_POST)){
	if(!empty($_POST['latitude']) && !empty($_POST['longitude']) && !empty($_POST['id'])){

		$id = $_POST['id'];
		$latitude = $_POST['latitude'];
		$longitude = $_POST['longitude'];
		$city = $_POST['city'];
		$region = $_POST['region'];
		$country = $_POST['country'];

		$sql = $db->prepare("UPDATE users SET latitude = ?, longitude = ?, city = ?, region = ?, country = ? WHERE id = ?");
		$sql->execute([$latitude, $longitude, $city, $region, $country, $id]);
		//POur vérifier une update, faire un rowCount
		if($sql){
			$results['error'] = false;
			$results['message'] = "Update effectuee";
		}else{
			$results['error'] = true;
			$results['message'] = "Une erreur s'est produite";
		}

	}else{
		$results['error'] = true;
		$results['message'] = "Impossible de récupérer votre position";
	}

	echo json_encode($results);
}