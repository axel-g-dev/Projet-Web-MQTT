Voici le protocole de validation strict pour votre documentation, adapté avec l'adresse IP 172.40.1.243.

# Protocole de Validation : Installation LAMP & Surveillance

**Cible :** VM Proxmox (Debian 12)
**Adresse IP :** 172.40.1.243
**Prérequis :** Accès SSH (root) sur la VM et navigateur web sur le poste client.

### 1\. Vérification de l'Installation Système

Objectif : Valider la présence et l'état des paquets installés (Étape 1 du guide).

  * **Contrôle des versions :** Exécuter les commandes suivantes pour confirmer l'installation.
    ```bash
    apache2 -v
    mariadb --version
    php -v
    ```
  * **État des services :** Vérifier que les services sont actifs (running).
    ```bash
    systemctl is-active apache2
    systemctl is-active mariadb
    ```
    *Résultat attendu :* La sortie doit afficher `active` pour les deux commandes.

### 2\. Vérification de la Base de Données

Objectif : Valider l'authentification et la configuration du plugin (Étape 2 du guide).

  * **Test de connexion CLI :** Tenter une connexion avec le mot de passe défini.
    ```bash
    mysql -u root -p
    # Mot de passe : ciel12000.
    ```
    *Résultat attendu :* Accès au prompt `MariaDB [(none)]>`.
  * **Vérification du mode d'authentification :** Dans le prompt SQL, exécuter :
    ```sql
    SELECT user, host, plugin FROM mysql.user WHERE user='root';
    ```
    *Résultat attendu :* La colonne `plugin` pour `root`@`localhost` doit indiquer `mysql_native_password`.

### 3\. Vérification de PhpMyAdmin

Objectif : Valider l'accès à l'outil d'administration (Étape 3 du guide).

  * **Accès HTTP :** Naviguer vers `http://172.40.1.243/phpmyadmin`
  * **Test de connexion :**
      * Utilisateur : `root`
      * Mot de passe : `ciel12000.`
      * *Résultat attendu :* Accès réussi à l'interface de gestion sans erreur.

### 4\. Vérification de la Configuration Web

Objectif : Valider le VirtualHost et les permissions (Étape 4 du guide).

  * **Vérification syntaxique Apache :**
    ```bash
    apache2ctl configtest
    ```
    *Résultat attendu :* La sortie doit afficher `Syntax OK`.
  * **Vérification des fichiers et permissions :**
    ```bash
    ls -l /var/www/surveillance/index.php
    ```
    *Résultat attendu :* Le fichier existe et le propriétaire est `www-data`.

### 5\. Vérification de l'Intégrité des Données

Objectif : Confirmer l'exécution du script SQL et la présence des données (Étape 5 du guide).

  * **Comptage des enregistrements :** Exécuter la commande suivante :
    ```bash
    mysql -u root -pciel12000. -D surveillanceEauCanal -e "SELECT COUNT(*) FROM \`1\`;"
    ```
    *Résultat attendu :* Le retour doit indiquer exactement `25`.

### 6\. Validation Fonctionnelle Finale

Objectif : Valider le rendu final de l'application (Étape 6 du guide).

  * **Accès Application :** Ouvrir `http://172.40.1.243/`
  * **Points de contrôle visuels :**
    1.  Le titre "Surveillance Eau Canal" est affiché.
    2.  Les cartes de statistiques (Hauteur Actuelle, Température, etc.) affichent des valeurs numériques et non des erreurs PHP.
    3.  Les 3 graphiques (Chart.js) sont visibles et tracés.
    4.  Le tableau "Historique des Mesures" en bas de page est rempli.
