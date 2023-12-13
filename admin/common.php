<?php
@session_start();
$db = require './db.inc.php';
function isPostRequest(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function logger($description)
{
    global $db;
    $sql = 'insert into log (username,reason) value ("' . $_SESSION['user'] . '","' . $description . '")';
    $db->update($sql);
}

return $db;
