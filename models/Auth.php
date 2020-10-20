<?php
/*
    -- Classe Auth --

    - Responsavel por verificar se o usuario esta logado, e retornar um usuario
*/

require_once 'dao/UserDaoMysql.php';
class Auth {
    // private $pdo - para fazer a integração com o BD
    // private $base - para fazer o redirecinamento com o link completo
    // private $dao - pois estou usando a classe UserDaoMysql em todos os metodos
    private $pdo;
    private $base;
    private $dao;

    // Class Auth recebe o pdo e a base via construtor
    public function __construct(PDO $pdo, $base) {
        $this->pdo = $pdo;
        $this->base = $base;
        $this->dao = new UserDaoMysql($this->pdo);
    }

    // - Verifica se token existe e se esta vazio, caso não entre no if redirecionar para login
    // - Implementar UserDaoMysql enviar o pdo (por causa do constrtutor) enviar o token para a função findByToken
    public function chekToken() {
        if (!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $user = $this->dao->findByToken($token);

            if ($user) {
                return $user;
            }
        }

        header("Location: ".$this->base."/login.php");
        exit;
    }


    /*
        - Metodo validarLogin, recebe o email e senha do login_action
        - Entancia a DaoMysql pois é ele que vai fazer de fato as consultas
        - Usa o dao para verificar se email existe
            - se exite verificar se a senha é igual a hash do BD
            - Gerar um token
            - Salvar token na sessao
            - Atualizar token no BD
        - Se tudo de certo retornar verdadeiro para o login_action
    */
    public function validateLogin($email, $password) {
        

        $user = $this->dao->findByEmail($email);
        
        if ($user) {

            if (password_verify($password, $user->password)) {
                $token = md5(time().rand(0, 9999));
                
                $_SESSION['token'] = $token;
                $user->token = $token;
                $this->dao->update($user);
    
                return true;
            }

        }

        return false;
    }

    // Função para verificar se um email ja existe, retorna true caso ache um email
    // Usar emailExists para manter a organização do codigo
    public function emailExists($email) {
        
        return $this->dao->findByEmail($email) ? true : false;
    }

    /*
        Metodo registerUser
        - Recebe os dados do usuario
        - Estancar o UserDaoMysql
        - Gera o token (para inserir no BD e o usuario conseguir logar)
        - gera o hash da senha
        - monta o objeto de usuario e envia o objeto para o insert do UserDaoMysql
        - insere o token gerado na sessao
    */
    public function registerUser($name, $email, $password, $birthdate) {
        
        $token = md5(time().rand(0, 9999));
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $newUser = new User();
        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->password = $hash;
        $newUser->birthdate = $birthdate;
        $newUser->token = $token;
    
        $this->dao->insert($newUser);

        $_SESSION['token'] = $token;

    }

}