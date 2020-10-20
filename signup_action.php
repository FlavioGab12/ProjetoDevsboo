<?php

require 'config.php';
require 'models/Auth.php';

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS); 
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$birthdate = filter_input(INPUT_POST,'birthdate');

if ($name && $email && $password && $birthdate) {
    
    $auth = new Auth($pdo, $base);

    // Verificar se data de nascimento esta no padrão correto que é 00/00/0000
    // Quebra variavel pela barra e verifica se gerou um array com 3 indices
    $birthdate = explode('/', $birthdate);
    if (count($birthdate) != 3) {
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header("Location:".$base."/signup.php");
        exit;
    }

    // Verificar se data de nascimento é uma data que existe
    // Transforma a data no padraõ americano, e verifica se retornou um time em milisegundo real
    $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
    if ( strtotime($birthdate) === false) {
        $_SESSION['flash'] = 'Data de nascimento não existe!';
        header("Location:".$base."/signup.php");
        exit;
    }

    // Verifica se email ja esta cadastrado, caso não esteja seguir com o cadastro
    // Rodar a função register, enviar os dados do usuario como parametro e redirecionar para a index
    if ($auth->emailExists($email) === false) {

        $auth->registerUser($name, $email, $password, $birthdate);
        header("Location:".$base);
        exit;

    } else {
        $_SESSION['flash'] = 'E-mail ja cadastrado.';
        header("Location:".$base."/signup.php");
        exit;
    } 

} 

// Caso não ache usuario, preencher a session flash e retornar para login.php
$_SESSION['flash'] = 'E-mail e/ou senha incorretos!';
header("Location:".$base."/signup.php");
exit;