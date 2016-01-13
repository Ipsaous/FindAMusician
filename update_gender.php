<?php
require_once "vendor/autoload.php";

$getModel = new \App\Models\UserModel();
$results["message"] = "";
$results["error"] = false;

if(!empty($_POST)){

    if(isset($_POST['_token'], $_POST['id'], $_POST['gender'], $_POST['birthday_date'])){

        if(!empty($_POST['_token']) && !empty($_POST['id']) && !empty($_POST['gender']) && !empty($_POST['birthday_date'])){

            $validator = new \App\Validator();
            $results = $getModel->updateGender($_POST['_token'], $_POST['id'], $_POST['gender'], $_POST['birthday_date'], $validator);
            if(count($results) === 0){
                $results['error'] = false;
                $results['message'] = "Update effectuée";
                $results['gender'] = $_POST['gender'];
                $results['birthday_date'] = $_POST['birthday_date'];
            }

        }else{
            $results['error'] = true;
            $results['message'] = "Veuillez remplir tous les champs";
        }

        echo json_encode($results);
    }

}
