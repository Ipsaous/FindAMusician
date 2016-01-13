<?php
namespace App;
use \PDO;
class Database {

    private static $db;

    /**
    * Création de la connexion à la base de données
    **/
    public static function getDb(){

        if(is_null(self::$db)){
                try{
                    self::$db = new PDO('mysql:host=localhost;dbname=find_a_musician','root','');
                    self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                    self::$db->exec('SET NAMES utf8'); 
                }catch(Exception $e){
                    echo "Impossible de se connecter à la base de données";
                }
                
        }
        return self::$db;

    }
} 