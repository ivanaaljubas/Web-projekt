<?php
session_start();
include 'spoj.php';

// Provjera je li korisnik prijavljen i ima li ulogu 'admin'
if (!isset($_SESSION['korisnik_id']) || $_SESSION['korisnik_uloga'] !== 'admin') {
    echo "<p>Nemate ovlasti za unos recepta.</p>";
    exit;  // Prekidamo daljnje izvođenje koda
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naslov = $_POST['naslov'];
    $opis = $_POST['opis'];
    $upute = $_POST['upute'];
    $slika = '';

    // Obrada slike (ako je odabrana)
    if (isset($_FILES['slika']) && $_FILES['slika']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["slika"]["name"]);
        
        // Provjera je li slika stvarno slika (nije obavezno, ali može pomoći u sigurnosti)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            move_uploaded_file($_FILES["slika"]["tmp_name"], $target_file);
            $slika = $target_file;
        } else {
            echo "<p>Dozvoljeni formati slika su JPG, JPEG, PNG i GIF.</p>";
            exit;
        }
    }

    // Unos recepta u bazu podataka
    $sql = "INSERT INTO recepti (naslov, opis, upute, slika) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $naslov, $opis, $upute, $slika);
    
    if ($stmt->execute()) {
        echo "<p>Recept je uspješno unesen!</p>";
    } else {
        echo "<p>Došlo je do pogreške prilikom unosa recepta.</p>";
    }
}
?>

<!-- Forma za unos recepta -->
<form action="unos_recepta.php" method="POST" enctype="multipart/form-data">
    <label for="naslov">Naslov:</label>
    <input type="text" name="naslov" required><br>

    <label for="opis">Opis:</label>
    <textarea name="opis" required></textarea><br>

    <label for="upute">Upute:</label>
    <textarea name="upute" required></textarea><br>

    <label for="slika">Slika:</label>
    <input type="file" name="slika" accept="image/*"><br>

    <button type="submit">Unesi recept</button>
</form>
