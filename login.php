<?php
    require 'config.php';
    // Colocar a $base em todos os links para garantir que esta acessadno o arquivo correto
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1"/>
    <link rel="stylesheet" href="<?=$base; ?>/assets/css/login.css" />
</head>
<body>
    <header>
        <div class="container">
            <a href="<?=$base; ?>"><img src="<?=$base; ?>/assets/images/devsbook_logo.png" /></a>
        </div>
    </header>
    <section class="container main">
        <form method="POST" action="<?=$base; ?>/login_action.php">

            <?php //Verificar se flash ta prenchido, se tiver mostra na tela e apagar ?>
            <?php if( !empty($_SESSION['flash'] )): ?>

                <?=$_SESSION['flash']; ?>
                <?php $_SESSION['flash'] = ''; ?>
                
            <?php endif ?>

            <input placeholder="Digite seu E-mail" class="input" type="email" name="email" />

            <input placeholder="Digite sua Senha" class="input" type="password" name="password" />

            <input class="button" type="submit" value="Acessar o sistema" />

            <a href="<?=$base; ?>/signup.php">Ainda não tem conta? Cadastre-se</a>
        </form>
    </section>
</body>
</html>