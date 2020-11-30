<?php
// Функция для создания PDO подключения к БД
function db_connection()
{
    $dsn = "mysql:host=localhost;dbname=test;charset=utf8";
    $dbuser = "user";
    $dbpass = "pass";
    global $db_conn;
    try {
        $db_conn = new PDO($dsn, $dbuser, $dbpass);
        $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo $e->getMessage();
        return;
    }
    return $db_conn;
}
