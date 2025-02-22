<?php
session_start();
include 'spoj.php';  // Uključi vezu s bazom

// Provjera je li korisnik prijavljen i ima li validan recept_id
if (isset($_SESSION['korisnik_id']) && isset($_POST['recept_id'])) {
    $korisnik_id = $_SESSION['korisnik_id'];
    $recept_id = $_POST['recept_id'];

    // Provjeri je li korisnik već lajkao recept
    $sql_check = "SELECT * FROM lajkovi WHERE korisnik_id = ? AND recept_id = ?";
    $stmt_check = $spoj->prepare($sql_check);
    $stmt_check->bind_param("ii", $korisnik_id, $recept_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // Ako nije lajkao, dodaj lajk
        $sql_insert = "INSERT INTO lajkovi (korisnik_id, recept_id) VALUES (?, ?)";
        $stmt_insert = $spoj->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $korisnik_id, $recept_id);
        $stmt_insert->execute();

        // Dodaj aktivnost u tablicu aktivnosti
        $sql_aktivnost = "INSERT INTO aktivnosti (korisnik_id, tip_aktivnosti, detalji) VALUES (?, 'lajk', ?)";
        $stmt_aktivnost = $spoj->prepare($sql_aktivnost);
        $detalji = json_encode(['recept_id' => $recept_id]);
        $stmt_aktivnost->bind_param("is", $korisnik_id, $detalji);
        $stmt_aktivnost->execute();

        $lajkano = true;
    } else {
        // Ako je već lajkao, ukloni lajk
        $sql_delete = "DELETE FROM lajkovi WHERE korisnik_id = ? AND recept_id = ?";
        $stmt_delete = $spoj->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $korisnik_id, $recept_id);
        $stmt_delete->execute();

        $lajkano = false;
    }

    // Broj lajkova
    $sql_lajk = "SELECT COUNT(*) AS broj_lajkova FROM lajkovi WHERE recept_id = ?";
    $stmt_lajk = $spoj->prepare($sql_lajk);
    $stmt_lajk->bind_param("i", $recept_id);
    $stmt_lajk->execute();
    $result_lajk = $stmt_lajk->get_result();
    $broj_lajkova = $result_lajk->fetch_assoc()['broj_lajkova'];

    // Vraćanje JSON odgovora
    echo json_encode(['broj_lajkova' => $broj_lajkova, 'lajkano' => $lajkano]);
} else {
    // Ako nije prijavljen ili nije postavljen recept_id
    echo json_encode(['error' => 'Neispravan zahtjev']);
}
?>
