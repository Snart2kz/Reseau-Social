<?php
// ==========================
// PAGE CHAT
// ==========================
?>
<div class="navbar">
    <div class="titre">Chat</div>
    <div class="nav-links">
        <a href="#" class="nav-link" data-page="accueil">Accueil</a>
        <a href="#" class="nav-link" data-page="profil">Profil</a>
        <a href="#" class="nav-link" data-page="amis">Amis</a>
        <a href="#" class="nav-link" data-page="deconnexion">Déconnexion</a>
    </div>
</div>

<div class="chat-container">
    <div class="chat-sidebar" id="liste-contacts">
        <h3>Contacts</h3>
        <p>Chargement...</p>
    </div>
    <div class="chat-main">
        <h3 id="nom-contact">Sélectionnez un contact</h3>
        <div class="chat-messages" id="conversation-messages">
            <p>Sélectionnez un ami pour commencer à discuter.</p>
        </div>
        <div class="chat-saisie" id="zone-saisie" style="display: none;">
            <input type="text" id="input-message" placeholder="Écrire un message...">
            <button id="btn-envoyer-message">Envoyer</button>
        </div>
    </div>
</div>
