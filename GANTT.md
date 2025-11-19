## Suivi des tâches – 12/11/2025

### 15h50 – 16h30

**Création de la machine virtuelle**

* Mise en place d’une VM destinée à héberger une page web et une base de données temporaire.

**Installation de Docker sur Debian**

* Installation du paquet Docker.
* Test de validation de l’installation :

Commande exécutée :

```
docker --version
```

Résultat obtenu :

```
root@Serveur-Web-MQTT-AG:~# docker --version
Docker version 29.0.0, build 3d4129b
```

La tâche est finalisée à 16h30.

---

### 16h35 – (en cours)

**Création du fichier docker-compose.yml**

* Début de la configuration du fichier.
* Intégration des services prévus :

  * phpMyAdmin
  * index.php

 Problème que j'ai rencontré : "deamon problem", cela semble être une erreur suite à la nouvelle mise à jour de docker, je devrais le vérifier lors de la prochaine séance. 


---
## Suivi des tâches – 19/11/2025

De 14H00 à 15H30 : 
Création d'une nouvelle VM, suite au problème de la màj de docker. 
Création de la vm, puisque supprimée lors de la refonte du serveur bts2

J'ai rencontré des erreurs suite à la dernière màj de docker : 
 - j'ai essayé de faire une vm avec mysql
 - une autre avec mariadb
 Cette erreur précisémment : "Error response from daemon: failed to create task for container: failed to create shim task: OCI runtime create failed: runc create failed: unable to start container process: error during container init: open sysctl net.ipv4.ip_unprivileged_port_start file: reopen fd 8: permission denied"

Ces VMs m'ont fait perdre du temps.

Suite à cela, j'ai voulu déployer une bdd mariadb et un site sans passer par docker.

J'ai réalisé une installation via lamp.


