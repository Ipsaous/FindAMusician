<?php

namespace App\Models;

use App\Entity\User;
use App\Utility\Helper;
use App\Validator;
use \PDO;

class UserModel extends Model {


    public function __construct(){
        parent::__construct();
    }

    /**
     * Fonction permettant d'enregistrer un utilisateur de maniere "classique"
     * @param string $pseudo
     * @param string $email
     * @param string $password
     * @param string $password2
     * @param Validator $validator
     * @return array
     */
    public function registerNormal($pseudo, $email, $password, $password2, Validator $validator){

            $validator->checkPseudo($pseudo);
            $validator->checkEmail($email);
            $validator->checkPassword($password, $password2);
            if(count($validator->getErrors()) === 0){
                $password = password_hash($password, PASSWORD_BCRYPT);
                $key = Helper::generateKey();
                $sql = $this->db->prepare("INSERT INTO users(pseudo, email, password, created_at, validation_key) VALUES (?, ?, ?, NOW(), ?)");
                $sql->execute([$pseudo, $email, $password, $key]);
                if(!$sql){
                    $validator->setErrors('insert', "Une erreur s'est produite");
                    $validator->setErrors('error', true);
                }else{
                    Helper::sendValidationEmail($this->db->lastInsertId(), $email, $key);
                }
            }
        return $validator->getErrors();
    }

    /**
     * Fonction permettant d'enregistrer un utilisateur via facebook
     * @param string $pseudo
     * @param string $facebook_uid
     * @param string $email
     * @param Validator $validator
     * @return array
     */
    public function registerFacebook($pseudo, $facebook_uid, $email, Validator $validator){

        if($validator->checkIfFieldsExist('facebook_uid', $facebook_uid)){
            $validator->setErrors('already_exist', true);
            $validator->setErrors('facebook_uid', $facebook_uid);
            $validator->setErrors('error', true);
        }else{
            if(empty($email)){
               $email = null;
            }
            $key = Helper::generateKey();
            $sql = $this->db->prepare("INSERT INTO users(pseudo, email, facebook_uid, created_at, validation_key, actif) VALUES(:pseudo, :email, :facebook_uid, NOW(), :validation_key, 1)");
            $sql->execute([":pseudo" => $pseudo, ":email" => $email, ":facebook_uid" => $facebook_uid, "validation_key" => $key]);
            if(!$sql){
                $validator->setErrors('insert', "Une erreur s'est produite");
                $validator->setErrors('error', true);
            }
        }

        return $validator->getErrors();

    }

    /**
     * Fonction permettant de logger l'utilisateur via inscription classique
     * @param string $pseudo
     * @param string $password
     * @return User
     */
    public function login($pseudo, $password){

        $sql = $this->db->prepare("SELECT * FROM users WHERE pseudo = ?");
        $sql->execute([$pseudo]);
        $row = $sql->fetch();
        if($row){
            if(password_verify($password, $row->password)){
                $token = $this->refreshToken("users.id", $row->id);
                if(!$token){
                    $results["error"] = true;
                    $results["message"] = "Token non valide";
                }else{
                    $user = $this->buildUser($row);
                    $user->setToken($token);
                    return $user;
                }

            }else{
                $results["error"] = true;
                $results["message"] = "Pseudo ou mot de passe incorrect";
            }
        }else{
            $results["error"] = true;
            $results["message"] = "Pseudo ou mot de passe incorrect";
        }
        return $results;

    }

    /**
     * Fonction permettant de logger un utilisateur via facebook
     * @param string $facebook_uid
     * @return User
     */
    public function loginFacebook($facebook_uid){
        $sql = $this->db->prepare("SELECT * FROM users WHERE facebook_uid =?");
        $sql->execute([$facebook_uid]);
        $row = $sql->fetch();
        if($row){
            $token = $this->refreshToken("users.id", $row->id);
            if(!$token){
                $results["error"] = true;
                $results["message"] = "Token non valide";
            }else{
                $user = $this->buildUser($row);
                $user->setToken($token);
                return $user;
            }
        }else{
            $results["error"] = true;
            $results["message"] = "Aucun compte Facebook ne correspond";
        }
        return $results;
    }

    /**
     * Fonction permettant de récupérer tous les utilisateurs (Virer la récupération du password)
     * @return array
     */
    public function findAll()
    {
        $sql = $this->db->query("SELECT * FROM users");
        $results = $sql->fetchAll(PDO::FETCH_CLASS, "\\App\\Entity\\User");
        return $results;
    }

