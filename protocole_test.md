C'est une documentation très propre et bien structurée \! Pour valider qu'elle fonctionne parfaitement "bout en bout" (de l'installation vierge jusqu'à l'affichage du graphique), voici un **Protocole de Recette (Test Plan)**.

Ce protocole est conçu pour être exécuté ligne par ligne sur un conteneur vierge afin de certifier ta documentation.

-----

# 📋 Protocole de Validation : Installation LAMP & Surveillance

**Objectif :** Valider la documentation d'installation v1.0.
**Prérequis :** Un conteneur Proxmox (Debian 12) fraîchement créé, accès SSH ou Console root.

### Phase 1 : Vérification de l'Installation (Correspond au point 1)

  * [ ] **Exécution des commandes :** Lancer le bloc d'installation `apt install ...`.
  * [ ] **Contrôle des versions :** Vérifier que les services sont bien là.
    ```bash
    apache2 -v      # Doit retourner la version d'Apache
    mariadb --version # Doit retourner la version de MariaDB
    php -v          # Doit retourner PHP 8.x
    ```
  * [ ] **État des services :** Vérifier qu'ils tournent.
    ```bash
    systemctl is-active apache2
    systemctl is-active mariadb
    ```
    *Résultat attendu :* Les deux doivent retourner `active`.

### Phase 2 : Vérification Base de Données (Correspond au point 2)

  * [ ] **Sécurisation :** Lancer `mysql_secure_installation`.
  * [ ] **Test de connexion Root :** Tenter de se connecter avec le nouveau mot de passe.
    ```bash
    mysql -u root -p
    # Entrer: ciel12000.
    ```
    *Résultat attendu :* Accès au prompt MariaDB `MariaDB [(none)]>`.
  * [ ] **Vérification du plugin d'auth (Critique) :** Une fois connecté en SQL, vérifier que la modif 2.2 a fonctionné.
    ```sql
    SELECT user, host, plugin FROM mysql.user WHERE user='root';
    ```
    *Résultat attendu :* La ligne root/localhost doit indiquer `mysql_native_password`.

### Phase 3 : Vérification PhpMyAdmin (Correspond au point 3)

  * [ ] **Accès HTTP :** Ouvrir un navigateur sur `http://<IP_CT>/phpmyadmin`.
    *Résultat attendu :* La page de login s'affiche.
  * [ ] **Authentification :** Se connecter avec `root` / `ciel12000.`.
    *Résultat attendu :* Accès au dashboard sans erreur rouge en bas de page.

### Phase 4 : Vérification Web & Code (Correspond au point 4)

  * [ ] **Fichiers présents :**
    ```bash
    ls -la /var/www/surveillance/
    ```
    *Résultat attendu :* `index.php` est présent et appartient à `www-data`.
  * [ ] **Syntaxe Apache :** Avant de redémarrer, tester la config.
    ```bash
    apache2ctl configtest
    ```
    *Résultat attendu :* `Syntax OK`.
  * [ ] **Redémarrage :** Le `systemctl restart apache2` ne doit renvoyer aucune erreur.

### Phase 5 : Vérification des Données (Correspond au point 5)

  * [ ] **Intégrité des données :** Vérifier que les données fictives sont bien insérées via le terminal.
    ```bash
    mysql -u root -pciel12000. -D surveillanceEauCanal -e "SELECT COUNT(*) FROM \`1\`;"
    ```
    *Résultat attendu :* Le retour doit être `25` (car ton script insère 25 lignes).
    *(Note : J'ai échappé le nom de la table `1` car c'est un nom spécial).*

### Phase 6 : Validation Fonctionnelle Finale (Correspond au point 6)

  * [ ] **Accès Application :** Ouvrir `http://<172.40.1.243>/`.
  * [ ] **Vérification PHP/PDO :**
      * Le titre "Surveillance Eau Canal" est visible.
      * Pas de message d'erreur "Erreur de connexion" ou "Aucune donnée".
  * [ ] **Vérification JavaScript (Chart.js) :**
      * Les 3 graphiques s'affichent (Lignes bleues et rouges).
      * Passer la souris sur un point affiche l'infobulle (Tooltip).
  * [ ] **Vérification Tableau :**
      * Le tableau en bas de page contient bien 25 lignes.
      * Les dates sont cohérentes (date du jour et heures précédentes).

