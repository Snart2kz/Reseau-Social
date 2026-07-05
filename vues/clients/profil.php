<?php
// ==========================
// PAGE PROFIL
// ==========================
?>
<div class="navbar">
    <div class="titre">Mon Profil</div>
    <div class="nav-links">
        <a href="#" class="nav-link" data-page="accueil">Accueil</a>
        <a href="#" class="nav-link" data-page="amis">Amis</a>
        <a href="#" class="nav-link" data-page="chat">Chat</a>
        <a href="#" class="nav-link" data-page="deconnexion">Déconnexion</a>
    </div>
</div>

<div class="formulaire" style="max-width: 600px;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img id="profil-photo" src="" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
        <h2 id="profil-nom"></h2>
        <p id="profil-email"></p>
        <p>Rôle : <span id="profil-role"></span></p>
        <p>Membre depuis : <span id="profil-date"></span></p>
    </div>

    <h2>Modifier mes informations</h2>
    <div id="message-profil"></div>
    <input type="text" id="edit-nom" placeholder="Nom">
    <input type="text" id="edit-prenom" placeholder="Prénom">
    <input type="email" id="edit-email" placeholder="Email">
    <input type="file" id="edit-photo" accept="image/*">
    <button id="btn-modifier-profil">Enregistrer</button>

    <h2 style="margin-top: 30px;">Changer le mot de passe</h2>
    <div id="message-mot-de-passe"></div>
    <input type="password" id="ancien-mot-de-passe" placeholder="Ancien mot de passe">
    <input type="password" id="nouveau-mot-de-passe" placeholder="Nouveau mot de passe">
    <button id="btn-changer-mot-de-passe">Changer le mot de passe</button>
</div>
