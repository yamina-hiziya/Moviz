# 🎬 Mooviz

Site de notation et de critique de films, développé en **PHP orienté objet** avec une architecture **MVC**, une base **MySQL** pour les données métier et une base **MongoDB** pour les statistiques de consultation.

Projet réalisé dans le cadre du titre professionnel **Développeur Web et Web Mobile (DWWM)**, pour le bloc de compétences **CCP 2 — Développer la partie back-end d'une application web ou web mobile sécurisée**. Il part du projet pédagogique « Moviz » du cours PHP POO de Studi, entièrement corrigé, sécurisé et enrichi (voir [Améliorations apportées](#-améliorations-apportées-au-projet-de-départ)).

---

## Sommaire

- [Fonctionnalités](#-fonctionnalités)
- [Technologies](#-technologies)
- [Architecture](#-architecture)
- [Bases de données](#-bases-de-données)
- [Sécurité](#-sécurité)
- [Installation en local](#-installation-en-local)
- [Import de films depuis TMDB](#-import-de-films-depuis-tmdb-facultatif)
- [Commandes utiles](#-commandes-utiles)
- [Déploiement en production](#-déploiement-en-production)
- [Améliorations apportées au projet de départ](#-améliorations-apportées-au-projet-de-départ)
- [Crédits](#-crédits)

---

## ✨ Fonctionnalités

**Visiteur**

- Parcourir la liste des films et consulter leur fiche : synopsis, date de sortie, durée, genres, réalisateurs, affiche
- Voir la note moyenne et les critiques validées
- Voir les films les plus consultés et les derniers films ajoutés sur l'accueil
- Créer un compte

**Membre connecté**

- Noter un film de 1 à 5 étoiles, avec une critique facultative (un seul avis par film)
- La note compte immédiatement dans la moyenne ; la critique est visible après validation

**Administrateur**

- Valider ou supprimer les critiques en attente
- Ajouter, modifier et supprimer des films (affiche, genres, réalisateurs)
- Consulter le nombre de vues de chaque film

---

## 🛠 Technologies

| Domaine            | Technologie                                                   |
| ------------------ | ------------------------------------------------------------- |
| Langage            | PHP 8.2 (orienté objet, sans framework)                       |
| Base relationnelle | MySQL 8.0, accès via PDO et requêtes préparées                |
| Base NoSQL         | MongoDB 7, accès via l'extension officielle `mongodb`         |
| Front-end          | HTML, CSS, Bootstrap 5.3.3, polices Playfair Display et Inter |
| Environnement      | Docker et Docker Compose (Apache, MySQL, MongoDB, phpMyAdmin) |
| Données de films   | API The Movie Database (TMDB)                                 |
| Versionnement      | Git et GitHub                                                 |

---

## 🏗 Architecture

Le projet suit le modèle **MVC** (Modèle - Vue - Contrôleur) :

```
index.php                  Point d'entrée unique : session, autoload, routeur
config.php                 Constantes (rôles, dossiers d'images)
db_config.php              Connexion MySQL (lit les variables d'environnement)
App/
├── Controller/            Contrôleurs : reçoivent la requête, appellent le modèle, affichent la vue
│   ├── Controller.php         Routeur principal + méthode render()
│   ├── PageController.php     Accueil
│   ├── MovieController.php    Liste et fiche des films, envoi d'un avis
│   ├── AuthController.php     Connexion / déconnexion
│   ├── UserController.php     Inscription
│   └── AdminController.php    Espace administrateur (accès réservé)
├── Entity/                Entités : un objet par table (Movie, User, Review, Genre, Director)
├── Repository/            Accès aux données : toutes les requêtes SQL et MongoDB
├── Db/
│   ├── Mysql.php              Connexion PDO (singleton)
│   └── Mongo.php              Connexion MongoDB (singleton)
└── Tools/                 Outils : sécurité (CSRF), upload d'images, navigation
templates/                 Vues (HTML + PHP)
assets/                    CSS et images du site
uploads/movies/            Affiches des films
bin/import_tmdb.php        Script d'import des films depuis l'API TMDB (ligne de commande)
database/mooviz.sql        Script SQL : structure et données de démonstration
libs/                      MCD, MLD et sujet du projet
```

**Exemple de parcours d'une requête :** `index.php?controller=movie&action=show&id=12`

1. `index.php` démarre la session et appelle le routeur `Controller`
2. Le routeur instancie `MovieController`, qui appelle `show()`
3. `show()` interroge `MovieRepository`, `ReviewRepository` (MySQL) et `MovieViewRepository` (MongoDB)
4. Les repositories renvoient des entités (`Movie`, `Review`…)
5. Le contrôleur transmet ces données à la vue `templates/movie/show.php`, qui génère le HTML

---

## 🗄 Bases de données

### MySQL : les données métier

Les films, les utilisateurs et les critiques ont besoin de **relations** et de **contraintes d'intégrité** : ils sont stockés dans MySQL. Le MCD et le MLD sont dans le dossier `libs/`.

| Table                         | Contenu                                                                    |
| ----------------------------- | -------------------------------------------------------------------------- |
| `movie`                       | Films (titre, synopsis, date de sortie, durée, affiche, identifiant TMDB)  |
| `genre` / `movie_genre`       | Genres et liaison plusieurs-à-plusieurs avec les films                     |
| `director` / `movie_director` | Réalisateurs et liaison plusieurs-à-plusieurs avec les films               |
| `user`                        | Comptes (mot de passe haché, rôle `user` ou `admin`)                       |
| `review`                      | Notes et critiques (un avis par utilisateur et par film, champ `approved`) |

### MongoDB : les statistiques de consultation

Chaque affichage d'une fiche film incrémente un compteur. Un document par film dans la collection `movie_views` :

```json
{ "movie_id": 12, "views": 37, "last_viewed_at": "2026-11-20T18:42:00Z" }
```

**Pourquoi MongoDB pour ces données ?**

- beaucoup d'écritures très simples (une à chaque visite), réalisées en une opération atomique `$inc` avec `upsert`, sans charger la base relationnelle ;
- aucune relation ni jointure nécessaire ;
- un format document souple, qui peut évoluer (vues par jour, par exemple) sans modifier de schéma.

Le site fonctionne même si MongoDB est indisponible : les appels sont protégés par `try/catch` et un délai de connexion de 2 secondes ; seules les statistiques disparaissent alors de l'affichage.

---

## 🔒 Sécurité

| Risque                | Protection mise en place                                                                                                                  |
| --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| Injection SQL         | Requêtes préparées PDO avec paramètres typés (`PARAM_INT`, `PARAM_STR`)                                                                   |
| Faille XSS            | Échappement de toutes les données affichées avec `htmlspecialchars()` ; messages de confirmation en liste blanche                         |
| Attaque CSRF          | Jeton aléatoire (`random_bytes`) vérifié avec `hash_equals()` sur tous les formulaires ; cookie de session `SameSite=Lax`                 |
| Vol de session        | Cookie `HttpOnly` (et `Secure` en HTTPS) ; `session_regenerate_id()` à la connexion et à la déconnexion                                   |
| Mots de passe         | Hachage avec `password_hash()` (bcrypt), 8 caractères minimum, email unique                                                               |
| Contrôle d'accès      | Rôle `admin` vérifié au début de chaque action de `AdminController` (code HTTP 403 sinon)                                                 |
| Actions sensibles     | Modifications et suppressions uniquement en POST, jamais par un simple lien                                                               |
| Upload de fichiers    | Type MIME réel lu avec `finfo`, JPEG/PNG/WebP uniquement, 2 Mo maximum, nom de fichier aléatoire, `basename()` contre le _path traversal_ |
| Cohérence des données | Transactions PDO lors de l'enregistrement et de la suppression d'un film                                                                  |
| Secrets               | Identifiants lus dans les variables d'environnement ; clé TMDB dans un fichier exclu de Git                                               |
| Script d'import       | Exécution refusée depuis le navigateur (`PHP_SAPI`)                                                                                       |
| Dépendances front     | Bootstrap chargé avec vérification d'intégrité (SRI)                                                                                      |

---

## 🚀 Installation en local

### Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Docker et Docker Compose)
- Git

### 1. Cloner le projet

```bash
git clone https://github.com/yamina-hiziya/Moviz.git
cd Moviz
```

### 2. Lancer les conteneurs

```bash
docker compose up -d --build
```

Au premier lancement :

- l'image PHP est construite avec les extensions `pdo_mysql` et `mongodb` (quelques minutes) ;
- MySQL importe automatiquement le script `database/mooviz.sql` (structure et données de démonstration).

### 3. Créer l'index MongoDB

```bash
docker exec moviz-mongo-1 mongosh -u mooviz -p mooviz_dev --quiet --eval "db.getSiblingDB('mooviz').movie_views.createIndex({ movie_id: 1 }, { unique: true })"
```

### 4. Ouvrir le site

| Service                               | Adresse                                                               |
| ------------------------------------- | --------------------------------------------------------------------- |
| Site Mooviz                           | http://localhost:8086                                                 |
| phpMyAdmin                            | http://localhost:8085 (utilisateur `moviz`, mot de passe `moviz_dev`) |
| MySQL (client externe)                | `localhost:3311`                                                      |
| MongoDB (client externe, ex. Compass) | `mongodb://mooviz:mooviz_dev@localhost:27018`                         |

### Comptes de démonstration

| Rôle           | Email            | Mot de passe |
| -------------- | ---------------- | ------------ |
| Administrateur | `test@moviz.fr`  | `Test1234`   |
| Membre         | `test2@moviz.fr` | `Test1234`   |

### Variables d'environnement

Les identifiants ont des **valeurs par défaut de développement** définies dans `docker-compose.yml`. Pour les modifier, créer un fichier `.env` à la racine (il n'est pas versionné) :

```env
MYSQL_DATABASE=moviz
MYSQL_USER=moviz
MYSQL_PASSWORD=un_mot_de_passe_solide
MYSQL_ROOT_PASSWORD=un_autre_mot_de_passe_solide
MONGO_USER=mooviz
MONGO_PASSWORD=encore_un_mot_de_passe_solide
MONGO_DATABASE=mooviz
```

---

## 🎞 Import de films depuis TMDB (facultatif)

Le script `bin/import_tmdb.php` importe les films populaires de The Movie Database : titre, synopsis en français, date, durée, affiche, genres et réalisateurs. Chaque film est enregistré dans une transaction, et les films déjà présents sont ignorés (colonne `tmdb_id`).

1. Créer un compte gratuit sur [themoviedb.org](https://www.themoviedb.org/) et demander une clé API (type _Developer_).
2. Créer le fichier de configuration et y coller le « Jeton d'accès en lecture à l'API » :
   ```bash
   cp tmdb_config.example.php tmdb_config.php
   ```
3. Lancer l'import :
   ```bash
   docker exec -w /var/www/html moviz-web-1 php bin/import_tmdb.php
   ```

---

## 🌍 Déploiement en production

Le projet est conçu pour fonctionner en local. Pour une mise en ligne, voici la procédure et les points à adapter :

1. **Serveur** : un hébergement capable de faire tourner Docker (VPS), ou un hébergement PHP 8.2 avec MySQL, MongoDB (ou MongoDB Atlas) et l'extension PHP `mongodb`.
2. **Code** : cloner le dépôt sur le serveur (`git clone`), puis le mettre à jour avec `git pull`.
3. **Secrets** : créer un fichier `.env` avec des mots de passe forts (voir [Variables d'environnement](#variables-denvironnement)). Ne jamais utiliser les valeurs par défaut de développement.
4. **Ports** : ne pas exposer MySQL (3311), MongoDB (27018) ni phpMyAdmin (8085) sur Internet ; supprimer leurs lignes `ports` ou les limiter à `127.0.0.1`.
5. **HTTPS** : installer un certificat (par exemple Let's Encrypt). Le cookie de session passe alors automatiquement en `Secure` (`index.php`).
6. **Base de données** : importer `database/mooviz.sql` (automatique avec Docker), puis créer l'index MongoDB (étape 3 de l'installation) et changer les mots de passe des comptes de démonstration.
7. **Droits** : le serveur web doit pouvoir écrire dans `uploads/movies/` :
   ```bash
   docker exec moviz-web-1 chown -R www-data:www-data /var/www/html/uploads/movies
   ```
8. **PHP** : désactiver l'affichage des erreurs (`display_errors = Off`) et les enregistrer dans les logs.
9. **Sauvegardes** : planifier des exports réguliers avec `mysqldump` (MySQL) et `mongodump` (MongoDB).

---

## 📈 Améliorations apportées au projet de départ

Le projet de départ du cours a été repris et amélioré :

- **Corrections de bugs** : lien vers les fiches, image par défaut, rôle à l'inscription, requête `UPDATE` des utilisateurs, redirections sans `exit`, bouton de l'accueil
- **Sécurité** : XSS, CSRF, cookie de session, email unique, mot de passe de 8 caractères minimum, upload sécurisé
- **Notes et critiques** : notation par étoiles, moyenne, modération
- **Espace administrateur** : modération des critiques et gestion complète des films
- **Import TMDB** : script d'import des films populaires via l'API
- **MongoDB** : compteur de vues et films les plus consultés
- **Nouvelle identité visuelle** : logo Mooviz, charte bleu nuit et or, menu responsive
- **Documentation** : script SQL, Docker Compose et ce README

---

## 🙏 Crédits

- Projet de départ : cours « PHP - POO » de Studi
- Données et affiches de films : [The Movie Database (TMDB)](https://www.themoviedb.org/). Ce site utilise l'API TMDB mais n'est ni approuvé ni certifié par TMDB.
- Logo Mooviz : création originale

Réalisé par **Yamina** — [github.com/yamina-hiziya](https://github.com/yamina-hiziya)
