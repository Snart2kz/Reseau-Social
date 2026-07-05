// ==========================
// APPLICATION RÉSEAU SOCIAL
// Toutes les interactions se font via fetch()
// Aucun rechargement de page
// ==========================

const app = document.getElementById("app");
let pageActuelle = "";

// ==========================
// SESSION STORAGE
// ==========================

function getUtilisateur() {
    const data = sessionStorage.getItem("utilisateur");
    return data ? JSON.parse(data) : null;
}

function setUtilisateur(u) {
    sessionStorage.setItem("utilisateur", JSON.stringify(u));
}

function estConnecte() {
    return getUtilisateur() !== null;
}

function estAdmin() {
    const u = getUtilisateur();
    return u && (u.role === "administrateur" || u.role === "moderateur");
}

// ==========================
// NAVIGATION
// ==========================

async function naviguer(vue) {
    try {
        const response = await fetch(vue);
        const html = await response.text();
        app.innerHTML = html;
        pageActuelle = vue;

        // Affiche le lien Admin si l'utilisateur est admin/modo
        const navAdmin = document.getElementById("nav-admin-link");
        if (navAdmin) {
            navAdmin.style.display = estAdmin() ? "inline" : "none";
        }

        initialiserPage();
    } catch (error) {
        app.innerHTML = "<p class='message erreur'>Erreur de chargement.</p>";
    }
}

// ==========================
// AFFICHER UN MESSAGE
// ==========================

function afficherMessage(conteneur, texte, type) {
    const msg = document.createElement("div");
    msg.className = `message ${type}`;
    msg.textContent = texte;
    conteneur.appendChild(msg);
    setTimeout(() => msg.remove(), 4000);
}

// ==========================
// PAGES
// ==========================

function afficherConnexion() { naviguer("api/vue_connexion.php"); }
function afficherInscription() { naviguer("api/vue_inscription.php"); }
function afficherMotDePasseOublie() { naviguer("api/vue_mot_de_passe_oublie.php"); }
async function afficherAccueil() {
    await naviguer("vues/clients/accueil.php");
    chargerArticles();
}
async function afficherProfil() {
    await naviguer("vues/clients/profil.php");
    chargerProfil();
}
async function afficherAmis() {
    await naviguer("vues/clients/amis.php");
    chargerAmis();
}
async function afficherChat() {
    await naviguer("vues/clients/chat.php");
    chargerConversations();
}
async function afficherBackOffice() {
    await naviguer("vues/back-office/dashboard.php");
    chargerDashboard();
}

// ==========================
// INITIALISATION DES ÉVÉNEMENTS PAR PAGE
// ==========================

function initialiserPage() {
    // Navigation globale (liens avec data-page)
    document.querySelectorAll(".nav-link").forEach(lien => {
        lien.addEventListener("click", function(e) {
            e.preventDefault();
            const page = this.dataset.page;
            if (page === "accueil") afficherAccueil();
            else if (page === "profil") afficherProfil();
            else if (page === "amis") afficherAmis();
            else if (page === "chat") afficherChat();
            else if (page === "admin") afficherBackOffice();
            else if (page === "deconnexion") deconnecter();
        });
    });

    // Connexion
    const formConnexion = document.getElementById("form-connexion");
    if (formConnexion) {
        formConnexion.addEventListener("submit", function(e) {
            e.preventDefault();
            connecter(this.email.value, this.mot_de_passe.value);
        });
    }

    // Lien inscription
    const lienInscription = document.getElementById("lien-inscription");
    if (lienInscription) {
        lienInscription.addEventListener("click", function(e) {
            e.preventDefault();
            afficherInscription();
        });
    }

    // Lien mot de passe oublié
    const lienMdpOublie = document.getElementById("lien-mot-de-passe-oublie");
    if (lienMdpOublie) {
        lienMdpOublie.addEventListener("click", function(e) {
            e.preventDefault();
            afficherMotDePasseOublie();
        });
    }

    // Formulaire inscription
    const formInscription = document.getElementById("form-inscription");
    if (formInscription) {
        formInscription.addEventListener("submit", function(e) {
            e.preventDefault();
            inscrire(this.nom.value, this.prenom.value, this.email.value, this.mot_de_passe.value);
        });
    }

    // Lien retour connexion (depuis inscription)
    const retourConnexion = document.getElementById("lien-retour-connexion");
    if (retourConnexion) {
        retourConnexion.addEventListener("click", function(e) {
            e.preventDefault();
            afficherConnexion();
        });
    }

    // Formulaire mot de passe oublié
    const formMdpOublie = document.getElementById("form-mot-de-passe-oublie");
    if (formMdpOublie) {
        formMdpOublie.addEventListener("submit", function(e) {
            e.preventDefault();
            motDePasseOublie(this.email.value);
        });
    }

    // Lien retour connexion (depuis mdp oublié)
    const retourConnexion2 = document.getElementById("lien-retour-connexion2");
    if (retourConnexion2) {
        retourConnexion2.addEventListener("click", function(e) {
            e.preventDefault();
            afficherConnexion();
        });
    }

    // Bouton publier article
    const btnPublier = document.getElementById("btn-publier");
    if (btnPublier) {
        btnPublier.addEventListener("click", function() {
            const desc = document.getElementById("description-article");
            const img = document.getElementById("image-article");
            if (desc.value.trim() !== "") {
                ajouterArticle(desc.value.trim(), img);
                desc.value = "";
                if (img) img.value = "";
            }
        });
    }

    // Bouton modifier profil
    const btnModifierProfil = document.getElementById("btn-modifier-profil");
    if (btnModifierProfil) {
        btnModifierProfil.addEventListener("click", modifierProfil);
    }

    // Bouton changer mot de passe
    const btnChangerMdp = document.getElementById("btn-changer-mot-de-passe");
    if (btnChangerMdp) {
        btnChangerMdp.addEventListener("click", changerMotDePasse);
    }
}

