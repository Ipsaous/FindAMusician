<?php

namespace App;


class Validator {

    private $errors = [];
    private $db;

    public function __construct(){

        $this->db = Database::getDb();
    }

    public function checkPseudo($pseudo){
        //Vérification du pseudo
        if(!preg_match('/^[a-zA-Z0-9 \-_.]{2,60}+$/', $pseudo)){
            $this->errors['error'] = true;
            $this->errors['message']['pseudo'] = "Pseudo incorrect";
        }else{
            if($this->checkIfFieldsExist("pseudo", $pseudo)){
                $this->errors['error'] = true;
                $this->errors['message']['pseudo'] = "Ce pseudo est déjà pris";
            }
        }
    }

    public function checkEmail($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->errors['error'] = true;
            $this->errors['message']['email'] = "Email incorrect";
        }else{
            if($this->checkIfFieldsExist("email", $email)){
                $this->errors['error'] = true;
                $this->errors['message']['email'] = "Cet email existe déjà";
            }
        }
    }

    public function checkPassword($password, $password2){
        if($password !== $password2){
            $this->errors['error'] = true;
            $this->errors['message']['password'] = "Les mots de passes doivent être identiques";
        }
    }


    public function checkIfFieldsExist($fields, $value){

        $sql = $this->db->prepare("SELECT Count(*) FROM users WHERE $fields = ?");
        $sql->execute([$value]);
        $row = $sql->fetchColumn();
        if($row){
            return true;
        }
        return false;

    }

    public function checkGender($gender){
        $valid = ["H", "F"];
        if(in_array($gender, $valid)){
            return true;
        }else{
            return false;
        }
    }

    public function checkBirthdayDate($birthdayDate){

        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$birthdayDate)){
            return true;
        }else{
            return false;
        }

    }

    public function isTokenValid($token, $id){

        $sql = $this->db->prepare("SELECT token FROM users WHERE id = ?");
        $sql->execute([$id]);
        $row = $sql->fetch();
        if($row){
            if($token === $row->token){
                return true;
            }
        }
        return false;
    }

    public function alreadyActif($id){
        $sql = $this->db->prepare("SELECT id FROM users WHERE id = ? AND actif = 1");
        $sql->execute([$id]);
        if($result = $sql->fetch()){
            return true;
        }
        return false;
    }

    public function setErrors($key, $value){
        $this->errors[$key] = $value;
    }

    public function getErrors(){
        return $this->errors;
    }

} 