<?php

namespace App\Utility;

class Helper {

    /**
     * Genere la cle de la validation envoyé en email
     * @return string
     */
    static function generateKey(){
        $key = md5(microtime(TRUE)*100000);
        return $key;
    }

    /**
     * Generation du token
     * @return string
     */
    static function generateToken(){
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        return $token;
    }

    /**
     * Fonction qui envoie l'email de confirmation ( A voir si je la laisse ici )
     * @param $id
     * @param $mail
     * @param $key
     * @return boolean
     */
    static function sendValidationEmail($id, $mail, $key){

            $entete = 'From: admin@findamusician.com';
            $sujet = 'Activer votre compte' ;
            $message = 'Bienvenue sur FindAMusician,

				Pour valider votre compte, veuillez cliquer sur le lien ci dessous
				ou copier/coller dans votre navigateur internet.

				http://localhost/findamusician/valid_account.php?id='.urlencode($id).'&validation_key='.urlencode($key).'

				---------------
				Ceci est un mail automatique, Merci de ne pas y repondre.';


            $response = @mail($mail, $sujet, $message, $entete);
            if ($response != true){
                return false;
            }
            return true;

    }
} 