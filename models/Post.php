<?php
/*
    -- classe responsavel pelos pots --

    - Todos os atributos publicos para evitar criar get e set de todos
*/

class Post {
    public $id;
    public $id_user;
    public $type;
    public $name;
    public $created_at;
    public $body;

}

// Criar interface para implementar no arquivo UserDaoMysql.php
interface PostDAO {
    public function insert(Post $p);
    public function getUserFeed($id_user);
    public function getHomeFeed($id_user);
    public function getPhotosFrom($id_user);
}