// ==========================
// AUTHENTIFICATION
// ==========================

async function connecter(email, motDePasse) {
    const formData = new FormData();
    formData.append("email", email);
    formData.append("mot_de_passe", motDePasse);

    try {
        const response = await fetch("api/connexion.php", { method: "POST", body: formData });
        const resultat = await response.json();
        const zoneMessage = document.getElementById("message");

        if (resultat.statut === 200) {
            setUtilisateur(resultat.utilisateur);
            afficherAccueil();
        } else if (zoneMessage) {
            afficherMessage(zoneMessage, resultat.message, "erreur");
        }
    } catch (error) {
        console.error("Erreur connexion :", error);
    }
}

async function inscrire(nom, prenom, email, motDePasse) {
    const formData = new FormData();
    formData.append("nom", nom);
    formData.append("prenom", prenom);
    formData.append("email", email);
    formData.append("mot_de_passe", motDePasse);

    try {
        const response = await fetch("api/inscription.php", { method: "POST", body: formData });
        const resultat = await response.json();
        const zoneMessage = document.getElementById("message");

        if (resultat.statut === 200) {
            afficherMessage(zoneMessage, resultat.message, "succes");
            setTimeout(() => afficherConnexion(), 2000);
        } else if (zoneMessage) {
            afficherMessage(zoneMessage, resultat.message, "erreur");
        }
    } catch (error) {
        console.error("Erreur inscription :", error);
    }
}

async function motDePasseOublie(email) {
    const formData = new FormData();
    formData.append("email", email);

    try {
        const response = await fetch("api/mot_de_passe_oublie.php", { method: "POST", body: formData });
        const resultat = await response.json();
        const zoneMessage = document.getElementById("message");
        if (zoneMessage) {
            afficherMessage(zoneMessage, resultat.message, resultat.statut === 200 ? "succes" : "erreur");
        }
    } catch (error) {
        console.error("Erreur :", error);
    }
}

function deconnecter() {
    sessionStorage.removeItem("utilisateur");
    afficherConnexion();
}

// ==========================
// ARTICLES
// ==========================

async function chargerArticles() {
    const conteneur = document.getElementById("flux-articles");
    if (!conteneur) return;

    const user = getUtilisateur();
    const userId = user ? user.id : 0;

    try {
        const response = await fetch(`api/articles.php?utilisateur_id=${userId}`);
        const resultat = await response.json();

        if (resultat.statut === 200) {
            conteneur.innerHTML = "";
            resultat.articles.forEach(article => {
                conteneur.innerHTML += genererArticleHTML(article, user);
            });
            attacherEvenementsArticles();
        }
    } catch (error) {
        console.error("Erreur articles :", error);
    }
}

