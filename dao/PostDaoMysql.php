<?php
/*
    -- Classe PostDaoMysql -- 
        - Dentro da classe DAO utilizar sempre requiere_once, para puxar o arquvo apenas uma vez
        - Recebe o $pdo para ter a conexão com o BD
*/
require_once 'models/Post.php';
require_once 'dao/UserRelationDaoMysql.php';
require_once 'dao/UserDaoMysql.php';
require_once 'dao/PostLikeDaoMysql.php';
require_once 'dao/PostCommentDaoMysql.php';

class PostDaoMysql implements PostDAO {

    private $pdo;

    public function __construct(PDO $driver) {
        $this->pdo = $driver;
    }
    
    // Recebe o objeto de Post e insere no B
    public function insert(Post $p) {
        $sql = $this->pdo->prepare("INSERT INTO posts (
            id_user, type, created_at, body
        ) VALUES (
            :id_user, :type, :created_at, :body
        )");
        $sql->bindValue(':id_user', $p->id_user);
        $sql->bindValue(':type', $p->type);
        $sql->bindValue(':created_at', $p->created_at);
        $sql->bindValue(':body', $p->body);
        $sql->execute();

        return true;
    }

    /*
        -- Metodo getUserFeed: Retorna os feeds do usuario em forma de objetos --

        - Semelhante ao getHomeFeed, porem não tem o passo 1
        - Faz a listagem dos feeds
        - Tranformar a lisatgem em objetos
    */
    public function getUserFeed($id_user) {

        $array = [];

        $sql = $this->pdo->prepare("SELECT * FROM posts
        WHERE id_user = :id_user
        ORDER BY created_at DESC");
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            // Tranfromar o resultado em objeto 
            $array = $this->_postListToObject($data, $id_user);
        }

        return $array;
        }


    /*
        -- Metodo para buscar feeds do BD e retornar para a home em forma de objeto --
        - Pode usar um dao dentro do outro, se estiverem relacionados
    */
    public function getHomeFeed($id_user) {

        $array = [];

        // 1. Lista de usuario que eu sigo, incluindo eu mesmo
        $urDao = new UserRelationDaoMysql($this->pdo);
        $userList = $urDao->getFollowing($id_user);
        $userList[] = $id_user;

        // 2. Pegar os posts ordenado pela data
        // - Usar query pq estou apenas buscando informaçoes
        // - Concaternar com função impldode para query buscar (1,2) essa função junta o array em uma string,
        //   no caso seperado por uma virgula
        $sql = $this->pdo->query("SELECT * FROM posts
        WHERE id_user IN (".implode(',', $userList).")
        ORDER BY created_at DESC");
        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            // 3. Tranfromar o resultado em objeto 
            $array = $this->_postListToObject($data, $id_user);
        }

        return $array;
        }

        // Metodo para buscar lista de fotos um usuario e transformar em objeto
        public function getPhotosFrom($id_user) {
            $array = [];

            $sql = $this->pdo->prepare("SELECT * FROM posts WHERE
            id_user = :id_user AND type = 'photo'
            ORDER BY created_at DESC");
            $sql->bindValue(':id_user', $id_user);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                $data = $sql->fetchAll(PDO::FETCH_ASSOC);
    
                // Tranfromar o resultado em objeto 
                $array = $this->_postListToObject($data, $id_user);
            }

            return $array;
        }


        /*
            -- Função auxiliar para tranformar o resultado em objeto --
                - Recebe $post_list: lista de posts
                - Recebe $id_user: para verificar se algum dos posts e do usuario logado, esses posts o usuario
                logado vai poder fazer alterações nele
        */
        private function _postListToObject($post_list, $id_user) {
            // Array final que vai ser retornado mais estancia de Usuario, para poder pegar as info do usuario
            $posts = [];
            $userDao = new UserDaoMysql($this->pdo);

            // Para usar no contagem de likes
            $postLikeDao = new PostLikeDaoMysql($this->pdo);

            // Para usar nos comentarios
            $postCommentDao = new PostCommentDaoMysql($this->pdo);

            // foreach recebe toda a lista de $post_list, cria o objeto e joga cada item dentro do objeto,
            // depois joga esse objeto no array $posts[]
            foreach($post_list as $post_item) {
                $newPost = new Post();
                $newPost->id = $post_item['id'];
                $newPost->type = $post_item['type'];
                $newPost->created_at = $post_item['created_at'];
                $newPost->body = $post_item['body'];
                $newPost->mine = false;

                // Verificação para ver se o post é meu
                if($post_item['id_user'] == $id_user) {
                    $newPost->mine = true;
                }

                // Pegar informações do usuario
                $newPost->user = $userDao->findById($post_item['id_user']);

                // Informações sobre LIKE
                $newPost->likeCount = $postLikeDao->getLikeCount($newPost->id);
                $newPost->liked = $postLikeDao->isLiked($newPost->id, $id_user);

                // Informações sobre COMMENTS
                // Pegar comentarios pelo id
                $newPost->comments = $postCommentDao->getComments($newPost->id);

                $posts[] = $newPost;
            }

            return $posts;
        }
}