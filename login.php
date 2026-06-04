<?php
session_start();

$error = "";

$admin_user = "admin";
$admin_pass = "admin123"; // 🔐 ton mot de passe fixe

if (isset($_POST['login'])) {

    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === $admin_user && $pass === $admin_pass) {

        $_SESSION['admin'] = true;

        header("Location: admin.php");
        exit;

    } else {
        $error = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>

<style>
body {
    font-family: Arial;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    background:#f5f6fa;
}
.box {
    background:white;
    padding:20px;
    border-radius:8px;
    width:300px;
}
input {
    width:100%;
    padding:8px;
    margin-bottom:10px;
}
button {
    width:100%;
    padding:10px;
    background:#2c3e50;
    color:white;
    border:none;
}
</style>
</head>

<body>

<div class="box">

<h2>🔐 Admin login</h2>

<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    <input name="username" placeholder="Utilisateur">
    <input type="password" name="password" placeholder="Mot de passe">
    <button name="login">Connexion</button>
</form>

</div>

<div style="margin-top:15px;">
    <a href="index.php" class="btn-home">🏠 Retour à l'accueil</a>
</div>

</body>
</html>