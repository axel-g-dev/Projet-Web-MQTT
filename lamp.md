## 💻 Documentation Technique : Installation LAMP (MariaDB) sur Proxmox CT (Sans Docker)

Ce guide détaille l'installation d'un environnement **L**inux, **A**pache, **M**ariaDB, **P**HP (LAMP) dans un conteneur (CT) Proxmox basé sur Debian 12, et le déploiement de votre application de surveillance.

### 1\. Préparation du Système et Installation des Composants LAMP 🛠️

Connectez-vous à votre conteneur Proxmox et installez les paquets de base.

```bash
# 1. Mise à jour du système
apt update && apt upgrade -y

# 2. Installation des composants LAMP
# - apache2: Serveur Web
# - mariadb-server: Base de données (remplace MySQL)
# - php: Langage de script
# - libapache2-mod-php: Module PHP pour Apache
# - php-mysql: Extension de connexion DB (PDO/mysqli, compatible MariaDB)
# - php-cli: Outil ligne de commande PHP
apt install sudo nano wget curl apache2 mariadb-server php libapache2-mod-php php-mysql php-cli -y
```

-----

### 2\. Configuration et Sécurisation de MariaDB 🔐

Nous allons sécuriser l'installation de MariaDB et préparer l'accès.

#### 2.1. Sécurisation Initiale

Exécutez le script de sécurisation.

```bash
mysql_secure_installation
```

