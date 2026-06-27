# 💎 La Cristal des Bijoux Kabyle

> Application web e-commerce de vente de bijoux traditionnels kabyles, développée de A à Z en PHP, HTML/CSS et JavaScript vanille.

🌐 **[Voir la démo en ligne →](https://bijouxkabyle.freedev.app/)**

---

## 🖼️ Aperçu

| Page | Description |
|------|-------------|
| `index.html` | Catalogue de 20 produits organisés par catégorie |
| `commande_produit.html` | Fiche produit avec ajout au panier |
| `panier.html` | Panier interactif avec récapitulatif et confirmation |
| `login.html` | Authentification utilisateur |
| `inscription.html` | Création de compte avec validation complète |

---

## ✨ Fonctionnalités

- 🛒 **Panier dynamique** — ajout, suppression, modification de quantité (localStorage)
- 🔐 **Authentification sécurisée** — hachage bcrypt des mots de passe, sessions PHP
- 🛡️ **Protection SQL Injection** — requêtes préparées (PDO-style avec MySQLi)
- 📦 **Commande en lot** — envoi AJAX de tout le panier en une seule transaction
- ✅ **Validation double** — côté client (JavaScript) et côté serveur (PHP)
- 📱 **Interface responsive** — backdrop-filter, animations CSS, toast notifications

---

## 🗂️ Structure du projet

```
la-cristal-bijoux/
├── index.html               # Page d'accueil / catalogue
├── commande_produit.html    # Fiche produit
├── panier.html              # Panier d'achat
├── login.html               # Connexion
├── inscription.html         # Inscription
│
├── panier.js                # Moteur du panier (localStorage)
├── login.js                 # Validation formulaire login
├── inscription.js           # Validation formulaire inscription
├── commande_produit.js      # (legacy)
│
├── login.php                # Authentification + session
├── inscription.php          # Création de compte
├── commande.php             # Commande produit unique
├── commande_lot.php         # Commande panier complet (AJAX/JSON)
│
├── config.example.php       # ← Modèle de config (copier → config.php)
├── config.php               # ← NON versionné (.gitignore)
│
├── *.css                    # Feuilles de style par page
└── IMAGES/                  # Visuels produits
```

---

## 🚀 Installation locale

### Prérequis
- PHP ≥ 8.0
- MySQL / MariaDB
- Serveur local : XAMPP, WAMP, Laragon ou équivalent

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/TON_USERNAME/la-cristal-bijoux.git
cd la-cristal-bijoux

# 2. Créer le fichier de configuration
cp config.example.php config.php
# Puis éditer config.php avec vos identifiants de base de données
```

### Base de données

Créer les tables suivantes dans votre base MySQL :

```sql
-- Table clients
CREATE TABLE client (
    Id_Clt     INT AUTO_INCREMENT PRIMARY KEY,
    No_Clt     VARCHAR(50)  NOT NULL,
    Pno_Clt    VARCHAR(50)  NOT NULL,
    Age_Clt    INT          NOT NULL,
    Wi_Clt     VARCHAR(50)  NOT NULL,
    Tel_Clt    VARCHAR(10)  NOT NULL,
    Mail_Clt   VARCHAR(100) NOT NULL UNIQUE,
    Adr_Clt    TEXT         NOT NULL,
    Mot_Clt    VARCHAR(255) NOT NULL,
    Sexe_Clt   ENUM('Homme','Femme') NOT NULL
);

-- Table commandes
CREATE TABLE commande_produit (
    Id_cmd     INT AUTO_INCREMENT PRIMARY KEY,
    Id_client  INT          NOT NULL,
    Vendeur_prod VARCHAR(100) NOT NULL,
    Prix_prod  VARCHAR(50)  NOT NULL,
    Ref_prod   VARCHAR(20)  NOT NULL,
    Qant_prod  INT          NOT NULL,
    Date_cmd   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Id_client) REFERENCES client(Id_Clt)
);
```

---

## 🛠️ Technologies utilisées

| Technologie | Usage |
|-------------|-------|
| **PHP 8** | Backend, sessions, requêtes préparées |
| **MySQL** | Stockage des clients et commandes |
| **JavaScript ES6** | Panier, validation, AJAX (fetch) |
| **HTML5 / CSS3** | Structure et design (backdrop-filter, animations) |
| **localStorage** | Persistance du panier côté client |

---

## 🔒 Sécurité

- Mots de passe hachés avec `password_hash()` (bcrypt)
- Requêtes SQL préparées contre les injections
- Validation des données côté serveur sur chaque endpoint PHP
- Sessions régénérées après connexion (`session_regenerate_id`)
- Fichier `config.php` exclu du versionnement

---

## 👤 Auteur

**[Nehal Chalabia]**
Étudiant en informatique — Wilaya de Sétif, Algérie

📧 Email: chalabianehal36@gmail.com 

🌐 GitHub: https://github.com/chalabianehal36-lgtm

🚀 Project: https://github.com/chalabianehal36-lgtm/la-cristal-bijoux-kabyle

---

🌐 **Démo :** [bijouxkabyle.freedev.app](https://bijouxkabyle.freedev.app/)

---

*Projet réalisé dans le cadre d'un apprentissage personnel du développement web full-stack.*
