<?php
session_start();
include 'spoj.php'; // Uključi vezu s bazom

header('Content-Type: application/json'); // Postavi JSON header

// Provjera je li korisnik prijavljen
if (!isset($_SESSION['korisnik_id'])) {
    echo json_encode(["success" => false, "error" => "Morate biti prijavljeni."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['recept_id']) && isset($_POST['komentar'])) {
        $recept_id = $_POST['recept_id'];
        $korisnik_id = $_SESSION['korisnik_id'];
        $komentar = htmlspecialchars($_POST['komentar']); // Sigurnosna obrada

        // Provjera postoji li recept_id u bazi
        $query_check_recept = "SELECT id FROM recepti WHERE id = ?";
        $stmt_check = $spoj->prepare($query_check_recept);
        $stmt_check->bind_param("i", $recept_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows == 0) {
            echo json_encode(["success" => false, "error" => "Recept ne postoji."]);
            exit;
        }

        // SQL upit za dodavanje komentara
        $query = "INSERT INTO komentari (recept_id, korisnik_id, komentar, datum) VALUES (?, ?, ?, NOW())";
        $stmt = $spoj->prepare($query);
        $stmt->bind_param("iis", $recept_id, $korisnik_id, $komentar);

        if ($stmt->execute()) {
            // Dodavanje aktivnosti u tablicu aktivnosti
            $query_aktivnost = "INSERT INTO aktivnosti (korisnik_id, tip_aktivnosti, detalji) VALUES (?, 'komentar', ?)";
            $stmt_aktivnost = $spoj->prepare($query_aktivnost);
            $detalji = json_encode(['recept_id' => $recept_id, 'komentar' => $komentar]);
            $stmt_aktivnost->bind_param("is", $korisnik_id, $detalji);
            $stmt_aktivnost->execute();

            // Vratimo JSON 
            echo json_encode(["success" => true, "message" => "Komentar uspješno dodan!"]);
        } else {
            echo json_encode(["success" => false, "error" => "Greška prilikom dodavanja komentara."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Nisu poslani svi potrebni podaci."]);
    }
}
?>
