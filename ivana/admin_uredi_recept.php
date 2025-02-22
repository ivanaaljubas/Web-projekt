<?php
session_start();
include 'spoj.php';

// Provjera prijavljenog admina
if (!isset($_SESSION['korisnik_uloga']) || $_SESSION['korisnik_uloga'] !== 'admin') {
    header('Location: prijava.php');
    exit();
}

// Dohvaćanje ID-a recepta iz URL-a
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_recepti.php?error=Nije odabran recept za uređivanje");
    exit();
}

$id = (int)$_GET['id'];

// Dohvati podatke o receptu
$stmt = $spoj->prepare("SELECT * FROM recepti WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$rezultat = $stmt->get_result();

if ($rezultat->num_rows === 0) {
    header("Location: admin_recepti.php?error=Recept ne postoji");
    exit();
}

$recept = $rezultat->fetch_assoc();

// Ako je forma poslana, ažuriraj recept
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = htmlspecialchars($_POST['naslov']);
    $opis = htmlspecialchars($_POST['opis']);
    $priprema = htmlspecialchars($_POST['priprema']);
    $vrijeme = htmlspecialchars($_POST['vrijeme']);
    $slika = $recept['slika']; // Početno postavi staru sliku

    // Ako je označeno brisanje slike
    if (isset($_POST['obrisi_sliku']) && $_POST['obrisi_sliku'] == '1') {
        if (!empty($recept['slika']) && file_exists($recept['slika'])) {
            unlink($recept['slika']); // Obriši sliku sa servera
        }
        $slika = ''; // Ukloni sliku iz baze
    }

    // Upload nove slike
    if (!empty($_FILES['slika']['name'])) {
        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $slika_naziv = basename($_FILES["slika"]["name"]);
        $target_file = $target_dir . $slika_naziv;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["slika"]["tmp_name"], $target_file)) {
                $slika = $target_file; // Spremi novu putanju slike
            } else {
                echo "Greška pri uploadu slike.";
            }
        } else {
            echo "Format slike nije dozvoljen.";
        }
    }

    // Ažuriranje recepta u bazi
    $stmt = $spoj->prepare("UPDATE recepti SET naslov=?, opis=?, priprema=?, vrijeme_pripreme=?, slika=? WHERE id=?");
    $stmt->bind_param("sssssi", $naslov, $opis, $priprema, $vrijeme, $slika, $id);

    if ($stmt->execute()) {
        header("Location: admin_recepti.php?success=Recept ažuriran!");
        exit();
    } else {
        echo "Greška pri ažuriranju recepta.";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uredi recept</title>
    <link rel="stylesheet" href="admin_uredi_recept.css">
</head>

<body>
<header>
    <div class="header-container">
        <div class="logo">
            <img src="logo.png" alt="Logo" class="logo-img">
            <h1>Kuhinjska Čarolija</h1>
        </div>
        <div class="auth-links">
            <a href="admin.php"> Povratak na admin panel</a>
            <a href="admin_recepti.php"> Uredi recepte</a>
        </div>
    </div>
</header>

<main>
<div class="form-container">
    <h2>Uredi recept</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Naslov -->
        <label class="section-title">Naslov:</label>
        <input type="text" name="naslov" value="<?php echo htmlspecialchars($recept['naslov']); ?>" required>

        <!-- Opis -->
        <label class="section-title">Opis:</label>
        <textarea name="opis" required><?php echo htmlspecialchars($recept['opis']); ?></textarea>

        <!-- Priprema -->
        <label class="section-title">Priprema:</label>
        <textarea name="priprema" required><?php echo htmlspecialchars($recept['priprema']); ?></textarea>

        <!-- Vrijeme pripreme -->
        <label class="section-title">Vrijeme pripreme:</label>
        <input type="text" name="vrijeme" value="<?php echo htmlspecialchars($recept['vrijeme_pripreme']); ?>" required>

        <!-- Slika -->
        <label class="section-title">Slika:</label>
        <?php if (!empty($recept['slika'])): ?>
            <img src="<?php echo $recept['slika']; ?>" alt="Trenutna slika" class="recipe-image">
        <?php endif; ?>

        <!-- File input -->
        <div class="file-upload">
            <label for="file-input">📂 Odaberi sliku</label>
            <input type="file" id="file-input" name="slika">
            <span class="file-name">Nijedna datoteka nije odabrana</span>
        </div>

        <!-- Checkbox za brisanje slike -->
        <div class="checkbox-group">
            <input type="checkbox" name="obrisi_sliku" value="1"> Obriši sliku
        </div>

        <!-- Gumb za spremanje -->
        <button type="submit">💾 Spremi promjene</button>
    </form>
</div>

<!-- Skripta za prikaz naziva odabrane datoteke -->
<script>
    document.getElementById("file-input").addEventListener("change", function() {
        let fileName = this.files.length > 0 ? this.files[0].name : "Nijedna datoteka nije odabrana";
        document.querySelector(".file-name").textContent = fileName;
    });
</script>


</main>

<footer>
    <p>© 2025 Kuhinjska Čarolija. Sva prava pridržana.</p>
</footer>

</body>
</html>