function genererArticleHTML(article, user) {
    const photo = article.photo_utilisateur || "default.png";
    const imgArticle = article.image ? `<img src="${article.image}" class="image-article" style="max-width:100%;">` : "";
    const likeActif = article.mon_like === "like" ? "like-actif" : "";
    const dislikeActif = article.mon_like === "dislike" ? "dislike-actif" : "";

    return `
        <div class="article" data-id="${article.id}">
            <div class="entete">
                <img src="assets/images/${photo}">
                <div>
                    <div class="nom">${article.prenom} ${article.nom}</div>
                    <small>${article.date_creation}</small>
                </div>
            </div>
            <div class="description">${article.description}</div>
            ${imgArticle}
            <div class="actions">
                <button class="btn-like ${likeActif}" data-id="${article.id}">
                    👍 Like (${article.nb_likes || 0})
                </button>
                <button class="btn-dislike ${dislikeActif}" data-id="${article.id}">
                    👎 Dislike (${article.nb_dislikes || 0})
                </button>
                <button class="btn-commentaires" data-id="${article.id}">
                    💬 Commentaires
                </button>
            </div>
            <div class="commentaires" id="commentaires-${article.id}" style="display: none;"></div>
        </div>
    `;
}

function attacherEvenementsArticles() {
    document.querySelectorAll(".btn-like").forEach(btn => {
        btn.addEventListener("click", function() {
            ajouterLike(this.dataset.id, "like");
        });
    });
    document.querySelectorAll(".btn-dislike").forEach(btn => {
        btn.addEventListener("click", function() {
            ajouterLike(this.dataset.id, "dislike");
        });
    });
    document.querySelectorAll(".btn-commentaires").forEach(btn => {
        btn.addEventListener("click", function() {
            basculerCommentaires(this.dataset.id);
        });
    });
}

async function ajouterLike(articleId, type) {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("article_id", articleId);
    formData.append("type", type);
    formData.append("utilisateur_id", user.id);

    try {
        const response = await fetch("api/likes.php", { method: "POST", body: formData });
        await response.json();
        chargerArticles();
    } catch (error) {
        console.error("Erreur like :", error);
    }
}

async function basculerCommentaires(articleId) {
    const zone = document.getElementById(`commentaires-${articleId}`);
    if (!zone) return;

    if (zone.style.display === "none") {
        zone.style.display = "block";
        await chargerCommentaires(articleId);
    } else {
        zone.style.display = "none";
    }
}

async function chargerCommentaires(articleId) {
    try {
        const response = await fetch(`api/commentaires.php?article_id=${articleId}`);
        const resultat = await response.json();
        const zone = document.getElementById(`commentaires-${articleId}`);
        if (!zone) return;

        if (resultat.statut === 200) {
            let html = "";
            resultat.commentaires.forEach(c => {
                html += `
                    <div class="commentaire">
                        <img src="assets/images/${c.photo_utilisateur || 'default.png'}">
                        <div class="contenu">
                            <div class="nom">${c.prenom} ${c.nom}</div>
                            <div>${c.contenu}</div>
                        </div>
                    </div>`;
            });
            html += `
                <div class="saisie-commentaire">
                    <input type="text" class="input-commentaire" data-id="${articleId}" placeholder="Écrire un commentaire...">
                    <button class="btn-envoyer-commentaire" data-id="${articleId}">Envoyer</button>
                </div>`;
            zone.innerHTML = html;

            document.querySelector(`.btn-envoyer-commentaire[data-id="${articleId}"]`).addEventListener("click", function() {
                const input = document.querySelector(`.input-commentaire[data-id="${articleId}"]`);
                if (input && input.value.trim()) {
                    ajouterCommentaire(articleId, input.value.trim());
                    input.value = "";
                }
            });
        }
    } catch (error) {
        console.error("Erreur commentaires :", error);
    }
}

async function ajouterCommentaire(articleId, contenu) {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("article_id", articleId);
    formData.append("utilisateur_id", user.id);
    formData.append("contenu", contenu);

    try {
        await fetch("api/commentaire_ajouter.php", { method: "POST", body: formData });
        chargerCommentaires(articleId);
    } catch (error) {
        console.error("Erreur ajout commentaire :", error);
    }
}

async function ajouterArticle(description, imageInput) {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("utilisateur_id", user.id);
    formData.append("description", description);
    if (imageInput && imageInput.files && imageInput.files[0]) {
        formData.append("image", imageInput.files[0]);
    }

    try {
        const response = await fetch("api/article_ajouter.php", { method: "POST", body: formData });
        const resultat = await response.json();
        const zone = document.getElementById("message-article");
        if (zone) {
            afficherMessage(zone, resultat.message, resultat.statut === 200 ? "succes" : "erreur");
        }
        if (resultat.statut === 200) chargerArticles();
    } catch (error) {
        console.error("Erreur ajout article :", error);
    }
}

