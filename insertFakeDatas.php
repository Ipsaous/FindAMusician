<?php 

require_once "class/Database.php";

$db = Database::getDb();

insertFakeDatas($db);

function insertFakeDatas($db){

	$pseudos = ["jacques", "michel", "catherine", "player2", "guitare35", "jean", "simon", "jeremie", "sophie", "stephanie", "nicole", "laure", "lucie", "anais", "champion57", "joueurvert", "trompe_toi", "je_suis_nul", "best_of_the_world"];
	$emails = ["truc@truc.fr", "jacques@truc.fr", "cateh@sfr.fr", "machin@machin.fr", "orangeee@laposte.net"];
	$niveaux = ["Débutant", "Moyen", "Très bon", "Expert"];
	$ages = [];
	for($i = 15; $i < 100; $i++){
		$ages[$i] = $i;
	}
	$latitudes = [];
	$longitudes = [];
	for($i = 44; $i < 50; $i+=0.001){
		$latitudes[$i] = $i;
	}
	for($i = -2; $i < 6; $i+=0.001){
		$longitudes[$i] = $i;
	}

	for($j = 0; $j < 5; $j++){
		$pseudo = array_rand($pseudos,1);
		$pseudo = $pseudos[$pseudo];
		$email = array_rand($emails,1);
		$email = $emails[$email];
		$niveau = array_rand($niveaux,1);
		$niveau = $niveaux[$niveau];
		$age = array_rand($ages,1);
		$age = $ages[$age];
		$latitude = array_rand($latitudes,1);
		$latitude = $latitudes[$latitude];
		//$latitude = number_format($latitude,4);
		$longitude = array_rand($longitudes,1);
		$longitude = $longitudes[$longitude];
		//$longitude = number_format($longitude, 4);
		$city = "";
		$region = "";
		$country = "";
		var_dump($pseudo, $email, $niveau, $age, $latitude, $longitude);

		/*$sql = $db->prepare("INSERT INTO users(pseudo, email, niveau, age, latitude, longitude, city, region, country) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?");
		$sql->execute([$pseudo, $email, $niveau, $age, $latitude, $longitude, $city, $region, $country]);
		var_dump($sql);
		*/
		$insert = "INSERT INTO users(pseudo, email, niveau, age, latitude, longitude) VALUES (:pseudo, :email, :niveau, :age, :latitude, :longitude)";		
		$sql = $db->prepare($insert);
		$sql->execute([
			":pseudo" => $pseudo,
			":email" => $email,
			":niveau" => $niveau,
			":age" => $age,
			":latitude" => $latitude,
			":longitude" => $longitude
			]);	
	}


}
