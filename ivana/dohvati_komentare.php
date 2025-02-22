<?php
session_start();
include 'spoj.php'; // Povezivanje s bazom

header('Content-Type: application/json'); // Vraćamo JSON, JSON je format koji olakšava razmjenu podataka između servera i klijenta.

if (!isset($_GET['recept_id'])) {
    echo json_encode(["error" => "Nema ID recepta"]);
    exit;
}

$recept_id = $_GET['recept_id'];

$query = "SELECT k.komentar, k.datum, u.ime 
          FROM komentari k
          JOIN korisnici u ON k.korisnik_id = u.id
          WHERE k.recept_id = ?
          ORDER BY k.datum DESC";

$stmt = $spoj->prepare($query);
$stmt->bind_param("i", $recept_id);
$stmt->execute();
$result = $stmt->get_result();

$komentari = [];
while ($row = $result->fetch_assoc()) {
    $komentari[] = $row;
}

echo json_encode($komentari);
?>
