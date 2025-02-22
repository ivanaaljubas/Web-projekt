<?php
session_start();
include 'spoj.php';

// Provjera je li korisnik prijavljen i ima li administratorska prava
if (!isset($_SESSION['korisnik_uloga']) || $_SESSION['korisnik_uloga'] !== 'admin') {
    header('Location: prijava.php'); // Preusmjeri ako nije admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="logo.png" alt="Logo" class="logo-img">
                <h1>Kuhinjska čarolija - Admin Panel</h1>
            </div>
            <nav class="auth-links">
                <a href="index.php">Upravljanje stranicom</a>
                <a href="odjava.php">Odjava</a>
            </nav>
        </div>
    </header>

    <!-- Glavni sadržaj -->
<main>
    <h2>Dobrodošli, <?php echo htmlspecialchars($_SESSION['korisnik_ime']); ?> (Admin)</h2>
    <div class="filters">
        <!-- Ovdje možete dodati filtriranje aktivnosti ako želite -->
    </div>
    <div class="recipe-list">
        <div class="recipe-card">
            <div class="recipe-info">
                <h3><a href="dodaj_recept.php">Dodaj novi recept</a></h3>
            </div>
        </div>

        <div class="recipe-card">
            <div class="recipe-info">
                <h3><a href="obrisi_recept.php">Obriši recept</a></h3>
            </div>
        </div>

        <div class="recipe-card">
            <div class="recipe-info">
                <h3><a href="admin_aktivnosti.php">Pregled aktivnosti korisnika</a></h3>
            </div>
        </div>

        <div class="recipe-card">
            <div class="recipe-info">
                <h3><a href="admin_uredi_recept.php">Uređivanje recepta</a></h3>
            </div>
        </div>
    </div>
</main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Kuhinjska čarolija. Sva prava pridržana.</p>
    </footer>
</body>
</html>