// ==========================
// PROFIL
// ==========================

async function chargerProfil() {
    const user = getUtilisateur();
    if (!user) return;

    try {
        const response = await fetch(`api/profil.php?id=${user.id}`);
        const resultat = await response.json();

        if (resultat.statut === 200) {
            const p = resultat.profil;
            const el = (id) => document.getElementById(id);
            if (el("profil-nom")) el("profil-nom").textContent = `${p.prenom} ${p.nom}`;
            if (el("profil-email")) el("profil-email").textContent = p.email;
            if (el("profil-role")) el("profil-role").textContent = p.role;
            if (el("profil-date")) el("profil-date").textContent = p.date_inscription;
            if (el("profil-photo")) el("profil-photo").src = `assets/images/${p.photo || "default.png"}`;
            if (el("edit-nom")) el("edit-nom").value = p.nom;
            if (el("edit-prenom")) el("edit-prenom").value = p.prenom;
            if (el("edit-email")) el("edit-email").value = p.email;
        }
    } catch (error) {
        console.error("Erreur profil :", error);
    }
}

async function modifierProfil() {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("id", user.id);
    formData.append("nom", document.getElementById("edit-nom").value);
    formData.append("prenom", document.getElementById("edit-prenom").value);
    formData.append("email", document.getElementById("edit-email").value);

    const photoInput = document.getElementById("edit-photo");
    if (photoInput && photoInput.files[0]) {
        formData.append("photo", photoInput.files[0]);
    }

    try {
        const response = await fetch("api/profil_modifier.php", { method: "POST", body: formData });
        const resultat = await response.json();
        const zone = document.getElementById("message-profil");
        if (zone) afficherMessage(zone, resultat.message, resultat.statut === 200 ? "succes" : "erreur");

        if (resultat.statut === 200) {
            user.nom = document.getElementById("edit-nom").value;
            user.prenom = document.getElementById("edit-prenom").value;
            user.email = document.getElementById("edit-email").value;
            setUtilisateur(user);
            chargerProfil();
        }
    } catch (error) {
        console.error("Erreur modif profil :", error);
    }
}

async function changerMotDePasse() {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("id", user.id);
    formData.append("ancien_mot_de_passe", document.getElementById("ancien-mot-de-passe").value);
    formData.append("nouveau_mot_de_passe", document.getElementById("nouveau-mot-de-passe").value);

    try {
        const response = await fetch("api/profil_mot_de_passe.php", { method: "POST", body: formData });
        const resultat = await response.json();
        const zone = document.getElementById("message-mot-de-passe");
        if (zone) afficherMessage(zone, resultat.message, resultat.statut === 200 ? "succes" : "erreur");

        if (resultat.statut === 200) {
            document.getElementById("ancien-mot-de-passe").value = "";
            document.getElementById("nouveau-mot-de-passe").value = "";
        }
    } catch (error) {
        console.error("Erreur changement mdp :", error);
    }
}

// ==========================
// AMIS
// ==========================

async function chargerAmis() {
    const user = getUtilisateur();
    if (!user) return;

    try {
        const response = await fetch(`api/amis.php?utilisateur_id=${user.id}`);
        const resultat = await response.json();

        if (resultat.statut === 200) {
            afficherDemandesRecues(resultat.demandes_recues, user.id);
            afficherGrille(resultat.amis, "liste-amis", "Vous n'avez pas encore d'amis.");
            afficherGrille(resultat.utilisateurs, "utilisateurs-ajout", "Aucun autre utilisateur.", true);
        }
    } catch (error) {
        console.error("Erreur amis :", error);
    }
}

