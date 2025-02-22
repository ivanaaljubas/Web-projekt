<?php
include 'spoj.php'; // Uključi spajanje na bazu

header('Content-Type: application/json'); // Osigurava JSON izlaz

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $lozinka = trim($_POST['lozinka'] ?? '');

    if (empty($ime) || empty($email) || empty($lozinka)) {
        echo json_encode(["status" => "error", "message" => "Sva polja su obavezna!"]);
        exit;
    }

    // Provera postoji li već korisnik s istim emailom
    $sql = "SELECT id FROM korisnici WHERE email = ?";
    $stmt = $spoj->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Greška u upitu: " . $spoj->error]);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "E-mail je već registriran!"]);
        exit;
    }

    // Hashiranje lozinke
    $hashed_password = password_hash($lozinka, PASSWORD_DEFAULT);

    // Dodavanje korisnika u bazu
    $sql = "INSERT INTO korisnici (ime, email, lozinka) VALUES (?, ?, ?)";
    $stmt = $spoj->prepare($sql);
    $stmt->bind_param("sss", $ime, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Došlo je do greške. Pokušajte ponovno!"]);
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registracija</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="registration-container">
        <h2>Registracija</h2>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="registracija.php">
            <label for="ime">Ime:</label>
            <input type="text" name="ime" id="ime" required><br>

            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="lozinka">Lozinka:</label>
            <input type="password" name="lozinka" id="lozinka" required><br>

            <button type="submit">Registriraj se</button>
        </form>
        <p>Već imate račun? <a href="index.php?prijava=1">Prijavite se</a>.</p>
    </div>
</body>
</html>
