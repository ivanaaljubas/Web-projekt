<?php
session_start();
include 'spoj.php';

// Provjera je li admin
if (!isset($_SESSION['korisnik_uloga']) || $_SESSION['korisnik_uloga'] !== 'admin') {
    header('Location: prijava.php');
    exit();
}

// Brisanje recepta ako je postavljen ID u URL-u
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Dohvaćanje podataka o slici prije brisanja
    $rezultat = $spoj->query("SELECT slika FROM recepti WHERE id = $id");
    $redak = $rezultat->fetch_assoc();
    $slika = $redak['slika'];

    // Brisanje recepta iz baze podataka
    $spoj->query("DELETE FROM recepti WHERE id = $id");

    // Brisanje slike ako postoji
    if ($slika && file_exists($slika)) {
        unlink($slika);
    }

    header('Location: obrisi_recept.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obriši recept</title>
    <link rel="stylesheet" href="obrisi_recept.css">
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">
            <img src="logo.png" alt="Logo" class="logo-img">
            <h1>Kuhinjska čarolija</h1>
        </div>
        <div class="header-links">
            <a href="admin.php" class="admin-back-btn">Povratak na Admin Panel</a>

        </div>

    </div>
</header>

<main>
    <h1 class="popis-recepata">Popis recepata</h1>
    <table>
        <thead>
            <tr>
                <th>Slika</th>
                <th>Naziv recepta</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Dohvaćanje svih recepata iz baze podataka
            $recepti = $spoj->query("SELECT id, naslov, slika FROM recepti");
            while ($recept = $recepti->fetch_assoc()) {
                // Provjera postoji li slika za recept, ako ne, koristi zadanu sliku
                $slika = $recept['slika'] ? $recept['slika'] : 'default.jpg';
                // Prikazivanje svakog recepta u tablici
                echo "<tr>
                        <td><img src='$slika' alt='Slika recepta' style='width: 80px; height: 80px; border-radius: 8px; object-fit: cover;'></td>
                        <td>" . htmlspecialchars($recept['naslov']) . "</td>
                        <td><a href='obrisi_recept.php?id=" . $recept['id'] . "' class='delete-btn'>Obriši</a></td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<footer>
    &copy; 2025 Kuhinjska Čarolija. Sva prava pridržana.
</footer>

</body>
</html>
