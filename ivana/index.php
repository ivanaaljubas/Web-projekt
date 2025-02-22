<?php
session_start();
include 'spoj.php'; // Spajanje na bazu

// Provjerava da li je korisnik prijavljen i prati pposjete
if (isset($_SESSION['korisnik_id']) && isset($spoj)) {
    $korisnik_id = $_SESSION['korisnik_id'];
    $akcija = "Posjetio početnu stranicu";

    $sql_aktivnost = "INSERT INTO aktivnosti (korisnik_id, akcija) VALUES (?, ?)";
    $stmt = $spoj->prepare($sql_aktivnost);

    if ($stmt) {
        $stmt->bind_param("is", $korisnik_id, $akcija);
        $stmt->execute();
    }
}

// Provjerava je li korisnik admin i dodavanje linka za Admin Panel
$admin_link = '';
if (isset($_SESSION['korisnik_uloga']) && $_SESSION['korisnik_uloga'] === 'admin') {
    $admin_link = '<a href="admin.php" class="admin-link">Admin Panel</a>';
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuhinjska čarolija</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    
</head>



<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="logo.png" alt="Logo" class="logo-img">
                <h1>Kuhinjska čarolija</h1>
            </div>
            <form action="pretraga.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Pretraži recepte..." required>
                <button type="submit">🔍</button>
            </form>
            <div class="auth-links">
                <?php if (isset($_SESSION['korisnik_ime'])): ?>
                    <span class="<?= ($_SESSION['korisnik_uloga'] === 'admin') ? 'admin-text' : 'user-text' ?>">Dobrodošli, <?= htmlspecialchars($_SESSION['korisnik_ime']) ?>!</span>
                    <a href='odjava.php'>Odjava</a>
                    <?= $admin_link ?>
                <?php else: ?>
                    <a href='#' onclick="openModal('modal-prijava')">Prijava</a> | <a href="#" onclick="openModal('modal-registracija'); return false;">Registracija</a>

                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Modal za prijavu -->
    <div id="modal-prijava" class="modal">
        <div class="modal-content">
            <div class="form-section">
                <span class="close" onclick="closeModal('modal-prijava')">&times;</span>
                <h2>Prijava</h2>
                <p id="prijava-poruka" style="color: red; display: none;"></p>
                <form id="prijava-forma">
                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" required>
                    <label for="lozinka">Lozinka:</label>
                    <input type="password" name="lozinka" id="lozinka" required>
                    <button type="submit">Prijava</button>
                </form>
                <p>Nemate račun? <a href="#" onclick="openModal('modal-registracija')">Registrirajte se</a>.</p>
            </div>
            <div class="avatar-section">
                <img src="Animation - 1736805607371.gif" alt="Avatar">
            </div>
        </div>
    </div>
<!-- Modal za registraciju -->
<div id="modal-registracija" class="modal">
    <div class="modal-content">
        <div class="form-section">
            <span class="close" onclick="closeModal('modal-registracija')">&times;</span>
            <h2>Registracija</h2>
            <p id="registracija-poruka" style="color: red; display: none;"></p>
            <form id="registracija-forma">
                <label for="ime">Ime:</label>
                <input type="text" name="ime" id="ime" required>
                <label for="email-registracija">E-mail:</label>
                <input type="email" name="email" id="email-registracija" required>
                <label for="lozinka-registracija">Lozinka:</label>
                <input type="password" name="lozinka" id="lozinka-registracija" required>
                <button type="submit">Registracija</button>
            </form>
            <p>Već imate račun? <a href="#" onclick="openModal('modal-prijava'); return false;">Prijavite se</a>.</p>
        </div>
        <div class="avatar-section">
            <img src="Animation - 1736805607371.gif" alt="Avatar">
        </div>
    </div>
</div>



    <!-- Glavni sadržaj -->
    <main>
        <div class="content-container">
            <h2>Pronađi inspiraciju!</h2>
            <div class="filters">
                <button class="filter-button active" onclick="window.location.href='svi_recepti.php'">Sve</button>
                <button class="filter-button" onclick="window.location.href='predjela.php'">Predjela</button>
                <button class="filter-button" onclick="window.location.href='glavno.php'">Glavna jela</button>
                <button class="filter-button" onclick="window.location.href='deserti.php'">Deserti</button>
                <button class="filter-button" onclick="window.location.href='veganska.php'">Veganska jela</button>
            </div>
            <div class="recipe-list">
                <?php
                $sql = "SELECT * FROM recepti";

                $result = $spoj->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($recept = $result->fetch_assoc()) {
                        echo "<div class='recipe-card'>";
                        echo "<a href='recept.php?id=" . urlencode($recept['id']) . "' class='recipe-link'>";
                        echo "<div class='recipe-card-content'>";

                        $slika = (!empty($recept['slika']) && file_exists($recept['slika'])) ? htmlspecialchars($recept['slika']) : 'default-image.png';
                        
                        echo "<img src='$slika' alt='" . htmlspecialchars($recept['naslov']) . "' class='recipe-image'>";
                        echo "<div class='recipe-info'>";
                        echo "<h3>" . htmlspecialchars($recept['naslov']) . "</h3>";
                        echo "<p>" . htmlspecialchars(substr($recept['opis'], 0, 50)) . "...</p>";
                        echo "</div></div></a></div>";
                    }
                } else {
                    echo "<p>Nema recepata za prikazivanje.</p>";
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Kuhinjska čarolija. Sva prava pridržana.</p>
    </footer>

    <script>
    function openModal(modalId) {
    // Zatvori sve modal prozore prije otvaranja ciljanog
    let modali = document.querySelectorAll(".modal");
    modali.forEach(modal => modal.style.display = "none");

    // Otvori željeni modal
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "flex";
    }
}

function closeModal(modalId) {
    let modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    }
}

// Zatvaranje modala kada korisnik klikne izvan njega
window.onclick = function(event) {
    let modali = document.querySelectorAll(".modal");
    modali.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
};

document.addEventListener("DOMContentLoaded", function () {
    // Funkcija za otvaranje modala
    function openModal(modalId) {
        // Zatvori sve modal prozore prije otvaranja ciljanog
        document.querySelectorAll(".modal").forEach(modal => modal.style.display = "none");

        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "flex";
        }
    }

    // Funkcija za zatvaranje modala
    function closeModal(modalId) {
        let modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
        }
    }

    // Zatvaranje modala kada korisnik klikne izvan njega
    window.onclick = function (event) {
        document.querySelectorAll(".modal").forEach(modal => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    };

    // Prijava forma
    let prijavaForma = document.getElementById("prijava-forma");
    if (prijavaForma) {
        prijavaForma.addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("prijava.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        closeModal("prijava-modal"); // Zatvori modal nakon uspešne prijave
                        location.reload(); // Osvježi stranicu
                    } else {
                        let poruka = document.getElementById("prijava-poruka");
                        poruka.textContent = data.message;
                        poruka.style.display = "block";
                    }
                })
                .catch(error => console.error("Greška:", error));
        });
    }

    // Registracija forma
    let registracijaForma = document.getElementById("registracija-forma");
    if (registracijaForma) {
        registracijaForma.addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("registracija.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        closeModal("registracija-modal"); // Zatvori modal nakon uspešne registracije
                        location.reload(); // Osvježi stranicu
                    } else {
                        let poruka = document.getElementById("registracija-poruka");
                        poruka.textContent = data.message;
                        poruka.style.display = "block";
                    }
                })
                .catch(error => console.error("Greška:", error));
        });
    }
});


    </script>
</body>
</html>
