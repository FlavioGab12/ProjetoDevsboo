<?php
/*
    -- Fotos --
    
    
*/
require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/PostDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->chekToken();
$activeMenu = 'photos';

// Pegando ID do usuario, caso não tenha é o id do usuario logado
$id = filter_input(INPUT_GET, 'id');

if(!$id) {
    $id = $userInfo->id;
}

// Verificar se o ID do perfil, é diferente do ID do usuario logado
if ($id != $userInfo->id) {
    $activeMenu = '';
}

$postDao = new PostDaoMysql($pdo);
$userDao = new UserDaoMysql($pdo);

// 1. Pegar informações do Usuario, e verificar se trouxe um usuario existente
$user = $userDao->findById($id, true);
if(!$user) {
   header("Location:".$base);
   exit;
}


// Verificando a diferença em Anos, da data de nascimento do Usuario, com a data atual
$dateFrom = new DateTime($user->birthdate);
$dateTo = new DateTime('today');
$user->ageYears = $dateFrom->diff($dateTo)->y;

// 3. Verificar se eu sigo esse Usuario


require 'partials/header.php';
require 'partials/menu.php';
?>

<section class="feed">

    <!-- Informações gerais do usuario -->
    <div class="row">
        <div class="box flex-1 border-top-flat">
            <div class="box-body">
                <div class="profile-cover" style="background-image: url('<?=$base;?>/media/covers/<?=$user->cover;?>');"></div>
                <div class="profile-info m-20 row">
                    <div class="profile-info-avatar">
                        <img src="<?=$base;?>/media/avatars/<?=$user->avatar;?>" />
                    </div>
                    <div class="profile-info-name">
                        <div class="profile-info-name-text"><?=$user->name;?></div>

                        <?php if(!empty($user->city)): ?>
                        <div class="profile-info-location"><?=$user->city;?></div>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info-data row">
                        <div class="profile-info-item m-width-20">
                            <div class="profile-info-item-n"><?=count($user->followers);?></div>
                            <div class="profile-info-item-s">Seguidores</div>
                        </div>
                        <div class="profile-info-item m-width-20">
                            <div class="profile-info-item-n"><?=count($user->following);?></div>
                            <div class="profile-info-item-s">Seguindo</div>
                        </div>
                        <div class="profile-info-item m-width-20">
                            <div class="profile-info-item-n"><?=count($user->photos);?></div>
                            <div class="profile-info-item-s">Fotos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="column">                
            <div class="box">
                <div class="box-body">

                    <div class="full-user-photos">

                        <?php foreach($user->photos as $key => $item): ?>
                            <div class="user-photo-item">
                                <a href="#modal-<?=$key;?>" rel="modal:open">
                                    <img src="<?=$base;?>/media/uploads/<?=$item->photo;?>" />
                                </a>
                                <div id="modal-<?=$key;?>" style="display:none">
                                    <img src="<?=$base;?>/media/uploads/<?=$item->photo;?>" />
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if(count($user->photos) === 0): ?>
                            Não há fotos deste usuário.
                        <?php endif; ?>

                    </div>
                    
                </div>
            </div>
        </div>
        
    </div>

</section>

<?php
require 'partials/footer.php';
?>
