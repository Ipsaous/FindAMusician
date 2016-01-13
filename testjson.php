<?php 
require_once "class/Database.php";

//$db = Database::getDb();

$results['error'] = false;
$results['message'] = "";

if(!empty($_POST)){
	if(isset($_POST['user_id']) && !empty($_POST['user_id'])){

		foreach ($_POST as $key => $value) {
			if(preg_match('/^instru_[0-9]+$/', $key)){

				$instrument = json_decode($_POST[$key]);
				$instru_id = $instrument->id;
				$results["user_id"] = $_POST['user_id'];
				$results["instru_id"] = $instru_id;
				if(checkIfInstruForUser($_POST['user_id'], $instru_id, $db)){
					
					echo json_encode("update");

					//Si l'insertion ne se fait pas, erreur de l'instru id ou user_id i guess. Gérer à ce moment
				}else{
					echo json_encode("insert");
				}
			}
		}			

	}
}


/**
@Return True ou false
**/
function checkIfInstruForUser($user_id, $instru_id, $db){

	$sql = "SELECT id FROM instru_level WHERE user_id = ? AND instru_id = ?";
	$sql = $db->prepare($sql);
	$sql->execute([$user_id, $instru_id]);	
	if($row = $sql->fetch()){
		return true;
	}
	return false;

}