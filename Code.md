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
      MYSQL_DATABASE: surveillanceEauCanal
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"

  php:
    build: .  
    volumes:
      - ./:/var/www/html
    ports:
      - "8080:80"
    depends_on:
      - db

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
puis créez un dockerfile :

'''nano Dockerfile'''

collez : 

'''FROM php:8.2-apache

# Installation des extensions MySQL/PDO
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activation du module Apache rewrite (bonus)
RUN a2enmod rewrite

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html
'''

-----

ensuite 

'''docker-compose build'''

# Relancez
'''docker-compose up -d'''

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
'''


Puis 

'''nano index.php'''

'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveillance Eau Canal - Dashboard Pro</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #888;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #999;
            font-size: 0.9em;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .chart-container h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        tr:hover {
            background: #f8f9ff;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .status-normal {
            background: #d4edda;
            color: #155724;
        }

        .status-warning {
            background: #fff3cd;
            color: #856404;
        }

        .status-alert {
            background: #f8d7da;
            color: #721c24;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: white;
            font-size: 1.5em;
        }

        .icon {
            width: 50px;
            height: 50px;
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Surveillance Eau Canal
            </h1>
            <p>Monitoring en temps réel des paramètres hydrauliques</p>
        </div>

        <?php
        // Configuration de la connexion à la base de données
        $host = 'db';
        $dbname = 'surveillanceEauCanal';
        $username = 'root';
        $password = 'ciel12000';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Récupération des données
            $stmt = $pdo->query("SELECT * FROM `1` ORDER BY date_heure DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcul des statistiques
            $hauteurs = array_column($data, 'hauteurEau');
            $temperatures = array_column($data, 'temperatureEau');

            $hauteurMoy = round(array_sum($hauteurs) / count($hauteurs), 2);
            $hauteurMax = max($hauteurs);
            $hauteurMin = min($hauteurs);

            $tempMoy = round(array_sum($temperatures) / count($temperatures), 2);
            $tempMax = max($temperatures);
            $tempMin = min($temperatures);

            // Dernière mesure
            $derniereMesure = $data[0];
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Hauteur Actuelle</h3>
                <div class="stat-value"><?php echo $derniereMesure['hauteurEau']; ?> m</div>
                <div class="stat-label">Moyenne: <?php echo $hauteurMoy; ?> m</div>
            </div>

            <div class="stat-card">
                <h3>Température Actuelle</h3>
                <div class="stat-value"><?php echo $derniereMesure['temperatureEau']; ?> °C</div>
                <div class="stat-label">Moyenne: <?php echo $tempMoy; ?> °C</div>
            </div>

            <div class="stat-card">
                <h3>Variation Hauteur</h3>
                <div class="stat-value"><?php echo round($hauteurMax - $hauteurMin, 2); ?> m</div>
                <div class="stat-label">Min: <?php echo $hauteurMin; ?> / Max: <?php echo $hauteurMax; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Mesures</h3>
                <div class="stat-value"><?php echo count($data); ?></div>
                <div class="stat-label">Dernière: <?php echo date('H:i', strtotime($derniereMesure['date_heure'])); ?></div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h2>📊 Évolution de la Hauteur d'Eau</h2>
                <canvas id="hauteurChart"></canvas>
            </div>

            <div class="chart-container">
                <h2>🌡️ Évolution de la Température</h2>
                <canvas id="temperatureChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <h2>📈 Vue d'Ensemble - Hauteur & Température</h2>
            <canvas id="combinedChart"></canvas>
        </div>

        <div class="table-container">
            <h2 style="margin-bottom: 20px; color: #333;">📋 Historique des Mesures</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date & Heure</th>
                        <th>Hauteur d'Eau (m)</th>
                        <th>Température (°C)</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($row['date_heure'])); ?></td>
                        <td><?php echo $row['hauteurEau']; ?> m</td>
                        <td><?php echo $row['temperatureEau']; ?> °C</td>
                        <td>
                            <?php
                            $hauteur = $row['hauteurEau'];
                            if ($hauteur < 1.30 || $hauteur > 1.65) {
                                echo '<span class="status-badge status-alert">⚠️ Alerte</span>';
                            } elseif ($hauteur < 1.40 || $hauteur > 1.60) {
                                echo '<span class="status-badge status-warning">⚡ Attention</span>';
                            } else {
                                echo '<span class="status-badge status-normal">✓ Normal</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
            // Préparation des données pour les graphiques
            const data = <?php echo json_encode(array_reverse($data)); ?>;
            const labels = data.map(item => {
                const date = new Date(item.date_heure);
                return date.toLocaleString('fr-FR', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            });
            const hauteurs = data.map(item => parseFloat(item.hauteurEau));
            const temperatures = data.map(item => parseFloat(item.temperatureEau));

            // Configuration commune des graphiques
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            };

            // Graphique Hauteur d'Eau
            new Chart(document.getElementById('hauteurChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Hauteur d\'Eau (m)',
                        data: hauteurs,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Hauteur (m)'
                            }
                        }
                    }
                }
            });

            // Graphique Température
            new Chart(document.getElementById('temperatureChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Température (°C)',
                        data: temperatures,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Température (°C)'
                            }
                        }
                    }
                }
            });

            // Graphique Combiné
            new Chart(document.getElementById('combinedChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hauteur d\'Eau (m)',
                            data: hauteurs,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            yAxisID: 'y',
                            tension: 0.4
                        },
                        {
                            label: 'Température (°C)',
                            data: temperatures,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            yAxisID: 'y1',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Hauteur (m)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Température (°C)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });
        </script>

        <?php
        } catch (PDOException $e) {
            echo '<div class="loading">❌ Erreur de connexion: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>
</html>
'''
