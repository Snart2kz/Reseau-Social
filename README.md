# Réseau Social - Exposé PHP & AJAX

Application web de type réseau social développée en PHP et AJAX (Fetch API).

## Architecture

```
social-network/
├── assets/
│   ├── css/style.css
│   ├── images/        (photos de profil)
│   └── js/app.js      (JavaScript principal)
├── vues/
│   ├── clients/       (pages client)
│   │   ├── accueil.php
│   │   ├── profil.php
│   │   ├── amis.php
│   │   └── chat.php
│   └── back-office/
│       └── dashboard.php
├── api/               (API PHP)
│   ├── config.php
│   ├── connexion.php
│   ├── inscription.php
│   ├── mot_de_passe_oublie.php
│   ├── profil.php / profil_modifier.php / profil_mot_de_passe.php
│   ├── articles.php / article_ajouter.php
│   ├── likes.php
│   ├── commentaires.php / commentaire_ajouter.php
│   ├── amis.php / demande_amis.php / repondre_demande.php / amis_liste.php
│   ├── messages.php / message_envoyer.php
│   ├── admin_dashboard.php / admin_supprimer.php
│   ├── vue_connexion.php
│   ├── vue_inscription.php
│   └── vue_mot_de_passe_oublie.php
├── index.html         (point d'entrée)
├── database.sqlite    (base de données)
└── README.md
```

## Fonctionnalités

1. **Authentification** - Inscription, connexion, mot de passe oublié
2. **Page d'accueil** - Articles, likes/dislikes, commentaires
3. **Profil** - Modification infos, photo, mot de passe
4. **Amis** - Demandes, acceptation/refus, liste
5. **Chat** - Messagerie temps réel (rafraîchissement toutes les 3s)
6. **Back Office** - Dashboard statistiques, gestion utilisateurs et articles

## Technologies

- **Frontend** : HTML, CSS, JavaScript natif (Fetch API)
- **Backend** : PHP natif
- **Base de données** : MySQL (via SQLite pour la démonstration)
- **Session** : sessionStorage JavaScript

## Identifiants de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Administrateur | admin@social.com | admin123 |
| Utilisateur | jean@test.com | password123 |
| Utilisateur | sophie@test.com | password123 |

## Installation

### Avec PHP intégré (démonstration) :
```bash
php -S localhost:8080 -t /chemin/vers/social-network/
```

### Avec MySQL (production) :
1. Créer la base de données avec `database.sql`
2. Modifier `api/config.php` : décommenter la section MySQL, commenter SQLite

## Membres du groupe

- AMOUSSOU-GUENOU Donald
- BELLO Deen
- GOUTHON Bachard
- HACHEME Loan

## Dépôt

[Lien vers le dépôt GitHub/GitLab]
