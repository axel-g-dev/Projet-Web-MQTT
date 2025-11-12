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
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: mydb
      MYSQL_USER: user
      MYSQL_PASSWORD: pass
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
      PMA_USER: user
      PMA_PASSWORD: pass
    ports:
      - "8081:80"
    depends_on:
      - db

volumes:
  mysql_data:
```

-----

### IV. Prochaine Étape

La structure est définie pour trois conteneurs (MySQL, PHP/Apache, phpMyAdmin).

Voulez-vous la commande pour démarrer les services définis dans le fichier `docker-compose.yml` ?
