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

Création d'une nouvelle VM, suite au problème de la màj de docker. 
Création de la vm, puisque supprimée lors de la refonte du serveur bts2
