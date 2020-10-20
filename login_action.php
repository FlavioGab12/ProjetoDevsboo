<?php

require 'config.php';
require 'models/Auth.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');

if ($email && $password) {

    // Estanciar Auth, usar o motodo para validar o login se retornar true direcionar para a index
    $auth = new Auth($pdo, $base);
    
    if ($auth->validateLogin($email, $password)) {
        header("Location:".$base);
        exit;
    }

} 

// Caso não ache usuario, preencher a session flash e retornar para login.php
$_SESSION['flash'] = 'E-mail e/ou senha incorretos!3';
header("Location:".$base."/login.php");
exit;