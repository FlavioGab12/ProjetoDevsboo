<?php
/*
    -- classe responsavel pelas relçoes de usuarios --

    - Todos os atributos publicos para evitar criar get e set de todos
*/

class UserRelation {
    public $id;
    public $user_from;
    public $user_to;

}

// Criar interface para implementar no arquivo UserRelationDaoMysql.php
interface UserRelationDAO {
    public function insert(UserRelation $u);
    public function getFollowing($id);
    public function getFollowers($id);
}