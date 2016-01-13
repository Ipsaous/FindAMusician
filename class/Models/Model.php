<?php

namespace App\Models;

use \App\Database;

abstract class Model {

    protected  $db;

    protected function __construct(){

        $this->db = Database::getDb();

    }

    public abstract function findAll();

    public abstract function findById($id);

    public abstract function update($id);




} 