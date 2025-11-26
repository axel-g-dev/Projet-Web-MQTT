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

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid #fecaca;
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
        $host = '127.0.0.1';
        $dbname = 'surveillanceEauCanal';
        $username = 'root';
        $password = 'ciel12000.';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->query("SELECT * FROM `1` ORDER BY date_heure DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($data) > 0) {
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
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($row['date_heure'])); ?></td>
                            <td><?php echo htmlspecialchars($row['hauteurEau']); ?> m</td>
                            <td><?php echo htmlspecialchars($row['temperatureEau']); ?> °C</td>
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
            } else {
                echo '<div class="loading">Aucune donnée disponible dans la base de données</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="error"><strong>Erreur de connexion :</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>
</html>
