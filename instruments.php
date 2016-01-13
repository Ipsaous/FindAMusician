<?php

require_once "class/Database.php";

$db = Database::getDb();

$sql = $db->query("SELECT * FROM instruments");
$results = $sql->fetchAll();
echo json_encode($results);