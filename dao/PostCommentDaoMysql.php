<?php

require_once 'models/PostComment.php';
require_once 'dao/UserDaoMysql.php';

class PostCommentDaoMysql implements PostCommentDAO {
    private $pdo;

    public function __construct(PDO $driver) {
        $this->pdo = $driver;
    }

    // Função para pegar comentarios do BD e transformr em objeto
    public function getComments($id_post) {
        $array = [];

        $sql = $this->pdo->prepare("SELECT * FROM postcomments
        WHERE id_post = :id_post");
        $sql->bindValue(':id_post', $id_post);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            // Estaciar UserDao para pegar inforaçoes do usuario que comentou
            $userDao = new UserDaoMysql($this->pdo);

            foreach($data as $item) {

                // Cria objeto de comentarios
                $commentItem = new PostComment();
                $commentItem->id = $item['id'];
                $commentItem->id_post = $item['id_post'];
                $commentItem->id_user = $item['id_user'];
                $commentItem->created_at = $item['created_at'];
                $commentItem->body = $item['body'];

                // Usar função findById para pegar as informaçoes basicas do usuario
                $commentItem->user = $userDao->findById($item['id_user']);

                $array[] = $commentItem;
            }
        }
        
        return $array;
    }

    public function addComment(PostComment $pc) {
        
        $sql = $this->pdo->prepare("INSERT INTO postcomments 
        (id_post, id_user, created_at, body) VALUES 
        (:id_post, :id_user, :created_at, :body)");

        $sql->bindValue(':id_post', $pc->id_post);
        $sql->bindValue(':id_user', $pc->id_user);
        $sql->bindValue(':created_at', $pc->created_at);
        $sql->bindValue(':body', $pc->body);
        $sql->execute();

        return true;
    }

}