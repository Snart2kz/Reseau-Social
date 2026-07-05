<?php
// ==========================
// PAGE D'ACCUEIL - FLUX D'ARTICLES
// ==========================
?>
<div class="navbar">
    <div class="titre">Réseau Social</div>
    <div class="nav-links">
        <a href="#" class="nav-link" data-page="accueil">Accueil</a>
        <a href="#" class="nav-link" data-page="profil">Profil</a>
        <a href="#" class="nav-link" data-page="amis">Amis</a>
        <a href="#" class="nav-link" data-page="chat">Chat</a>
        <a href="#" class="nav-link" data-page="admin" id="nav-admin-link" style="display:none;">Admin</a>
        <a href="#" class="nav-link" data-page="deconnexion">Déconnexion</a>
    </div>
</div>

<div class="formulaire" style="margin-bottom: 20px;">
    <h2>Publier un article</h2>
    <div id="message-article"></div>
    <textarea id="description-article" rows="3" placeholder="Quoi de neuf ?" style="width:100%;margin-bottom:10px;"></textarea>
    <input type="file" id="image-article" accept="image/*">
    <button id="btn-publier">Publier</button>
</div>

<div id="flux-articles"></div>
