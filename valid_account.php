<?php

use App\Validator;

require_once "vendor/autoload.php";

$getModel = new \App\Models\UserModel();
$message = "";
if(!empty($_GET)){

    if(isset($_GET['id'], $_GET['validation_key'])){
        if(!empty($_GET['id']) && !empty($_GET['validation_key'])){

            $validator = new Validator();
            if($validator->alreadyActif($_GET['id'])){
                $message = "Votre compte est déjà actif";
            }else{
                if($getModel->confirmUser($_GET['id'], $_GET['validation_key'])){
                    $message = "Votre compte a bien été confirmé. Vous pouvez dorénavant vous connecter !";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset = "utf-8" />
        <title>Confirmation de l'email</title >
    </head>
    <body>

    <?= $message != "" ? $message : "" ;?>

    </body>
</html>