> **Actions recommandées lors de la sécurisation :**
>
> 1.  **Définissez le mot de passe ROOT** (Utilisez **`ciel12000`** pour la cohérence avec votre `docker-compose.yml` initial).
> 2.  Répondez par `y` aux questions pour supprimer les utilisateurs anonymes, interdire la connexion root à distance (par défaut, il n'écoute que sur `localhost`), supprimer la base de données de test et recharger les tables de privilèges.

#### 2.2. Modification de l'Authentification (facultatif mais utile)

Par défaut, Debian utilise un plugin d'authentification Unix Socket qui empêche la connexion en tant que `root` avec un mot de passe classique. Pour permettre à l'application et à PhpMyAdmin de se connecter en utilisant le mot de passe **`ciel12000`**, assurez-vous que l'utilisateur `root` utilise l'authentification standard par mot de passe.

Connectez-vous à MariaDB :

```bash
mysql -u root -p
# Entrez ciel12000
```

Exécutez ces commandes pour forcer `root` à utiliser l'authentification par mot de passe pour les connexions TCP/IP (y compris `localhost`) :

```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'ciel12000';
FLUSH PRIVILEGES;
exit
```

-----

### 3\. Installation et Accès à PhpMyAdmin (Facultatif) 🖥️

Pour remplacer le service `phpmyadmin` de votre ancienne configuration Docker, installez-le directement sur le CT.

```bash
# Installation du paquet phpmyadmin
apt install phpmyadmin -y
```

Lors de l'installation, vous serez invité à faire des choix :

1.  **Serveur web à reconfigurer :** Sélectionnez **`apache2`** (Appuyez sur **`Espace`** pour sélectionner, puis **`Entrée`**).
2.  **Configurer la base de données pour phpmyadmin avec dbconfig-common :** Répondez **`Yes`**.
3.  **Mot de passe de l'administrateur de la base de données (MariaDB root) :** Entrez **`ciel12000`**.
4.  **Mot de passe d'application de phpmyadmin :** Laissez vide pour qu'il soit généré ou définissez-en un.

PhpMyAdmin est maintenant accessible sur : `http://ip_vm/phpmyadmin`

> **Connexion PhpMyAdmin :**
>
>   * **Utilisateur :** `root`
>   * **Mot de passe :** `ciel12000`

-----

### 4\. Configuration d'Apache et Déploiement du Code 🌐

Nous allons configurer le Virtual Host pour servir votre application et y placer le code corrigé.

#### 4.1. Création du Dossier Web

Nous utiliserons `/var/www/surveillance` pour séparer votre application des autres fichiers (comme PhpMyAdmin).

```bash
mkdir -p /var/www/surveillance
cd /var/www/surveillance
```

#### 4.2. Configuration du Virtual Host (`/etc/apache2/sites-available/surveillance.conf`)

Créez le fichier de configuration pour votre application :

```bash
nano /etc/apache2/sites-available/surveillance.conf
```

Collez-y le contenu suivant :

```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/surveillance
    ServerName surveillance.local
    
    <Directory /var/www/surveillance>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/surveillance_error.log
    CustomLog ${APACHE_LOG_DIR}/surveillance_access.log combined
</VirtualHost>
```

#### 4.3. Activation du Site et Redémarrage d'Apache

Activez la nouvelle configuration et désactivez celle par défaut (pour que votre site soit le seul à répondre sur le port 80).

```bash
# Activation du nouveau site
a2ensite surveillance.conf

# Désactivation du site par défaut (si vous n'en avez pas besoin)
a2dissite 000-default.conf

# Activation du module rewrite (nécessaire pour la bonne pratique)
a2enmod rewrite

# Redémarrage d'Apache
systemctl restart apache2
```

#### 4.4. Création du Fichier `index.php` (Corrigé)

Créez le fichier `index.php` dans le nouveau répertoire (`/var/www/surveillance`).

```bash
nano /var/www/surveillance/index.php
```

Collez le code complet, en vous assurant que l'hôte de connexion est bien **`localhost`** :

```php
// ... (début du code HTML/CSS)

        <?php
        $host = 'localhost'; // CORRIGÉ : L'hôte est local pour l'installation sur CT
        $dbname = 'surveillanceEauCanal';
        $username = 'root';
        $password = 'ciel12000'; // Mot de passe du root MariaDB

        try {
// ... (le reste du code PHP/HTML/JS est inchangé)
```

#### 4.5. Attribution des Permissions

Appliquez les bonnes permissions au répertoire web.

```bash
chown -R www-data:www-data /var/www/surveillance
chmod -R 755 /var/www/surveillance
```

-----

### 5\. Initialisation de la Base de Données (SQL) 💾

Pour remplir la base de données avec les données de démonstration, connectez-vous à la console MariaDB une dernière fois (ou utilisez PhpMyAdmin à l'étape suivante).

```bash
mysql -u root -p
# Entrez ciel12000
```

Collez et exécutez le script SQL original :

```sql
-- --------------------------------------------------------
-- 1. CRÉATION DE LA BASE
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `surveillanceEauCanal`;
USE `surveillanceEauCanal`;

-- --------------------------------------------------------
-- 2. CRÉATION DE LA TABLE "1"
-- --------------------------------------------------------
DROP TABLE IF EXISTS `1`; 
CREATE TABLE `1` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `hauteurEau` DECIMAL(5,2) NOT NULL,
    `temperatureEau` DECIMAL(5,2) NOT NULL,
    `date_heure` DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- 3. INSERTION DE 25 LIGNES DE DONNÉES
-- --------------------------------------------------------
INSERT INTO `1` (`hauteurEau`, `temperatureEau`, `date_heure`) VALUES
(1.50, 14.2, NOW() - INTERVAL 24 HOUR),
(1.52, 14.3, NOW() - INTERVAL 23 HOUR),
(1.48, 14.1, NOW() - INTERVAL 22 HOUR),
(1.55, 14.5, NOW() - INTERVAL 21 HOUR),
(1.60, 14.8, NOW() - INTERVAL 20 HOUR),
(1.58, 14.7, NOW() - INTERVAL 19 HOUR),
(1.45, 14.0, NOW() - INTERVAL 18 HOUR),
(1.42, 13.9, NOW() - INTERVAL 17 HOUR),
(1.35, 13.8, NOW() - INTERVAL 16 HOUR),
(1.30, 13.5, NOW() - INTERVAL 15 HOUR),
(1.25, 13.2, NOW() - INTERVAL 14 HOUR),
(1.28, 13.4, NOW() - INTERVAL 13 HOUR),
(1.32, 13.6, NOW() - INTERVAL 12 HOUR),
(1.40, 14.1, NOW() - INTERVAL 11 HOUR),
(1.45, 14.3, NOW() - INTERVAL 10 HOUR),
(1.50, 14.5, NOW() - INTERVAL 9 HOUR),
(1.53, 14.6, NOW() - INTERVAL 8 HOUR),
(1.55, 14.9, NOW() - INTERVAL 7 HOUR),
(1.62, 15.1, NOW() - INTERVAL 6 HOUR),
(1.65, 15.3, NOW() - INTERVAL 5 HOUR),
(1.60, 15.0, NOW() - INTERVAL 4 HOUR),
(1.58, 14.8, NOW() - INTERVAL 3 HOUR),
(1.55, 14.7, NOW() - INTERVAL 2 HOUR),
(1.52, 14.5, NOW() - INTERVAL 1 HOUR),
(1.50, 14.4, NOW());
```

Quittez la console MariaDB :

```sql
exit
```

-----

### 6\. Accès aux Interfaces ✔️

| Service | Accès (Remplacez `ip_vm` par l'IP de votre conteneur) | Détails de Connexion |
| :--- | :--- | :--- |
| **Application Web** | `http://ip_vm/` | Lecture des données de la DB. |
| **PhpMyAdmin** | `http://ip_vm/phpmyadmin` | **User:** `root` / **Pass:** `ciel12000` |


