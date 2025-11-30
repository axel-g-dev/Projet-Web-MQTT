## Bilan du Suivi de Projet : Déploiement d'une Solution Web et BDD

Ce document retrace les étapes de mise en place d'une infrastructure pour héberger une page web et une base de données de surveillance, incluant les problèmes rencontrés et les solutions implémentées.

### Session du 12/11/2025 (15h50 – 16h30)

#### Objectif Initial : Déploiement via Docker

  * **Création de la Machine Virtuelle (VM)** :
      * Mise en place d’une VM Debian destinée à héberger une page web et une base de données temporaire.
  * **Installation de Docker sur Debian (Terminée à 16h30)** :
      * Installation du paquet Docker.
      * **Validation de l’installation** :
          * Commande : `docker --version`
          * Résultat : `Docker version 29.0.0, build 3d4129b`
  * **Tâche en cours : Configuration Docker Compose** :
      * Début de la configuration du fichier `docker-compose.yml` pour intégrer les services `phpMyAdmin` et `index.php`.
  * **Blocage rencontré** :
      * Problème de type **"daemon problem"** rencontré. Hypothèse : erreur suite à la nouvelle mise à jour de Docker, à vérifier lors de la prochaine session.

-----

### Session du 19/11/2025 (14h00 – 17h00)

#### Changement d'Orientation : Abandon de Docker et Passage à LAMP (Installation Native)

  * **Période 14h00 – 15h30 : Gestion des Incidents et Changement de Stratégie**
      * **Recréation de VM :** Nécessité de créer une nouvelle VM, l'ancienne ayant été supprimée lors de la refonte du serveur `bts2`.
      * **Blocage persistant avec Docker :** Des erreurs critiques liées à la dernière mise à jour de Docker ont été rencontrées (sur des tests avec MySQL et MariaDB).
          * **Erreur précise :** `Error response from daemon: failed to create task for container: failed to create shim task: OCI runtime create failed: runc create failed: unable to start container process: error during container init: open sysctl net.ipv4.ip_unprivileged_port_start file: reopen fd 8: permission denied`.
      * **Décision :** Abandonner temporairement Docker et déployer la base de données (MariaDB) et le site sans conteneurisation via une installation LAMP.
  * **Période 15h30 – 17h00 : Installation de LAMP et Développement Web**
      * **Installation LAMP :** Installation de MariaDB et phpMyAdmin sur un Conteneur (CT) du réseau.
      * **Création de la page web :**
          * Problème initial : le fichier `index.php` ne fonctionnait pas correctement.
          * **Solution :** Utilisation et adaptation d'un code PHP simple pour établir la connexion.
      * **Code PHP validé :** Le script ci-dessous a été rendu fonctionnel, assurant la connexion à la base de données (`surveillanceEauCanal`) et l'affichage des données dans un tableau HTML.

<!-- end list -->

```php
<?php
$host = "127.0.0.1";       // IP de MariaDB
$dbname = "surveillanceEauCanal";
$username = "root";
$password = "ciel12000.";

try {
    // Connexion
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Connexion à MariaDB réussie !</h2>";

    // Récupération et affichage des données
    $stmt = $pdo->query("SELECT * FROM `1` ORDER BY date_heure DESC");

    echo "<table border='1' cellpadding='8' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>Hauteur (m)</th>
            <th>Température (°C)</th>
            <th>Date & Heure</th>
          </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['hauteurEau'] . "</td>";
        echo "<td>" . $row['temperatureEau'] . "</td>";
        echo "<td>" . $row['date_heure'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (PDOException $e) {
    echo "<b>ERREUR : </b>" . $e->getMessage();
}
?>
```

---

### Session du 26/11/2025 (14h00 – 17h00)

#### Intégration des graphiques et finalisation de la VM LAMP

* **Ajout de fonctionnalités dans `index.php`** :

  * Intégration de Chart.js pour afficher des graphiques basés sur les données (valeurs moyennes, évolution des mesures).
  * Création et test d’un tableau récapitulatif et d’un premier graphique utilisant des données factices.

* **Finalisation de l’environnement LAMP** :

  * Stabilisation de la VM (Apache, PHP, MariaDB).
  * Vérification du fonctionnement global du site.

* **Documentation** :

  * Rédaction de la documentation du projet et de la fiche de test associée.

* **Blocage restant** :

  * La base de données finale n’étant pas encore terminée (travail en attente de Louna), le site est temporairement connecté à une BDD factice.
  * Prochaine étape : intégrer la base définitive et adapter les requêtes SQL.

---

### Synthèse des Progrès

| Élément                     | État Actuel | Commentaires                                                            |
| --------------------------- | ----------- | ----------------------------------------------------------------------- |
| VM/CT Hôte                  | Finalisé    | VM Debian avec LAMP opérationnelle.                                     |
| Conteneurisation (Docker)   | Abandonné   | Problèmes de permissions persistants.                                   |
| Base de Données             | En cours    | MariaDB installée mais la BDD finale doit être intégrée.                |
| Serveur Web                 | Finalisé    | Apache configuré.                                                       |
| Affichage Web (Tableau PHP) | Finalisé    | Affichage des données fonctionnel.                                      |
| Affichage Web (Graphiques)  | En cours    | Chart.js intégré dans `index.php`, tests réalisés sur données factices. |
| Documentation & Tests       | Finalisé    | Documentation projet et fiche de test rédigées.                         |

---

