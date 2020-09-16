<?php
require_once 'functions.php';  // для вызова функции importData()
require_once 'DB.php'; // для вызова функции db_connection() и получения PDO подключения к БД
$file_name = 'test.json'; // имя файладля передачи парметром  функции importData()

db_connection(); // вызов функции db_connection()
try {
    if (!$db_conn) {
        throw new Exception('DB fail');
    } else {
        importData($file_name, $db_conn); // вызов функции importData()
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
$db_conn = ""; // Обнуляем подключение к БД
