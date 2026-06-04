<?php require 'db.php'; ?>

<h2>Inscription</h2>

<form method="POST">
<input name="name" placeholder="Nom">
<input name="email" type="email">
<input name="password" type="password">
<button>Créer compte</button>
</form>

<?php
if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO users(name,email,password) VALUES (?,?,?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        password_hash($_POST['password'], PASSWORD_DEFAULT)
    ]);

    header("Location: login.php");
}
?>