<?php

require_once "vendor/autoload.php";

$getModel = new \App\Models\UserModel();
$results['error'] = false;
$results['message'] = "";

if(!empty($_POST)){
    if(isset($_POST['id'], $_POST['_token'])){

        if(!empty($_POST['id']) && !empty($_POST['_token'])){

            $token = $getModel->refreshToken($_POST['id'], $_POST['_token']);
            if($token === false){
                $results['error'] = true;
                $results['message'] = "Impossible de rafraichir le token, veuillez vous reconnecter";
            }else{
                //C'est bon, je renvoie le token
                $results['error'] = false;
                $results['token'] = $token;
            }
            echo json_encode($results);

        }

    }
}