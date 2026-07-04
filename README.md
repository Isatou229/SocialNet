# SocialNet ESGIS

Réseau social web développé en **PHP natif (API) + HTML/CSS/JavaScript (AJAX, sans framework)
+ MySQL**, dans le cadre de l'examen final "TP Réseau Social Web en PHP et AJAX" — ESGIS Cotonou.

## 1. Description du projet

SocialNet ESGIS permet aux étudiants de :
- créer un compte et se connecter (session gérée côté client via `sessionStorage`) ;
- publier des articles (texte + image), liker/disliker, commenter sans rechargement de page ;
- rechercher des utilisateurs, envoyer/accepter/refuser des invitations d'amitié ;
- consulter et modifier leur profil (infos, photo, mot de passe) ;
- échanger des messages privés (texte + image), actualisés toutes les 3 secondes ;
- pour les rôles **modérateur** et **administrateur** : un back-office indépendant avec
  dashboard, gestion des utilisateurs, modération des publications, et gestion des rôles
  (administrateur uniquement).

## 2. Architecture

```
index.html                  -> page d'entrée, redirige vers connexion ou accueil
assets/css/style.css         -> styles partagés
assets/js/                   -> common.js, auth.js, feed.js, friends.js, profile.js, chat.js, admin.js
vues/clients/                -> pages utilisateur (connexion, inscription, accueil, profil, amis, chat, parametres...)
vues/back-office/            -> pages admin/modérateur (login-admin, dashboard, utilisateurs, articles, roles)
api/                         -> scripts PHP (auth, posts, comments, likes, friends, users, messages, admin)
includes/                    -> config.php, fonctions utilitaires, vérification d'authentification, emails HTML
database/socialnet.sql       -> script de création de la base de données (8 tables)
emails_log/                  -> copie HTML des emails envoyés (utile si le SMTP local n'est pas configuré)
```

## 3. Mode de fonctionnement (installation locale avec XAMPP)

1. Copier le dossier du projet dans `C:\xampp\htdocs\socialnet`
2. Démarrer **Apache** et **MySQL** depuis le panneau de contrôle XAMPP
3. Ouvrir `http://localhost/phpmyadmin`, créer la base, puis importer `database/socialnet.sql`
   (le script crée la base `socialnet` et insère 3 comptes de test)
4. Ouvrir `http://localhost/socialnet/` dans le navigateur

Aucune dépendance externe (pas de Composer, pas de Node.js obligatoire) : tout fonctionne
avec PHP natif + PDO + MySQL, livré nativement avec XAMPP.

## 4. Gestion des sessions

Conformément à la consigne ("session gérée via sessionStorage"), l'authentification ne
repose pas sur `$_SESSION` côté serveur mais sur un **token** :
- généré à la connexion (`api/auth/login.php`) et stocké dans la table `auth_tokens` ;
- renvoyé au client, qui le conserve dans `sessionStorage` avec les infos utilisateur ;
- renvoyé par le client dans le header `Authorization: Bearer <token>` à chaque appel AJAX.

## 5. Emails HTML

Les emails (bienvenue à l'inscription, réinitialisation de mot de passe) sont générés en
HTML (voir `includes/mailer.php`). En local, `mail()` de PHP nécessite un serveur SMTP
configuré (souvent absent par défaut sur XAMPP) : chaque email est donc aussi enregistré
dans `/emails_log/` pour pouvoir être ouvert et présenté tel quel à l'oral.

## 6. Identifiants de test

| Rôle          | Email                  | Mot de passe |
|---------------|-------------------------|--------------|
| Administrateur| admin@socialnet.test    | Test1234     |
| Modérateur    | modo@socialnet.test     | Test1234     |
| Utilisateur   | user@socialnet.test     | Test1234     |

Connexion utilisateur : `vues/clients/connexion.html`
Connexion back-office : `vues/back-office/login-admin.html`

## 7. Groupe

- Numéro de groupe : _à compléter_
- Membres : _à compléter (Nom Prénom — rôle/tâches)_
- Lien du dépôt GitHub/GitLab : _à compléter_

## 8. Tâches réalisées par membre

| Membre | Tâches |
|--------|--------|
| _Nom 1_ | Authentification, emails HTML, gestion des sessions |
| _Nom 2_ | Publications, likes, commentaires |
| _Nom 3_ | Gestion des amis, profils utilisateurs |
| _Nom 4_ | Messagerie, back-office, dashboard |

_(à adapter selon la répartition réelle et les commits de chacun sur le dépôt Git)_
