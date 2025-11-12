## 📄 Documentation de Configuration et d'Installation

### I. Préparation du Conteneur Proxmox

Exécutez ces commandes après la création du Conteneur (CT) :

```bash
apt-get install upgrade
```

```bash
apt-get install sudo
```

-----

### II. Installation de Docker

Procédure pour installer Docker Engine et les outils associés :

1.  **Ajout du Dépôt Officiel**

<!-- end list -->

```bash
# Add Docker's official GPG key:
sudo apt update
sudo apt install ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
sudo tee /etc/apt/sources.list.d/docker.sources <<EOF
Types: deb
URIs: https://download.docker.com/linux/debian
Suites: $(. /etc/os-release && echo "$VERSION_CODENAME")
Components: stable
Signed-By: /etc/apt/keyrings/docker.asc
EOF

sudo apt update
```

2.  **Installation des Paquets**

<!-- end list -->

```bash
sudo apt install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

3.  **Vérification de l'Installation**

<!-- end list -->

```bash
sudo docker --version
```

-----

### III. Configuration Docker Compose

Créez le fichier de configuration `docker-compose.yml` :

```bash
sudo nano docker-compose.yml
```

Copiez et collez le contenu suivant :

```yaml
services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ciel12000
    volumes:
      - mysql_data:/var/lib/mysql

  php:
    image: php:8.2-apache
    volumes:
      - ./:/var/www/html
    ports:
      - "8080:80"

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: ciel12000
    ports:
      - "8081:80"
    depends_on:
      - db

volumes:
  mysql_data:

```

-----

ensuite 
'''docker compose up -d'''

Puis allez sur votre navigateur 

http://ip_vm:8081

Dans phpmyadmin entrez :
'''
-- --------------------------------------------------------
-- 1. CRÉATION DE LA BASE
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `surveillanceEauCanal`;
USE `surveillanceEauCanal`;

-- --------------------------------------------------------
-- 2. CRÉATION DE LA TABLE "1"
-- --------------------------------------------------------
DROP TABLE IF EXISTS `1`; -- On la supprime si elle existe pour repartir à neuf
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
'''
Pour creer la bdd et la remplir de valeurs 

