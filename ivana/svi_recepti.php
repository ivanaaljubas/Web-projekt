<?php
session_start(); // Započinjemo sesiju
include 'spoj.php';  // Uključi spajanje na bazu
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svi recepti - Kuhinjska čarolija</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="logo.png" alt="Logo" class="logo-img">
                <h1>Svi recepti - Kuhinjska čarolija</h1>
            </div>
            <form action="pretraga.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Pretraži recepte..." required>
                <button type="submit">🔍</button>
            </form>
            <div class="auth-links">
                <?php
                if (isset($_SESSION['korisnik_ime'])):
                    echo "<span>Dobrodošli, " . htmlspecialchars($_SESSION['korisnik_ime']) . "!</span>";
                    echo "<a href='odjava.php'>Odjava</a>";
                else:
                    echo "<a href='prijava.php'>Prijava</a> | <a href='registracija.php'>Registracija</a>";
                endif;
                ?>
            </div>
        </div>
    </header>

    <!-- Glavni sadržaj za sve recepte -->
    <main>
        <div class="content-container">
            <h2>Svi recepti</h2>
            <div class="filters">
                <button class="filter-button" onclick="window.location.href='index.php'">Početna</button>
                <button class="filter-button" onclick="window.location.href='predjela.php'">Predjela</button>
                <button class="filter-button" onclick="window.location.href='glavno.php'">Glavna jela</button>
                <button class="filter-button" onclick="window.location.href='deserti.php'">Deserti</button>
                <button class="filter-button" onclick="window.location.href='veganska.php'">Veganska jela</button>
                
            </div>
            <div class="recipe-list">
                <?php
                // Dohvati sve recepte
                $sql = "SELECT * FROM recepti";
                $result = $spoj->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($recept = $result->fetch_assoc()) {
                        echo "<div class='recipe-card'>";
                        echo "<img src='" . htmlspecialchars($recept['slika']) . "' alt='" . htmlspecialchars($recept['naslov']) . "' class='recipe-image'>";
                        echo "<div class='recipe-info'>";
                        echo "<h3>" . htmlspecialchars($recept['naslov']) . "</h3>";
                        echo "<p>" . htmlspecialchars(substr($recept['opis'], 0, 50)) . "...</p>";
                        echo "<a href='recept.php?id=" . urlencode($recept['id']) . "'>Pročitaj više</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nema recepata za prikazivanje.</p>";
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Kuhinjska čarolija. Sva prava pridržana.</p>
    </footer>
</body>
</html>
