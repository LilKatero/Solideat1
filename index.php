<?php
require __DIR__ . "/config/db.php";
require __DIR__ . "/includes/geocode.php";

/* =========================
   AJOUT RESTAURANT
========================= */
if (isset($_POST['add_restaurant'])) {

    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['description']);

    if ($name && $address) {

        // Géocodage
        $coords = geocode($pdo, $address);

        // fallback si API KO
        if (!$coords) {
            $coords = [45.1885, 5.7245];
        }

        $stmt = $pdo->prepare("
            INSERT INTO restaurants
            (name, address, lat, lng, phone, description, validated)
            VALUES (?, ?, ?, ?, ?, ?, 0)
        ");

        $stmt->execute([
            $name,
            $address,
            $coords[0],
            $coords[1],
            $phone,
            $description
        ]);

        $success = "Restaurant envoyé en attente de validation";
    } else {
        $error = "Nom et adresse obligatoires";
    }
}

/* =========================
   RESTAURANTS VALIDÉS (CARTE)
========================= */
$restaurants_validated = $pdo->query("
    SELECT * FROM restaurants WHERE validated = 1
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   RESTAURANTS EN ATTENTE
========================= */
$restaurants_pending = $pdo->query("
    SELECT * FROM restaurants WHERE validated = 0
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Solideat - Restaurants solidaires</title>

<link rel="stylesheet" href="assets/style.css">

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>
<a href="admin.php" style="position:fixed;top:10px;right:10px;background:#2c3e50;color:white;padding:8px 12px;border-radius:5px;text-decoration:none;">
🧑‍💼 Admin
</a>
<h1>🍽️ Restaurants solidaires Grenoble</h1>

<?php if (isset($success)) echo "<p style='color:green;text-align:center;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>

<div class="container">

<!-- =========================
     FORMULAIRE + LISTES
========================= -->
<div class="form">

    <h3>➕ Ajouter un restaurant</h3>

    <form method="POST">
        <input name="name" placeholder="Nom du restaurant" required>
        <input name="address" placeholder="Adresse complète" required>
        <input name="phone" placeholder="Téléphone">
        <textarea name="description" placeholder="Description"></textarea>

        <button type="submit" name="add_restaurant">
            Envoyer
        </button>
    </form>

    <hr>

    <!-- EN ATTENTE -->
    <h3>🟡 En attente de validation</h3>

    <?php foreach ($restaurants_pending as $r): ?>
        <div class="card">
            <b><?= htmlspecialchars($r['name']) ?></b><br>
            <?= htmlspecialchars($r['address']) ?><br>
            📞 <?= htmlspecialchars($r['phone']) ?>
        </div>
    <?php endforeach; ?>

    <hr>

    <!-- VALIDÉS -->
    <h3>🟢 Restaurants validés (GPS)</h3>

    <?php foreach ($restaurants_validated as $r): ?>
        <div class="card">
            <b><?= htmlspecialchars($r['name']) ?></b><br>
            <?= htmlspecialchars($r['address']) ?><br>

            📍 <?= $r['lat'] ?> , <?= $r['lng'] ?>
        </div>
    <?php endforeach; ?>

</div>

<!-- =========================
     CARTE
========================= -->
<div id="map"></div>

</div>

<!-- =========================
     JS CARTE
========================= -->
<script>
var restaurants = <?= json_encode($restaurants_validated) ?>;

var map = L.map('map').setView([45.1885, 5.7245], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

restaurants.forEach(r => {

    if (r.lat && r.lng) {

        L.marker([r.lat, r.lng])
        .addTo(map)
        .bindPopup(
            `<b>${r.name}</b><br>
             ${r.address}<br>
             📍 ${r.lat}, ${r.lng}<br>
             ${r.description || ""}<br>
             📞 ${r.phone || ""}`
        );
    }
});
</script>

</body>
</html>
