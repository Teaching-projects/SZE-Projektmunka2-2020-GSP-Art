<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME','projekt');
define('DB_PASSWORD','123Projekt123');
define('DB_NAME','projekt2');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($link === false) {
    die("ERROR: A kiszolgáló nem elérhető. " . mysqli_connect_error());
}
?>