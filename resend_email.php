<?php

use App\Utility\Helper;

require_once "vendor/autoload.php";

$getModel = new \App\Models\UserModel();
$results['error'] = false;
$results['message'] = "";

if(!empty($_POST)){

    if(isset($_POST['id'], $_POST['email'], $_POST['_token'])){

        if(!empty($_POST['id']) && !empty($_POST['email']) && !empty($_POST['_token'])){

           $validator = new \App\Validator();
            if($validator->isTokenValid($_POST['_token'], $_POST['id'])){
                $user = $getModel->findById($_POST['id'], "id, email, validation_key");
                if($user){
                   Helper::sendValidationEmail($user->id, $user->email, $user->validation_key);

                }
            }

        }
    }
}