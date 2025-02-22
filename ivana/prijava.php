<?php
session_start();
header("Content-Type: application/json");

include 'spoj.php'; // Uključi spajanje na bazu

// Proveri da li je zahtev POST i da li su podaci poslati
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'], $_POST['lozinka'])) {
    echo json_encode(["status" => "error", "message" => "Neispravan zahtev!"]);
    exit;
}

$email = trim($_POST['email']);
$lozinka = trim($_POST['lozinka']);

// Provera da li su polja prazna
if (empty($email) || empty($lozinka)) {
    echo json_encode(["status" => "error", "message" => "Morate uneti e-mail i lozinku!"]);
    exit;
}

// Priprema SQL upita
$sql = "SELECT * FROM korisnici WHERE email = ?";
$stmt = $spoj->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Greška u upitu: " . $spoj->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Provera da li korisnik postoji
if ($result->num_rows > 0) {
    $korisnik = $result->fetch_assoc();

    // Provera lozinke
    if (password_verify($lozinka, $korisnik['lozinka'])) {
        $_SESSION['korisnik_id'] = $korisnik['id'];
        $_SESSION['korisnik_ime'] = $korisnik['ime'];
        $_SESSION['korisnik_uloga'] = $korisnik['uloga'];

        session_regenerate_id(true); // Sprečavanje sesijskih napada

        // Snimanje aktivnosti prijave u bazu
        $aktivnost_sql = "INSERT INTO aktivnosti (korisnik_id, tip_aktivnosti, detalji) VALUES (?, 'prijava', '')";
        $aktivnost_stmt = $spoj->prepare($aktivnost_sql);

        if ($aktivnost_stmt) {
            $aktivnost_stmt->bind_param("i", $korisnik['id']);
            $aktivnost_stmt->execute();
        }

        echo json_encode(["status" => "success"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Pogrešna lozinka!"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "E-mail nije registriran!"]);
    exit;
}
