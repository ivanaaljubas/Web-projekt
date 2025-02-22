<?php
session_start();
include 'spoj.php'; // Uključi vezu s bazom

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['aktivnost_id'])) {
        // Brisanje jedne aktivnosti
        $aktivnost_id = intval($_POST['aktivnost_id']);
        $sql = "DELETE FROM aktivnosti WHERE id = ?";
        $stmt = $spoj->prepare($sql);
        $stmt->bind_param("i", $aktivnost_id);
        $success = $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['aktivnost_ids'])) {
        // Brisanje više aktivnosti
        $aktivnost_ids = json_decode($_POST['aktivnost_ids'], true);
        if (!is_array($aktivnost_ids)) {
            echo json_encode(["success" => false, "error" => "Neispravan format podataka"]);
            exit();
        }

        $placeholders = implode(",", array_fill(0, count($aktivnost_ids), "?"));
        $sql = "DELETE FROM aktivnosti WHERE id IN ($placeholders)";
        $stmt = $spoj->prepare($sql);
        
        $types = str_repeat("i", count($aktivnost_ids)); // svi su int
        $stmt->bind_param($types, ...$aktivnost_ids);
        $success = $stmt->execute();
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Neispravan zahtjev"]);
        exit();
    }

    if ($success) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $spoj->error]);
    }

    $spoj->close();
    exit();
}

echo json_encode(["success" => false, "error" => "Neispravan zahtjev"]);
?>
