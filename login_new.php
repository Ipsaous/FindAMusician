<?php
require_once "vendor/autoload.php";

$getModel = new App\Models\UserModel();
$results['error'] = false;
$results['message'] = "";

if(!empty($_GET) && !empty($_GET['action']) && $_GET["action"] === "normal") {
    if(!empty($_POST)){
        if(!empty($_POST["username"]) && !empty($_POST["password"])){

            $results = $getModel->login($_POST['username'], $_POST['password']);

        }else{
            $results["error"] = true;
            $results["message"] = "Veuillez remplir tous les champs";
        }
        echo json_encode($results);
    }
}
if(!empty($_GET) && !empty($_GET['action']) && $_GET['action'] === "facebook") {

    if (!empty($_POST) && !empty($_POST['fb_id'])) {

        $results = $getModel->loginFacebook($_POST['fb_id']);

    }else{
        $results['error'] = true;
        $results['message'] = "Veuillez remplir tous les champs";
    }
    echo json_encode($results);
}