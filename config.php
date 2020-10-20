<?php

/*
    -- Sesison e Base --

    - Iserir a ssesion no config para ser puxado em todas as pastas, essa session vai armazenar o token, para
    sempre verificar se o usuario esta logado 
    - Usar a varivael $base sempre que tiver um link no sistema, para não haver erro de redirecionamento em 
    outros navegadores
*/
session_start();
$base = "http://localhost/devsbookoo";

$db_name = 'devsbook';
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

$pdo = new PDO("mysql:dbname=".$db_name.";host=".$db_host, $db_user, $db_pass);
