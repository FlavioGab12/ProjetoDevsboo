<?php
    $firstName = current(explode(' ', $userInfo->name));
?>
<div class="box feed-new">
    <div class="box-body">
        <div class="feed-new-editor m-10 row">
            <div class="feed-new-avatar">
                <img src="<?=$base;?>/media/avatars/<?=$userInfo->avatar;?>" />
            </div>
            <div class="feed-new-input-placeholder">O que você está pensando, <?=$firstName;?>?</div>
            <div class="feed-new-input" contenteditable="true"></div>
            <div class="feed-new-send">
                <img src="<?=$base;?>/assets/images/send.png" />
            </div>
            <!-- 
                Formulario de envio do feed
                    - Criar o formulario para conseguir enviar o que o usuario digitou para outro arquivo
                    - inserir a class para utilizar no codgio javaScript
        
            -->
            <form class="feed-new-form" method="POST" action="<?=$base;?>/feed_editor_action.php">
                <input type="hidden" name="body" />
            </form>
        </div>
    </div>
</div>
<!--
    Codigo javaScript
    - Pega a classe aonde o usuario digitou o texto (.feed-new-input) utiliza o ponto antes pq é uma classe
    - Pega a classe da imagem do botão (.feed-new-send)
    - Pega a classe do meu formulario (.feed-new-form)

    - Quando o botão receber um click a função vai
        - Criar a variavel value, pegar o conteúdo de feedInput tirando os espaços, com a função trim()
        - Dentro do formulario pegar o input com name body, e jogar dentro a varivael value
        - Enviar o formulario para a pagina feed_editor_action.php, utilizando a função submit()
-->
<script>
let feedInput = document.querySelector('.feed-new-input');
let feedSubmit = document.querySelector('.feed-new-send');
let feedForm = document.querySelector('.feed-new-form');

feedSubmit.addEventListener('click', function(){
    let value = feedInput.innerText.trim();

    feedForm.querySelector('input[name=body]').value = value;
    feedForm.submit();
});

</script>