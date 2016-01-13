<?php
use App\Database;

require_once "vendor/autoload.php";

$db = Database::getDb();

$results['error'] = false;
$results['message'] = "";

if(isset($_GET) && isset($_GET['action']) && $_GET['action'] === "avatar"){

if(!empty($_POST)){
	if(isset($_POST['user_id']) && !empty($_POST['user_id']) && isset($_POST['avatar']) && !empty($_POST['avatar'])){
		$id = $_POST['user_id'];		
		$image = $_POST['avatar'];

		//Vérification que l'on ait un user 
		$sql = $db->prepare("SELECT id FROM users WHERE id = ?");
		$sql->execute([$id]);
		if($row = $sql->fetch()){
			$userId = $row->id;
			$path = $userId.".png";
			$fullpath = "images/avatar/".$path;

			$results['message'] = $path.$userId;

			$sql = "UPDATE users SET avatar = ? WHERE users.id = ?";
			$sql = $db->prepare($sql);
			$sql->execute([$path, $userId]);
			if($sql){
				file_put_contents($fullpath, base64_decode($image));
				$results['error'] = false;
				$results['message'] = "Avatar ajouté";
				$results['avatar'] = $path;
				
			}else{				
				$results['error'] = true;
				$results['message'] = "Erreur lors de l'insertion"; 
			}
		}else{
			$results['error'] = true;
			$results['message'] = "Aucun utilisateur trouvé"; 
		}

		echo json_encode($results);
		
	} 
	

}

}else{

	if(!empty($_POST) && isset($_POST['latitude']) && !empty($_POST['latitude']) && isset($_POST['longitude']) && !empty($_POST['longitude']) && isset($_POST['userId']) && !empty($_POST['userId'])){

		$userId = $_POST['userId'];
		$latitude = $_POST['latitude'];
		$longitude = $_POST['longitude'];
		if(isset($_POST['city'])){
			$city = $_POST['city'];
		}
		if(isset($_POST['region'])){
			$region = $_POST['region'];
		}
		if(isset($_POST['country'])){
			$country = $_POST['country'];
		}

		$sql = $db->prepare("UPDATE users SET latitude = ?,longitude = ?, city = ?, region = ?, country = ?, updated_at = NOW() WHERE users.id = ?");
		$sql->execute([$latitude, $longitude, $city, $region, $country, $userId]);
		if($sql){
			$results['error'] = false;		
			$results['latitude'] = $latitude;
			$results['longitude'] = $longitude;
			$results['city'] = $city;
			$results['region'] = $region;
			$results['country'] = $country;
		}else{
			$results['error'] = true;
			$results['message'] = "Une erreur s'est produite";
		}

		echo json_encode($results);

	}
}