<?php
/*
    -- Fedd_editor_action --
        - Puxa os arquivos para verificar se o usuario esta logado, e ter as informações do usuario
        - puxar PostDaoMysql - para pode estanciar essa classe e enviar o objeto de Post

        - Estancia PostDaoMysql enviando o pdo para o construtor, ($pdo vem do config.php)
        - Estancia Post (que vem do arquivo dao/PostDaoMysql.php)
        - Monta o objeto de Post e envia para o DAO de post
*/
require 'config.php';
require 'models/Auth.php'; 
require 'dao/PostDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->chekToken();

$body = filter_input(INPUT_POST, 'body');

if ($body) {

    $postDao = new PostDaoMysql($pdo);

    $newPost = new Post();
    $newPost->id_user = $userInfo->id;
    $newPost->type = 'text';
    $newPost->created_at = date('Y/m/d H:i:s');
    $newPost->body = $body;
    
    $postDao->insert($newPost);
}
header("Location: ".$base);
exit;