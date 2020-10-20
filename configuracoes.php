<?php
/*
    -- Configurações -- 

    - Puxar a conexão com o BD e a sessin pelo config.php
    - Puxar o Auth que vai verificar se o usuario esta logado, se o token é valido e qual usuario esta logado
    qualquer problema com o token redireciona direto para a pagina de login
    - Instanciar um objeto da classe Auth e enviar como parametros e pdo (para configuração no BD) e a $base
    (para redirecionar para o login caso precise)
*/

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->chekToken();

$activeMenu = 'config';

// Estanciar UserDaoMysql
$userDao = new UserDaoMysql($pdo);

require 'partials/header.php';
require 'partials/menu.php';

?>
<section class="feed mt-10">
   
    <h1>Configurações</h1>
    <?php //Verificar se flash ta prenchido, se tiver mostra na tela e apagar ?>
    <?php if( !empty($_SESSION['flash'] )): ?>
        <?=$_SESSION['flash']; ?>
        <?php $_SESSION['flash'] = ''; ?>
    <?php endif ?>

    <!-- enctype="multipart/form-data" - Para poder enviar arquivos -->
    <form method="POST" action="configuracoes_action.php" enctype="multipart/form-data" class="config-form">

        <label>
            Novo Avatar:</br>
            <input type="file" name="avatar" /></br>
            <img class="mini" src="<?=$base;?>/media/avatars/<?=$userInfo->avatar;?>"/>
        </label>

        <label>
            Nova Capa:</br>
            <input type="file" name="cover" /></br>
            <img class="mini" src="<?=$base;?>/media/covers/<?=$userInfo->cover;?>"/>
        </label>

        <hr/>

       

        <label>
            Nome Completo:</br>
            <input type="text" name="name" value="<?=$userInfo->name;?>"/>
        </label>

        <label>
            E-mail:</br>
            <input type="email" name="email" value="<?=$userInfo->email;?>"/>
        </label>

        <label>
            Data de Nascimento:</br>
            <input type="text" id="birthdate" name="birthdate" value=" <?=date('d/m/Y', strtotime($userInfo->birthdate)); ?>"/>
        </label>

        <label>
            Cidade:</br>
            <input type="text" name="city" value="<?=$userInfo->city;?>"/>
        </label>

        <label>
            Trabalho:</br>
            <input type="text" name="work" value="<?=$userInfo->work;?>"/>
        </label>

        <hr/>

        <label>
            Nova Senha:</br>
            <input type="password" name="password" />
        </label>

        <label>
            Confirmar Nova Senha:</br>
            <input type="password" name="password_confirmation" />
        </label>

        <button class="button">Salvar</button>

    </form>


</section>

<!-- Marcara para formatar data -->
<script src="https://unpkg.com/imask"></script>
    <script>
        IMask(
            document.getElementById("birthdate"),
            {mask:'00/00/0000'}
        );
    </script>
<?php
require 'partials/footer.php';
?>
