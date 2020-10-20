<?php
/*
    Dao do Usuario, arqeuivo responsavel por fazer as consultas no BD

    
*/
require_once 'models/UserRelation.php';

class UserRelationDaoMysql implements UserRelationDAO {
    private $pdo;

    public function __construct(PDO $driver) {
        $this->pdo = $driver;
    }

    public function insert(UserRelation $u) {

    }

    /*
        -- Metodo getFollowing : vai retornar uma lista dos usuarios que eu sigo --

        - Recebe o id do usuario logado
        - Cria o array $users
        - Lista o campo user_to (usuarios seguidos) na tabela userrelations aonde user_from (usuario que esta
        seguindo) for igual ao id enviado 
            [
        Obs: Uso o $id para fazer a consulta

        - Verificar se retorno foi maior do que zero
            - Joga a lista que veio do BD em $data
            - Usa o foreach para inserir no array $users todos os itens que veio do BD

        - Retorna o array $users
    */
    public function getFollowing($id) {
        $users = [];

        $sql = $this->pdo->prepare("SELECT user_to FROM userrelations WHERE user_from = :user_from");
        $sql->bindValue(':user_from', $id);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll();
            foreach ($data as $item) {
                $users[] = $item['user_to'];    
            }
        }

        return $users;
    }

    // Lista com Usuarios que estão me seguindo
    public function getFollowers($id) {
        $users = [];

        $sql = $this->pdo->prepare("SELECT user_from FROM userrelations WHERE user_to = :user_to");
        $sql->bindValue(':user_to', $id);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll();
            foreach ($data as $item) {
                $users[] = $item['user_from'];    
            }
        }

        return $users;
    }
}