function afficherDemandesRecues(demandes, userId) {
    const conteneur = document.getElementById("demandes-recues");
    if (!conteneur) return;

    if (demandes.length === 0) {
        conteneur.innerHTML = "<p>Aucune demande en attente.</p>";
        return;
    }

    let html = "";
    demandes.forEach(d => {
        html += `
            <div class="carte-utilisateur">
                <img src="assets/images/${d.photo || 'default.png'}">
                <div class="nom">${d.prenom} ${d.nom}</div>
                <button class="btn-accepter" data-id="${d.id}">✓ Accepter</button>
                <button class="btn-refuser" data-id="${d.id}">✗ Refuser</button>
            </div>`;
    });
    conteneur.innerHTML = html;

    document.querySelectorAll(".btn-accepter").forEach(btn => {
        btn.addEventListener("click", async function() {
            await repondreDemande(this.dataset.id, "accepte");
            chargerAmis();
        });
    });
    document.querySelectorAll(".btn-refuser").forEach(btn => {
        btn.addEventListener("click", async function() {
            await repondreDemande(this.dataset.id, "refuse");
            chargerAmis();
        });
    });
}

function afficherGrille(data, elementId, messageVide, avecBoutonAjout = false) {
    const conteneur = document.getElementById(elementId);
    if (!conteneur) return;

    if (data.length === 0) {
        conteneur.innerHTML = `<p>${messageVide}</p>`;
        return;
    }

    let html = "";
    data.forEach(item => {
        html += `
            <div class="carte-utilisateur">
                <img src="assets/images/${item.photo || 'default.png'}">
                <div class="nom">${item.prenom} ${item.nom}</div>
                ${avecBoutonAjout ? `<button class="btn-ajouter-ami" data-id="${item.id}">+ Ajouter</button>` : ""}
            </div>`;
    });
    conteneur.innerHTML = html;

    if (avecBoutonAjout) {
        document.querySelectorAll(".btn-ajouter-ami").forEach(btn => {
            btn.addEventListener("click", async function() {
                await envoyerDemandeAmi(this.dataset.id);
            });
        });
    }
}

async function repondreDemande(demandeId, action) {
    const formData = new FormData();
    formData.append("demande_id", demandeId);
    formData.append("action", action);
    try {
        await fetch("api/repondre_demande.php", { method: "POST", body: formData });
    } catch (error) {
        console.error("Erreur réponse :", error);
    }
}

async function envoyerDemandeAmi(destinataireId) {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("expediteur_id", user.id);
    formData.append("destinataire_id", destinataireId);

    try {
        const response = await fetch("api/demande_amis.php", { method: "POST", body: formData });
        const resultat = await response.json();
        if (resultat.statut === 200) chargerAmis();
    } catch (error) {
        console.error("Erreur demande :", error);
    }
}

// ==========================
// CHAT
// ==========================

let intervalMessages = null;

async function chargerConversations() {
    const user = getUtilisateur();
    if (!user) return;

    try {
        const response = await fetch(`api/amis_liste.php?utilisateur_id=${user.id}`);
        const resultat = await response.json();
        const sidebar = document.getElementById("liste-contacts");
        if (!sidebar) return;

        if (resultat.statut === 200) {
            let html = "<h3>Contacts</h3>";
            html += '<input type="text" id="recherche-contact" placeholder="Rechercher un ami...">';

            resultat.amis.forEach(a => {
                html += `
                    <div class="contact" data-id="${a.id}">
                        <img src="assets/images/${a.photo || 'default.png'}" style="width:30px;height:30px;border-radius:50%;vertical-align:middle;margin-right:8px;">
                        ${a.prenom} ${a.nom}
                    </div>`;
            });

            sidebar.innerHTML = html;

            document.querySelectorAll(".contact").forEach(c => {
                c.addEventListener("click", function() {
                    chargerMessages(this.dataset.id, this.textContent.trim());
                });
            });

            const recherche = document.getElementById("recherche-contact");
            if (recherche) {
                recherche.addEventListener("input", function() {
                    const val = this.value.toLowerCase();
                    document.querySelectorAll(".contact").forEach(c => {
                        c.style.display = c.textContent.toLowerCase().includes(val) ? "block" : "none";
                    });
                });
            }
        }
    } catch (error) {
        console.error("Erreur conversations :", error);
    }
}

async function chargerMessages(contactId, contactNom) {
    document.getElementById("nom-contact").textContent = contactNom;
    document.getElementById("zone-saisie").style.display = "flex";

    if (intervalMessages) clearInterval(intervalMessages);

    await recupererMessages(contactId);

    intervalMessages = setInterval(() => recupererMessages(contactId), 3000);

    document.getElementById("btn-envoyer-message").onclick = async function() {
        const input = document.getElementById("input-message");
        if (input.value.trim()) {
            await envoyerMessage(contactId, input.value.trim());
            input.value = "";
        }
    };
}

