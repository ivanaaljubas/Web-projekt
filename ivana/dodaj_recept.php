<?php
session_start();
include 'spoj.php';

// Provjera je li korisnik admin
if (!isset($_SESSION['korisnik_uloga']) || $_SESSION['korisnik_uloga'] !== 'admin') {
    header('Location: prijava.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = trim($_POST['naslov']); //trim() uklanja suvišne razmake s početka i kraja unesenog teksta.
    $opis = trim($_POST['opis']);
    $sastojci = trim($_POST['sastojci']);
    $vrijeme_pripreme = trim($_POST['vrijeme_pripreme']);
    $priprema = trim($_POST['priprema']);
    $kategorija_id = (int)$_POST['kategorija_id'];
    $slika = $_FILES['slika']; //$_FILES['slika'] dohvaća sliku koju je korisnik odabrao.

    // Provjera i prijenos slike
    $slika_naziv = basename($slika['name']);
    $ekstenzija = strtolower(pathinfo($slika_naziv, PATHINFO_EXTENSION));
    $dozvoljene_ekstenzije = ['jpg', 'jpeg', 'png', 'gif'];
    $slika_putanja = $slika_naziv;

    if (!in_array($ekstenzija, $dozvoljene_ekstenzije)) {
        $error = 'Dozvoljeni formati su: jpg, jpeg, png, gif.';
    } elseif ($slika['size'] > 2 * 1024 * 1024) { // Ograničenje veličine na 2MB
        $error = 'Veličina slike ne smije biti veća od 2MB.';
    } else {
        if (move_uploaded_file($slika['tmp_name'], $slika_putanja)) {
            // Ako je uspješno preneseno, nastavi s dodavanjem u bazu
            $sql = "INSERT INTO recepti (naslov, opis, sastojci, vrijeme_pripreme, priprema, kategorija_id, slika) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $spoj->prepare($sql);
            $stmt->bind_param("sssssis", $naslov, $opis, $sastojci, $vrijeme_pripreme, $priprema, $kategorija_id, $slika_putanja);
            //Ako je korisnik prijavljen, bilježimo aktivnost u tablicu aktivnosti.

            if ($stmt->execute()) {
                if (isset($_SESSION['korisnik_id'])) {
                    $korisnik_id = $_SESSION['korisnik_id'];
                    $akcija = "Dodao novi recept";
                    $sql_aktivnost = "INSERT INTO aktivnosti (korisnik_id, akcija) VALUES ('$korisnik_id', '$akcija')";
                    mysqli_query($spoj, $sql_aktivnost);
                }
                header('Location: admin.php');
                exit();
            } else {
                $error = 'Greška pri dodavanju recepta!';
            }
        } else {
            $error = 'Greška pri prijenosu slike!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj recept</title>
    <link rel="stylesheet" href="dodaj_recept.css">
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <img src="logo.png" alt="Logo" class="logo-img">
            <h1>Kuhinjska čarolija</h1>
        </div>
        <form action="admin.php">
            <a href="admin.php" class="admin-back-btn">Povratak na admin panel</a>

        </form>
    </div>
</header>

<main>
    <div class="form-container">
        <h2>Dodaj novi recept</h2>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="dodaj_recept.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="naslov">Naslov:</label>
                <input type="text" name="naslov" id="naslov" required>
            </div>
            <div class="form-group">
                <label for="opis">Opis:</label>
                <textarea name="opis" id="opis" required></textarea>
            </div>
            <div class="form-group">
                <label for="sastojci">Sastojci:</label>
                <textarea name="sastojci" id="sastojci" required></textarea>
            </div>
            <div class="form-group">
                <label for="vrijeme_pripreme">Vrijeme pripreme:</label>
                <input type="text" name="vrijeme_pripreme" id="vrijeme_pripreme" required>
            </div>
            <div class="form-group">
                <label for="priprema">Priprema:</label>
                <textarea name="priprema" id="priprema" required></textarea>
            </div>
            <div class="form-group">
                <label for="file-upload" class="custom-file-upload">
                    Odaberi datoteku
                </label>
                <input id="file-upload" type="file" name="slika" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="kategorija_id">Kategorija:</label>
                <select name="kategorija_id" id="kategorija_id" required>
                    <option value="1">Predjela</option>
                    <option value="2">Glavna jela</option>
                    <option value="3">Deserti</option>
                    <option value="4">Veganska jela</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Dodaj recept</button>
        </form>
    </div>
</main>

<footer>
    <p>© 2025 Kuhinjska čarolija. Sva prava pridržana.</p>
</footer>
</body>
</html>
