<?php
    $firstName = current(explode(' ', $userInfo->name));

    if(!empty($searchTerm)) {
        $valueInput = $searchTerm;
    } else {
        $valueInput = '';
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1"/>
    <link rel="stylesheet" href="<?=$base; ?>/assets/css/style1.css" />
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="<?=$base;?>"><img src="<?=$base; ?>/assets/images/devsbook_logo.png" /></a>
            </div>
            <div class="head-side">
                <div class="head-side-left">
                    <div class="search-area">
                        <form method="GET" action="<?=$base; ?>/search.php">
                            <input type="search" placeholder="Pesquisar" name="s" value="<?=$valueInput;?>" />
                        </form>
                    </div>
                </div>
                <div class="head-side-right">
                    <a href="<?=$base; ?>/perfil.php" class="user-area">
                    <!-- herader.php esta sendo puxado na index, na mesma index eu tenho a variavel $userInfo
                        que tem as informações do usuario logado
                        - firstName e $userInfo->avatar; Utiliza esse varivael para mostrar o nome do usuario
                        logado e o avatar cadastrado no BD
                    -->
                        <div class="user-area-text"><?=$firstName; ?></div>
                        <div class="user-area-icon">
                            <img src="<?=$base;?>/media/avatars/<?=$userInfo->avatar; ?>" />
                        </div>
                    </a>
                    
                    <a href="<?=$base;?>/logout.php" class="user-logout">
                        <img src="<?=$base;?>/assets/images/power_white.png" />
                    </a>
                </div>
            </div>
        </div>
    </header>
    <section class="container main">