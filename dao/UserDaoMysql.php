<?php
/*
    Dao do Usuario, arqeuivo responsavel por fazer as consultas no BD

    - Puxar models/User.php para usar a classe de usuario e a interface, utilizar require_once para puxar apenas
    uma vez
    - Puxar dao/UserRelationDaoMysql.php para user no generateUser
*/
require_once 'models/User.php';
require_once 'dao/UserRelationDaoMysql.php';
require_once 'dao/PostDaoMysql.php';

class UserDaoMysql implements UserDAO {
    private $pdo;

    public function __construct(PDO $driver) {
        $this->pdo = $driver;
    }

    // Função privada para gerar um objeto de usuario com os dados preenchidos, função recebe um array via
    // parametro, inserir no parametro os dados do array ou vazip
    private function generateUser($array, $full = false) {
        $u = new User();
        $u->id = $array{'id'} ?? 0;
        $u->email = $array['email'] ?? '';
        $u->password = $array['password'] ?? '';
        $u->name = $array['name'] ?? '';
        $u->birthdate = $array['birthdate'] ?? '';
        $u->city = $array['city'] ?? '';
        $u->work = $array['work'] ?? '';
        $u->avatar = $array['avatar'] ?? '';
        $u->cover = $array['cover'] ?? '';
        $u->token = $array['token'] ?? '';

        if($full) {
            $urDaoMysql = new UserRelationDaoMysql($this->pdo);
            $postDaoMysql = new PostDaoMysql($this->pdo);

            /*
                Followers = Quem segue o usuario
                foreach :
                - Recebe a lista de ID, pega o valor da chave (ou indice) e joga em $follower_id,
                - Estancia um objeto de usuario e roda o findById, que vai retornar um objeto com o id enviado
                - $u->followers - Recebe a chave e o objeto gerado

            */
            $u->followers = $urDaoMysql->getFollowers($u->id);
            foreach($u->followers as $key => $follower_id) {
                $newUser = $this->findById($follower_id);
                $u->followers[$key] = $newUser;
            }

            // Following = Quem o usuario segue
            $u->following = $urDaoMysql->getFollowing($u->id);
            foreach($u->following as $key => $follower_id) {
                $newUser = $this->findById($follower_id);
                $u->following[$key] = $newUser;
            }

            // Fotos
            $u->photos = $postDaoMysql->getPhotosFrom($u->id);

        }
        return $u;
    }

    // Metodo verifica se token existe, faz a consulta no BD, verifica se trouxe algum registro, caso tenha
    // jogar um array que retornou no metodo generateUser que vai gerar um objeto com os dados do array do 
    public function findByToken($token) {
        if(!empty($token)) {
            $sql = $this->pdo->prepare("SELECT * FROM users WHERE token = :token");
            $sql->bindValue(':token', $token);
            $sql->execute();

            if($sql->rowCount() > 0) {
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data);
                return $user;
            }
        }

        return false;
    }

    // Metodo para verificar se email existe bem similar ao findByToken
    public function findByEmail($email) {
        if(!empty($email)) {
            $sql = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $sql->bindValue(':email', $email);
            $sql->execute();
           
            if($sql->rowCount() > 0) {
                
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data);
                
                return $user;
            }
        }

        return false;
    }

    /*
        -- Metodo FindById -- 
         - $full = parametro opcional, para pegar informações completos do Usuario

        Paramtro opcional, pq nem sempre preciso de todas as informaçõs do usuario, assim evito fazer sempre
        essa consulta e melhora a performace do sistema
    */
    public function findById($id, $full = false){
        if(!empty($id)) {
            $sql = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
            $sql->bindValue(':id', $id);
            $sql->execute();
           
            if($sql->rowCount() > 0) {
                
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                $user = $this->generateUser($data, $full);
                
                return $user;
            }
        }

        return false;
    }

     /*
        -- Metodo FindByName -- 
         - Exemplo query: SELECT * FROM users WHERE name LIKE %name%

         - Query esta buscando no campo name, aonde tiver caracteres iguais os enviado, pode estar no começo,
         meio ou fim do name
         - Concaternar a variavel com % antes e depois
    */
    public function findByName($name) {
        $array = [];
        if(!empty($name)) {
            $sql = $this->pdo->prepare("SELECT * FROM users WHERE name LIKE :name");
            $sql->bindValue(':name', '%'.$name.'%');
            $sql->execute();
           
            if($sql->rowCount() > 0) {
                
                $data = $sql->fetchAll(PDO::FETCH_ASSOC);

                foreach($data as $item) {
                    $array[] = $this->generateUser($item);
                }
            }
        }

        return $array;
    }

    // Metodo update, recebe um objeto de usuario e atualiza todos os campos verificando pelo id
    public function update(User $u) {
        $sql = $this->pdo->prepare("UPDATE users SET
            email = :email,
            password = :password,
            name = :name,
            birthdate = :birthdate,
            city = :city,
            work = :work,
            avatar = :avatar,
            cover = :cover,
            token = :token
            WHERE id = :id");

        $sql->bindValue(':email', $u->email);
        $sql->bindValue(':password', $u->password);
        $sql->bindValue(':name', $u->name);
        $sql->bindValue(':birthdate', $u->birthdate);
        $sql->bindValue(':city', $u->city);
        $sql->bindValue(':work', $u->work);
        $sql->bindValue(':avatar', $u->avatar);
        $sql->bindValue(':cover', $u->cover);
        $sql->bindValue(':token', $u->token);
        $sql->bindValue(':id', $u->id);
        $sql->execute();

        return true;

    }

    // Metodo recebe um objeto de usuario e insere no BD
    public function insert(User $u) {
        $sql = $this->pdo->prepare("INSERT INTO users (
            name, email, password, birthdate, token
        ) VALUES (
            :name, :email, :password, :birthdate, :token
        )");
        $sql->bindValue(':name', $u->name);
        $sql->bindValue(':email', $u->email);
        $sql->bindValue(':password', $u->password);
        $sql->bindValue(':birthdate', $u->birthdate);
        $sql->bindValue(':token', $u->token);
        $sql->execute();

        return true;
    }
}