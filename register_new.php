<?php

require_once "vendor/autoload.php";

$getModel = new App\Models\UserModel();
$results['error'] = false;
$results['message'] = "";

if(!empty($_GET['action']) && $_GET['action'] === "normal") {

    if (!empty($_POST)) {
        if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password2'])) {

            $validator = new \App\Validator();
            //Changer $register par $results et pareil pour les $results a changer pour $results
            $results = $getModel->registerNormal($_POST['pseudo'], $_POST['email'], $_POST['password'], $_POST['password2'], $validator);
            if(count($results) === 0){
                $results['error'] = false;
                $results['message']['success'] = "Un email vient de vous être envoyé. Veuillez cliquer sur le lien présent dans le mail pour finaliser votre inscription";
            }

        } else {
            $results['error'] = true;
            $results['message']['empty'] = "Veuillez remplir tous les champs";
        }
        echo json_encode($results);
    }
}


if(!empty($_GET['action']) && $_GET['action'] === "facebook"){

    if(!empty($_POST) && !empty($_POST['pseudo']) && !empty($_POST['facebook_id'])){
        $validator = new \App\Validator();
        $_POST['email'] = !empty($_POST['email']) ? $_POST['email'] : "";
        $results = $getModel->registerFacebook($_POST['pseudo'], $_POST['facebook_id'], $_POST['email'], $validator);
        if(count($results) === 0){
            $results['error'] = false;
            $results['message'] = "Vous ête maintenant inscrit. Vous pouvez désormais vous connecter";
        }else{
            if(array_key_exists('already_exist', $results)){
                //Je refresh le token
                $token = $getModel->refreshToken("users.facebook_uid", $_POST['facebook_id']);
                if(!$token){
                    $results['error'] = true;
                    $results['message'] = "Token non valide";
                }else{
                    $results = $getModel->findByFacebookId($results['facebook_uid'], "id, pseudo, email, latitude, longitude, avatar, gender, birthday_date, actif, token");
                }
            }
        }

        echo json_encode($results);

    }

}