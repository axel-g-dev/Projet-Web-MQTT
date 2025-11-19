<?php
/*************************************************
 * CONFIGURATION BASE DE DONNÉES
 *************************************************/
$host = '127.0.0.1';
$dbname = 'surveillanceEauCanal';
$username = 'root';
$password = 'ciel12000.'; // 🔒 Idéalement à exporter hors du fichier !

/*************************************************
 * CONNEXION PDO
 *************************************************/
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $stmt = $pdo->query("SELECT * FROM `1` ORDER BY date_heure DESC");
    $data = $stmt->fetchAll();

} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surveillance Eau Canal</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- 🔥 Feuille de style optimisée -->
    <style>
        <?php /* Ton CSS original conservé tel quel, simplement nettoyé */ ?>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,sans-serif;background:linear-gradient(to bottom,#fff,#f8f9fa);color:#1d1d1f;line-height:1.6;min-height:100vh}
        .container{max-width:1400px;margin:auto;padding:60px 30px}
        .header{text-align:center;margin-bottom:70px;animation:fadeInDown .8s ease}
        @keyframes fadeInDown{0%{opacity:0;transform:translateY(-20px)}100%{opacity:1;transform:translateY(0)}}
        @keyframes fadeInUp{0%{opacity:0;transform:translateY(20px)}100%{opacity:1;transform:translateY(0)}}
        h1{font-size:3.5em;font-weight:700;margin-bottom:15px;background:linear-gradient(135deg,#1d1d1f,#4a5568);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .header p{font-size:1.3em;color:#6b7280}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;margin-bottom:60px;animation:fadeInUp .8s ease .2s both}
        .stat-card{background:linear-gradient(135deg,#fff,#fafbfc);padding:35px;border-radius:24px;box-shadow:0 4px 24px rgba(0,0,0,.06);transition:.4s;border:1px solid rgba(0,0,0,.04);position:relative;overflow:hidden}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#007AFF,#00C7BE);opacity:0;transition:.4s}
        .stat-card:hover{box-shadow:0 12px 40px rgba(0,0,0,.1);transform:translateY(-8px);border-color:rgba(0,122,255,.1)}
        .stat-card:hover::before{opacity:1}
        .stat-card h3{font-size:.8em;color:#9ca3af;font-weight:600;text-transform:uppercase;margin-bottom:16px}
        .stat-value{font-size:3.2em;font-weight:700}
        .stat-label{color:#9ca3af;font-size:.95em}
        .chart-section,.table-section{background:linear-gradient(135deg,#fff,#fafbfc);padding:45px;border-radius:24px;box-shadow:0 4px 24px rgba(0,0,0,.06);margin-bottom:30px;border:1px solid rgba(0,0,0,.04);animation:fadeInUp .8s ease .4s both}
        .chart-section h2,.table-section h2{font-size:1.6em;font-weight:700;margin-bottom:35px}
        .charts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(500px,1fr));gap:30px;margin-bottom:30px}
        .table-wrapper{overflow-x:auto;border-radius:16px;border:1px solid #e5e7eb}
        table{width:100%;border-collapse:collapse}
        th{background:linear-gradient(135deg,#f9fafb,#f3f4f6);color:#374151;padding:18px 20px;text-align:left;font-size:.85em;border-bottom:2px solid #e5e7eb}
        td{padding:18px 20px;border-bottom:1px solid #f3f4f6;background:white}
        tr:hover td{background:#f9fafb}
        .loading{text-align:center;padding:100px 20px;color:#9ca3af;font-size:1.3em}
        .error{background:#fee2e2;color:#991b1b;padding:20px;border-radius:12px;margin:20px 0;border:1px solid #fecaca}
        @media(max-width:768px){h1{font-size:2.2em}.charts-grid{grid-template-columns:1fr}.chart-section,.table-section{padding:30px 20px}}
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>Surveillance Eau Canal</h1>
        <p>Monitoring en temps réel des paramètres hydrauliques</p>
    </div>

<?php if (!empty($errorMsg)): ?>
    <div class="error"><strong>Erreur :</strong> <?= htmlspecialchars($errorMsg) ?></div>

<?php elseif (empty($data)): ?>
    <div class="loading">Aucune donnée disponible.</div>

<?php else: ?>

<?php
    /*************************************************
     * TRAITEMENT DES DONNÉES
     *************************************************/
    $hauteurs = array_column($data, 'hauteurEau');
    $temperatures = array_column($data, 'temperatureEau');
    $last = $data[0];

    function avg($arr) { return round(array_sum($arr) / count($arr), 2); }

    $hauteurMoy = avg($hauteurs);
    $tempMoy    = avg($temperatures);
?>
    <!-- 🔥 Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Hauteur Actuelle</h3>
            <div class="stat-value"><?= $last['hauteurEau'] ?> m</div>
            <div class="stat-label">Moyenne : <?= $hauteurMoy ?> m</div>
        </div>

        <div class="stat-card">
            <h3>Température Actuelle</h3>
            <div class="stat-value"><?= $last['temperatureEau'] ?> °C</div>
            <div class="stat-label">Moyenne : <?= $tempMoy ?> °C</div>
        </div>

        <div class="stat-card">
            <h3>Variation Hauteur</h3>
            <div class="stat-value"><?= max($hauteurs) - min($hauteurs) ?> m</div>
            <div class="stat-label">Min : <?= min($hauteurs) ?> — Max : <?= max($hauteurs) ?></div>
        </div>

        <div class="stat-card">
            <h3>Total Mesures</h3>
            <div class="stat-value"><?= count($data) ?></div>
            <div class="stat-label">Dernière : <?= date('H:i', strtotime($last['date_heure'])) ?></div>
        </div>
    </div>

    <!-- 🔥 Graphiques -->
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

    <!-- 🔥 Table historique -->
    <div class="table-section">
        <h2>Historique</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Date & Heure</th>
                    <th>Hauteur (m)</th>
                    <th>Température (°C)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($row['date_heure'])) ?></td>
                        <td><?= htmlspecialchars($row['hauteurEau']) ?></td>
                        <td><?= htmlspecialchars($row['temperatureEau']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<script>
const data = <?= json_encode(array_reverse($data)) ?>;

const labels = data.map(item =>
    new Date(item.date_heure).toLocaleString('fr-FR', {
        day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'
    })
);

const hauteurs = data.map(i => parseFloat(i.hauteurEau));
const temperatures = data.map(i => parseFloat(i.temperatureEau));

const chartBase = {
    responsive:true,
    plugins:{
        legend:{display:false},
        tooltip:{backgroundColor:'rgba(0,0,0,.8)',padding:12}
    }
};

new Chart(hauteurChart,{
    type:'line',
    data:{labels,datasets:[{data:hauteurs,borderColor:'#007AFF',fill:true}]},
    options:chartBase
});

new Chart(temperatureChart,{
    type:'line',
    data:{labels,datasets:[{data:temperatures,borderColor:'#FF3B30',fill:true}]},
    options:chartBase
});

new Chart(combinedChart,{
    type:'line',
    data:{
        labels,
        datasets:[
            {label:'Hauteur (m)',data:hauteurs,borderColor:'#007AFF',yAxisID:'y'},
            {label:'Température (°C)',data:temperatures,borderColor:'#FF3B30',yAxisID:'y1'}
        ]
    },
    options:{
        responsive:true,
        interaction:{mode:'index',intersect:false},
        plugins:{legend:{display:true}},
        scales:{y:{type:'linear'},y1:{type:'linear',position:'right'}}
    }
});
</script>

<?php endif; ?>
</div>
</body>
</html>
