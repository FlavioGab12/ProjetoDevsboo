<?php
/*
    -- classe responsavel pelo usuario --

    - Todos os atributos publicos para evitar criar get e set de todos
*/

class User {
    public $id;
    public $email;
    public $password;
    public $name;
    public $birthdate;
    public $city;
    public $work;
    public $avatar;
    public $cover;
    public $token;

}

// Criar interface para implementar no arquivo UserDaoMysql.php
interface UserDAO {
    public function findByToken($token);
    public function findByEmail($email);
    public function findById($id);
    public function findByName($name);
    public function update(User $u);
    public function insert(User $u);
}