<?php require 'db.php'; ?>

<h2>Dashboard</h2>

<a href="logout.php">Logout</a>

<form method="POST">
<input name="name" placeholder="Restaurant">
<input name="address" placeholder="Adresse">
<input name="lat" placeholder="Latitude">
<input name="lng" placeholder="Longitude">
<input name="phone" placeholder="Téléphone">
<textarea name="description"></textarea>
<button>Ajouter</button>
</form>

<?php
if ($_POST) {
    $stmt = $pdo->prepare("
        INSERT INTO restaurants(user_id,name,address,lat,lng,description,phone)
        VALUES (?,?,?,?,?,?,?)
    ");

    $stmt->execute([
        $_SESSION['user']['id'],
        $_POST['name'],
        $_POST['address'],
        $_POST['lat'],
        $_POST['lng'],
        $_POST['description'],
        $_POST['phone']
    ]);

    echo "Restaurant envoyé pour validation";
}
?>