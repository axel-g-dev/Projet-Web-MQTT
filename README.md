# Documentation technique : Installation et Configuration d'une VM 

## 1\. Préparation du système (Proxmox CT)

Connectez-vous au conteneur et mettez à jour le système.

```bash
apt-get update && apt-get upgrade -y
apt-get install sudo -y
```

## 2\. Installation de Docker

Installez les dépendances et ajoutez le dépôt officiel Docker.
**Ajout du Dépôt Officiel**

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

**Installation des paquets Docker :**

```bash
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y
```

**Vérification :**

```bash
docker --version
```

**Création du dossier :**
```bash
mkdir docker-web
```
puis entrez dans ce dossier, 

```bash
cd docker-web
```

## 3\. Configuration des services

Créez le fichier `docker-compose.yml`.

```bash
nano docker-compose.yml
```

Collez le contenu suivant :

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

Créez ensuite le fichier `Dockerfile`.

```bash
nano Dockerfile
```

Collez le contenu suivant :

```dockerfile
FROM php:8.2-apache

# Installation des extensions MySQL/PDO
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activation du module Apache rewrite
RUN a2enmod rewrite

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html
```

## 4\. Application Web

Créez le fichier principal de l'application.

```bash
nano index.php
```

Collez le code source :

```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveillance Eau Canal</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
            color: #1d1d1f;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 70px;
            animation: fadeInDown 0.8s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header h1 {
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #1d1d1f 0%, #4a5568 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            font-size: 1.3em;
            color: #6b7280;
            font-weight: 400;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 60px;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #007AFF 0%, #00C7BE 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .stat-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            transform: translateY(-8px);
            border-color: rgba(0, 122, 255, 0.1);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card h3 {
            font-size: 0.8em;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 16px;
        }

        .stat-value {
            font-size: 3.2em;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .stat-label {
            color: #9ca3af;
            font-size: 0.95em;
            font-weight: 500;
        }

        .chart-section {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 45px;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .chart-section:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        .chart-section h2 {
            font-size: 1.6em;
            font-weight: 700;
            margin-bottom: 35px;
            color: #1d1d1f;
            letter-spacing: -0.01em;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .table-section {
            background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
            padding: 45px;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04);
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        .table-section h2 {
            font-size: 1.6em;
            font-weight: 700;
            margin-bottom: 35px;
            color: #1d1d1f;
            letter-spacing: -0.01em;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            color: #374151;
            padding: 18px 20px;
            text-align: left;
            font-weight: 700;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #f3f4f6;
            color: #1d1d1f;
            font-size: 0.95em;
            font-weight: 500;
            background: white;
        }

        tr:hover td {
            background: #f9fafb;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .loading {
            text-align: center;
            padding: 100px 20px;
            color: #9ca3af;
            font-size: 1.3em;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.2em;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-section, .table-section {
                padding: 30px 20px;
            }

            .stat-card {
                padding: 25px;
            }
        }

        /* Scrollbar personnalisée */
        .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 10px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Surveillance Eau Canal</h1>
            <p>Monitoring en temps réel des paramètres hydrauliques</p>
        </div>

        <?php
        $host = 'db';
        $dbname = 'surveillanceEauCanal';
        $username = 'root';
        $password = 'ciel12000';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT * FROM `1` ORDER BY date_heure DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $hauteurs = array_column($data, 'hauteurEau');
            $temperatures = array_column($data, 'temperatureEau');

            $hauteurMoy = round(array_sum($hauteurs) / count($hauteurs), 2);
            $hauteurMax = max($hauteurs);
            $hauteurMin = min($hauteurs);

            $tempMoy = round(array_sum($temperatures) / count($temperatures), 2);
            $tempMax = max($temperatures);
            $tempMin = min($temperatures);

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
            <div class="chart-section">
                <h2>Hauteur d'Eau</h2>
                <canvas id="hauteurChart"></canvas>
            </div>

            <div class="chart-section">
                <h2>Température</h2>
                <canvas id="temperatureChart"></canvas>
            </div>
        </div>

        <div class="chart-section">
            <h2>Vue d'Ensemble</h2>
            <canvas id="combinedChart"></canvas>
        </div>

        <div class="table-section">
            <h2>Historique des Mesures</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date & Heure</th>
                            <th>Hauteur d'Eau (m)</th>
                            <th>Température (°C)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($row['date_heure'])); ?></td>
                            <td><?php echo $row['hauteurEau']; ?> m</td>
                            <td><?php echo $row['temperatureEau']; ?> °C</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
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

            const chartConfig = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                }
            };

            new Chart(document.getElementById('hauteurChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: hauteurs,
                        borderColor: '#007AFF',
                        backgroundColor: 'rgba(0, 122, 255, 0.08)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#007AFF',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { color: '#6b7280', font: { size: 12, weight: '500' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6b7280', font: { size: 11, weight: '500' } }
                        }
                    }
                }
            });

            new Chart(document.getElementById('temperatureChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: temperatures,
                        borderColor: '#FF3B30',
                        backgroundColor: 'rgba(255, 59, 48, 0.08)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#FF3B30',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { color: '#6b7280', font: { size: 12, weight: '500' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6b7280', font: { size: 11, weight: '500' } }
                        }
                    }
                }
            });

            new Chart(document.getElementById('combinedChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hauteur (m)',
                            data: hauteurs,
                            borderColor: '#007AFF',
                            backgroundColor: 'rgba(0, 122, 255, 0.05)',
                            yAxisID: 'y',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#007AFF',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3
                        },
                        {
                            label: 'Température (°C)',
                            data: temperatures,
                            borderColor: '#FF3B30',
                            backgroundColor: 'rgba(255, 59, 48, 0.05)',
                            yAxisID: 'y1',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#FF3B30',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3
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
                            labels: { 
                                color: '#1d1d1f', 
                                font: { size: 13, weight: '600' },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { color: '#6b7280', font: { size: 12, weight: '500' } }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { color: '#6b7280', font: { size: 12, weight: '500' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6b7280', font: { size: 11, weight: '500' } }
                        }
                    }
                }
            });
        </script>

        <?php
        } catch (PDOException $e) {
            echo '<div class="loading">Erreur de connexion à la base de données</div>';
        }
        ?>
    </div>
</body>
</html>
```

## 5\. Lancement


```bash
docker compose build
docker compose up -d  
```

## 6\. Initialisation de la Base de Données

1.  Ouvrez votre navigateur à l'adresse : `http://ip_vm:8081`
2.  Identifiants : **root** / **ciel12000**
3.  Allez dans l'onglet **SQL** :

<!-- end list -->

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

L'application est maintenant accessible sur `http://ip_vm:8080`.
