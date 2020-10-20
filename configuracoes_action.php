<?php
/*
    -- Alterar informações do Usuario -- 

*/

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->chekToken();


// Estanciar UserDaoMysql
$userDao = new UserDaoMysql($pdo);


$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$birthdate = filter_input(INPUT_POST, 'birthdate');
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS);
$work = filter_input(INPUT_POST, 'work', FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password');
$password_confirmation = filter_input(INPUT_POST, 'password_confirmation');

// Todo Usuario precisa ter nome e email
if($name && $email) {

    // Preenchar informações que não precisa de verificação
    $userInfo->name = $name;
    $userInfo->city = $city;
    $userInfo->work = $work;

    // Verifica se Email logado e diferente do email recebido
    if($userInfo->email != $email) {

        if($userDao->findByEmail($email) === false) {
            $userInfo->email = $email;
        } else {
            $_SESSION['flash'] = 'E-mail ja existente!';
            header("Location:".$base."/configuracoes.php");
            exit;
        }
    }
        
    // Verificar se data de nascimento é valida, mesmo codigo do signup_action.php
    $birthdate = explode('/', $birthdate);
    if (count($birthdate) != 3) {
        $_SESSION['flash'] = 'Data de nascimento invalida!';
        header("Location:".$base."/configuracoes.php");
        exit;
    }
    $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
    if ( strtotime($birthdate) === false) {
        $_SESSION['flash'] = 'Data de nascimento não existe!';
        header("Location:".$base."/configuracoes.php");
        exit;
    }

    $userInfo->birthdate = $birthdate;

    // Verificar se senha esta preenchida, se sáo iguais e gerar um hash da senha
    if(!empty($password)){
        if($password === $password_confirmation) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userInfo->password = $hash;
        } else {
            $_SESSION['flash'] = 'Campo senha ou confirmar senha invalidos!';
            header("Location:".$base."/configuracoes.php");
            exit;
        }
    }

    /*
        -- Avatar --
        - Verifica se foi enviado arquivo com name avatar, e se nome temporario esta preenchido
        - Verifica se tipo é igual a jpeg, jpeg ou png
    */

   if( isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name']) ){

       
        $newAvatar = $_FILES['avatar'];
        if (in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'] )) {
            $avatarWidth = 200;
            $avatarHeight = 200;

            // Pegar tamanhos originais da imagem, e gerar o ratio (proporção)
            list($widthOrig, $heightOrig) = getimagesize($newAvatar['tmp_name']);
            $ratio = $widthOrig / $heightOrig;

            // nova altura recebe altura que eu quero, mexer na largura pra ficar proporcional
            $newWidth = $avatarWidth;
            $newHeight = $newWidth / $ratio;    

            // se nova largura for menor que 200, nova largura recebe os 200, aumentar altura pra ficar proporcional
            if($newHeight < $avatarHeight) {
                $newHeight = $avatarHeight;
                $newWidth * $newHeight * $ratio;
            }
        }

        // Criar x e Y para cortar imagem se for maior do que zero
        $x = $avatarWidth - $newWidth;
        $y = $avatarHeight - $newHeight;
        $x = $x<0 ? $x/2 : $x;
        $y = $y<0 ? $y/2 : $y;

        // Função para criar imagem do zero, que esta vazia no momento 
        $finalImage = imagecreatetruecolor($avatarWidth, $avatarHeight);
        switch($newAvatar['type']) {
            case 'image/jpeg':
            case 'image/jpg' :

                // Função para carregar imagem que sera usada como referencia
                $image = imagecreatefromjpeg($newAvatar['tmp_name']);
            break;
            case 'iamge/png' :
                $image = imagecreatefrompng($newAvatar['tmp_name']);
            break;
        }

        // Função para copiar imagem referencia, dentro da imagem criada, com os devidos cortes
        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $avatarName = md5(time().rand(0, 9999)).'.jpg';

        imagejpeg($finalImage, './media/avatars/'.$avatarName, 100);

        $userInfo->avatar = $avatarName;
   }

   // COVER
   if( isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name']) ){

        $newCover = $_FILES['cover'];
        if (in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'] )) {
            $coverWidth = 850;
            $coverHeight = 313;

            list($widthOrig, $heightOrig) = getimagesize($newCover['tmp_name']);
            $ratio = $widthOrig / $heightOrig;

            $newWidth = $coverWidth;
            $newHeight = $newWidth / $ratio;    

            if($newHeight < $coverHeight) {
                $newHeight = $coverHeight;
                $newWidth * $newHeight * $ratio;
            }
        }

        $x = $coverWidth - $newWidth;
        $y = $coverHeight - $newHeight;
        $x = $x<0 ? $x/2 : $x;
        $y = $y<0 ? $y/2 : $y;

        $finalImage = imagecreatetruecolor($coverWidth, $coverHeight);
        switch($newCover['type']) {
            case 'image/jpeg':
            case 'image/jpg' :
                $image = imagecreatefromjpeg($newCover['tmp_name']);
            break;
            case 'image/png' :
                $image = imagecreatefrompng($newCover['tmp_name']);
            break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $coverName = md5(time().rand(0, 9999)).'.jpg';

        imagejpeg($finalImage, './media/covers/'.$coverName, 100);

        $userInfo->cover = $coverName;
    }

    $userDao->update($userInfo);

} 

header("Location: ".$base."/configuracoes.php");
exit;


