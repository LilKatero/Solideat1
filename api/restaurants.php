<?php
require "../config/db.php";

echo json_encode(
    $pdo->query("SELECT * FROM restaurants WHERE validated=1")->fetchAll()
);