<?php

class PostLike {
    public $id;
    public $id_post;
    public $id_user;
    public $created_at;
}
interface PostLikeDAO {
    //Função para pegar número de likes
    public function getLikeCount($id_post);

    // Função para verificar se determindado usuario deu like no Post
    public function isLiked($id_post, $id_user);

    // Função para dar ou tirar o loke do post
    public function likeToggle($id_post, $id_user);
}