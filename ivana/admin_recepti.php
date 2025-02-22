<?php
session_start();
include 'spoj.php';

// Provjera prijavljenog admina
if (!isset($_SESSION['korisnik_uloga']) || $_SESSION['korisnik_uloga'] !== 'admin') {
    header('Location: prijava.php');
    exit();
}

// Dohvati sve recepte
$rezultati = $spoj->query("SELECT * FROM recepti");
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Uredi recepte</title>
    <link rel="stylesheet" href="style_admin_recepti.css">
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">
            <img src="logo.png" alt="Logo Kuhinjske Čarolije" class="logo-img">
            <h2 class="header-title">Kuhinjska Čarolija</h2>
        </div>
        <div class="header-links">
            <a href="admin.php" class="back-button"> Povratak na Admin Panel</a>
        </div>
    </div>
</header>

<main>
    <h1>Popis recepata</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Slika</th>
                    <th>Naslov</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($recept = $rezultati->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($recept['slika']); ?>" alt="Slika recepta: <?php echo htmlspecialchars($recept['naslov']); ?>" class="recipe-image">
                        </td>
                        <td><?php echo htmlspecialchars($recept['naslov']); ?></td>
                        <td><a href="admin_uredi_recept.php?id=<?php echo intval($recept['id']); ?>">✏️ Uredi</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <p>© 2025 Kuhinjska Čarolija. Sva prava pridržana.</p>
</footer>

</body>
</html>
