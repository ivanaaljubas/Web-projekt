<?php
session_start(); // Započinjemo sesiju na početku dokumenta
include 'spoj.php'; // Uključi spajanje na bazu podataka
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predjela - Kuhinjska čarolija</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="logo.png" alt="Logo" class="logo-img">
                <h1>Predjela - Kuhinjska čarolija</h1>
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

    <!-- Glavni sadržaj za Predjela -->
    <main>
        <div class="content-container">
            <h2>Predjela</h2>
            <div class="filters">
                <button class="filter-button" onclick="window.location.href='index.php'">Početna</button>
                <button class="filter-button" onclick="window.location.href='glavno.php'">Glavna jela</button>
                <button class="filter-button" onclick="window.location.href='deserti.php'">Deserti</button>
                <button class="filter-button" onclick="window.location.href='veganska.php'">Veganska jela</button>
            </div>
            <div class="recipe-list">
                <?php
                try {
                    // SQL upit za dohvaćanje recepata iz kategorije Predjela
                    $sql = "SELECT * FROM recepti WHERE kategorija_id = (SELECT id FROM kategorije WHERE naziv = 'Predjela') LIMIT 6";
                    $stmt = $spoj->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($recept = $result->fetch_assoc()) {
                            echo "<div class='recipe-card'>";
                            echo "<a href='recept.php?id=" . htmlspecialchars($recept['id']) . "'>"; // Omogućava klik na cijelu karticu
                            echo "<img src='" . htmlspecialchars($recept['slika'] ?? 'placeholder.jpg') . "' alt='" . htmlspecialchars($recept['naslov']) . "' class='recipe-image'>";
                            echo "<div class='recipe-info'>";
                            echo "<h3>" . htmlspecialchars($recept['naslov'] ?? 'Nema naslova') . "</h3>";
                            echo "<p>" . htmlspecialchars(substr($recept['opis'] ?? 'Nema opisa', 0, 50)) . "...</p>";
                            echo "</div>";
                            echo "</a>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Nema recepata za prikazivanje.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p>Došlo je do pogreške: " . htmlspecialchars($e->getMessage()) . "</p>";
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
