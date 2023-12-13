<?php
require './db.php';
$configuration = [
    'servername' => 'mariadb',
    'username' => 'root',
    'password' => 'rootpwd',
    'dbname' => 'cw2-database'
];

return new DB(
    $configuration['servername'],
    $configuration['username'],
    $configuration['password'],
    $configuration['dbname'],
    3306
);