    /**
     * Récupération d'un utilisateur en fonction de son id
     * @param $id
     * @param string $fields
     * @return mixed
     */
    public function findById($id, $fields = "*")
    {
        $sql = $this->db->prepare("SELECT {$fields} FROM users WHERE users.id = ?");
        $sql->execute([$id]);
        $results = $sql->fetch();
        return $results;
    }

    /**
     * Fonction permettant de vérifier si un utilisateur a déja un facebook id
     * @param $facebook_uid
     * @param string $fields
     * @throws \Exception
     */
    public function findByFacebookId($facebook_uid, $fields = "*"){

        $select = "SELECT {$fields} FROM users WHERE facebook_uid = ?";
        $sql = $this->db->prepare($select);
        $sql->execute([$facebook_uid]);
        $result = $sql->fetch();
        if($result){
            $user = $this->buildUser($result);
            return $user;
        }else{
            throw new \Exception("Une erreur s'est produite");
        }
    }

    /**
     * Fonction permettant de rafraichir le token
     * @param string $field
     * @param string $id
     * @param null $oldToken
     * @return bool|string
     */
    public function refreshToken($field, $id, $oldToken = null){

        $token = Helper::generateToken();
        //Je check si c'est un refresh token apres expiration
        if($oldToken != null){
            $sql = $this->db->prepare("UPDATE users SET token = ? WHERE {$field} = ? AND token = ?");
            $sql->execute([$token, $id, $oldToken]);
            if($sql->rowCount() > 0){
                return $token;
            }else{
                return false;
            }
        }else{
            $sql = $this->db->prepare("UPDATE users SET token = ? WHERE {$field} = ?");
            $sql->execute([$token, $id]);
            if($sql->rowCount() > 0){
                return $token;
            }else{
                return false;
            }
        }
       
        

    }

    /**
     * Fonction permettant de "construire" un utilisateur en fonction d'une ligne de base de donnée
     * @param $row
     * @return User
     */
    public function buildUser($row){

        $user = new User();
        $user->setId($row->id);
        $user->setPseudo($row->pseudo);
        $user->setEmail($row->email);
        $user->setAvatar($row->avatar);
        $user->setLatitude($row->latitude);
        $user->setLongitude($row->longitude);
        $user->setGender($row->gender);
        $user->setBirthdayDate($row->birthday_date);
        $user->setActif($row->actif);

        return $user;
    }

    public function update($id){

    }

    /**
     * Fonction permettant de confirmer un utilisateur apres inscription
     * @param $id
     * @param $validationKey
     * @return bool
     */
    public function confirmUser($id, $validationKey){
        $sql = $this->db->prepare("UPDATE users SET actif = 1 WHERE users.id = ? AND validation_key = ?");
        $sql->execute([$id, $validationKey]);
        if($sql->rowCount() > 0 ){
            return true;
        }
        return false;
    }


    /**
     * Fonction permettant d'update le sexe et la date de naissance d'un utilisateur
     * @param string $token
     * @param string $id
     * @param string $gender
     * @param string $birthday_date
     * @param Validator $validator
     * @return array
     */
    public function updateGender($token, $id, $gender, $birthday_date, Validator $validator)
    {
        if(!$validator->checkGender($gender)){
            $validator->setErrors("error", true);
            $validator->setErrors("gender", "Erreur lors de la selection du sexe");
        }
        if(!$validator->checkBirthdayDate($birthday_date)){
            $validator->setErrors("error", true);
            $validator->setErrors("birthday_date", "Erreur avec la date de naissance");
        }
        if(!$validator->isTokenValid($token, $id)){
            $validator->setErrors("error", true);
            $validator->setErrors("token", "Token non valide");
        }
        if(count($validator->getErrors()) === 0){

            $sql = $this->db->prepare("UPDATE users SET gender = ?, birthday_date = ? WHERE users.id = ?");
            $sql->execute([$gender, $birthday_date, $id]);
            if(!$sql->rowCount() > 0){
                $validator->setErrors("error", true);
                $validator->setErrors("insert", "Erreur lors de l'insertion");
                $validator->setErrors("date", $birthday_date);
            }
        }
        return $validator->getErrors();
    }
}