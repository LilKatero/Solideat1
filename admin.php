<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . "/config/db.php";

/* =========================
   VALIDATION / SUPPRESSION
========================= */

if (isset($_GET['validate'])) {
    $id = (int) $_GET['validate'];

    $stmt = $pdo->prepare("UPDATE restaurants SET validated = 1 WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php");
    exit;
}

/* =========================
   DATA
========================= */
$pending = $pdo->query("SELECT * FROM restaurants WHERE validated = 0")->fetchAll();
$validated = $pdo->query("SELECT * FROM restaurants WHERE validated = 1")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin validation</title>

<style>
body { font-family: Arial; background:#f5f6fa; padding:20px; }
.container { display:flex; gap:20px; }
.box { flex:1; background:white; padding:15px; border-radius:8px; }
.card { border:1px solid #ddd; padding:10px; margin-bottom:10px; }
a { display:inline-block; margin:5px; padding:5px 8px; text-decoration:none; border-radius:5px; }
.val { background:green; color:white; }
.del { background:red; color:white; }
.logout { position:fixed; top:10px; right:10px; }
</style>
</head>

<body>

<a class="logout" href="logout.php">🚪 Déconnexion</a>

<h1>🧑‍💼 Admin validation</h1>

<div class="container">

<!-- EN ATTENTE -->
<div class="box">
<h2>🟡 En attente</h2>

<?php foreach ($pending as $r): ?>
    <div class="card">
        <b><?= htmlspecialchars($r['name']) ?></b><br>
        <?= htmlspecialchars($r['address']) ?><br>

        <a class="val" href="?validate=<?= $r['id'] ?>">✔ Valider</a>
        <a class="del" href="?delete=<?= $r['id'] ?>">🗑 Supprimer</a>
    </div>
<?php endforeach; ?>

</div>

<!-- VALIDÉS -->
<div class="box">
<h2>🟢 Validés</h2>

<?php foreach ($validated as $r): ?>
    <div class="card">
        <b><?= htmlspecialchars($r['name']) ?></b><br>
        <?= htmlspecialchars($r['address']) ?><br>
        📍 <?= $r['lat'] ?> , <?= $r['lng'] ?>
    </div>
<?php endforeach; ?>

</div>

</div>

</body>
</html>