async function recupererMessages(contactId) {
    const user = getUtilisateur();
    if (!user) return;

    try {
        const response = await fetch(`api/messages.php?utilisateur_id=${user.id}&destinataire_id=${contactId}`);
        const resultat = await response.json();
        const conteneur = document.getElementById("conversation-messages");
        if (!conteneur) return;

        if (resultat.statut === 200) {
            let html = "";
            resultat.messages.forEach(msg => {
                const estMoi = parseInt(msg.expediteur_id) === parseInt(user.id);
                html += `
                    <div class="chat-message ${estMoi ? 'moi' : ''}">
                        <div class="bulle">${msg.contenu}</div>
                        <small>${msg.date_envoi}</small>
                    </div>`;
            });
            conteneur.innerHTML = html;
            conteneur.scrollTop = conteneur.scrollHeight;
        }
    } catch (error) {
        console.error("Erreur messages :", error);
    }
}

async function envoyerMessage(destinataireId, contenu) {
    const user = getUtilisateur();
    if (!user) return;

    const formData = new FormData();
    formData.append("expediteur_id", user.id);
    formData.append("destinataire_id", destinataireId);
    formData.append("contenu", contenu);

    try {
        const response = await fetch("api/message_envoyer.php", { method: "POST", body: formData });
        await response.json();
        recupererMessages(destinataireId);
    } catch (error) {
        console.error("Erreur envoi :", error);
    }
}

// ==========================
// BACK OFFICE
// ==========================

async function chargerDashboard() {
    try {
        const response = await fetch("api/admin_dashboard.php");
        const resultat = await response.json();

        if (resultat.statut === 200) {
            const el = (id) => document.getElementById(id);
            if (el("stats-utilisateurs")) el("stats-utilisateurs").textContent = resultat.stats.utilisateurs;
            if (el("stats-articles")) el("stats-articles").textContent = resultat.stats.articles;
            if (el("stats-commentaires")) el("stats-commentaires").textContent = resultat.stats.commentaires;
            if (el("stats-messages")) el("stats-messages").textContent = resultat.stats.messages;

            afficherAdminTable(resultat.utilisateurs, "admin-utilisateurs", "utilisateur");
            afficherAdminTable(resultat.articles, "admin-articles", "article");
        }
    } catch (error) {
        console.error("Erreur dashboard :", error);
    }
}

function afficherAdminTable(data, elementId, type) {
    const conteneur = document.getElementById(elementId);
    if (!conteneur) return;

    if (type === "utilisateur") {
        let html = `<table class="admin-table">
            <tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Date</th><th>Action</th></tr>`;
        data.forEach(item => {
            html += `<tr>
                <td>${item.id}</td>
                <td>${item.prenom} ${item.nom}</td>
                <td>${item.email}</td>
                <td>${item.role}</td>
                <td>${item.date_inscription}</td>
                <td><button class="btn-supprimer-admin" data-type="utilisateur" data-id="${item.id}">Supprimer</button></td>
            </tr>`;
        });
        html += "</table>";
        conteneur.innerHTML = html;
    } else {
        let html = `<table class="admin-table">
            <tr><th>ID</th><th>Utilisateur</th><th>Description</th><th>Date</th><th>Action</th></tr>`;
        data.forEach(item => {
            html += `<tr>
                <td>${item.id}</td>
                <td>${item.prenom} ${item.nom}</td>
                <td>${item.description.substring(0, 50)}...</td>
                <td>${item.date_creation}</td>
                <td><button class="btn-supprimer-admin" data-type="article" data-id="${item.id}">Supprimer</button></td>
            </tr>`;
        });
        html += "</table>";
        conteneur.innerHTML = html;
    }

    document.querySelectorAll(".btn-supprimer-admin").forEach(btn => {
        btn.addEventListener("click", async function() {
            if (confirm("Supprimer ?")) {
                const formData = new FormData();
                formData.append("type", this.dataset.type);
                formData.append("id", this.dataset.id);
                try {
                    const response = await fetch("api/admin_supprimer.php", { method: "POST", body: formData });
                    const resultat = await response.json();
                    if (resultat.statut === 200) chargerDashboard();
                } catch (error) {
                    console.error("Erreur suppression :", error);
                }
            }
        });
    });
}

// ==========================
// INITIALISATION
// ==========================

document.addEventListener("DOMContentLoaded", function() {
    if (estConnecte()) {
        afficherAccueil();
    } else {
        afficherConnexion();
    }
});
