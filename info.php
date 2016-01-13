<?php

use App\Database;

require_once "vendor/autoload.php";


$db = Database::getDb();


if(isset($_GET) && isset($_GET['full']) && $_GET['full'] === "ok"){

	if(!empty($_POST) && isset($_POST['user_id']) && !empty($_POST['user_id'])){
		$sql = $db->prepare("SELECT id, pseudo, email, age, latitude, longitude, niveau, city, region, country, avatar,description, created_at, updated_at FROM users WHERE id = ?");
		$sql->execute([$_POST['user_id']]);
		$results = $sql->fetch();

		echo json_encode($results);
	}

}else{
	if(!empty($_POST) && isset($_POST['user_id']) && !empty($_POST['user_id'])){
		$sql = $db->prepare("SELECT id, pseudo, email, age, latitude, longitude, niveau, city, region, country, avatar, created_at, updated_at FROM users WHERE id != ?");
		$sql->execute([$_POST['user_id']]);
		$results = $sql->fetchAll();
		

		echo json_encode($results);
	}
}

