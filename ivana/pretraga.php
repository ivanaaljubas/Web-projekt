<?php
include 'spoj.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$recepti = [];

if ($query !== '') {
    $sql = "SELECT * FROM recepti WHERE naslov LIKE ? OR opis LIKE ?";
    $stmt = $spoj->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $recepti = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezultati pretrage - Kuhinjska Čarolija</title>
    <style>
        /* Globalni stilovi */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f8f8;
    color: #333;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Header */
header {
    background-color: #121212;
    box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1);
    padding: 18px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 20px;
}

.logo {
    display: flex;
    align-items: center;
}

.logo-img {
    width: 55px;
    height: 55px;
    margin-right: 12px;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.2);
}

h1 {
    font-size: 26px;
    color: #fff;
    font-weight: bold;
}

/* Navigacija */
.nav-container {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Gumb za početnu */
.home-button {
    background-color: transparent;
    color: #C9A227;
    border: 2px solid #C9A227;
    padding: 10px 18px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease-in-out;
    display: inline-block;
}

.home-button:hover {
    background-color: #C9A227;
    color: #121212;
    transform: scale(1.1);
}

/* Search bar */
.search-form {
    display: flex;
    align-items: center;
    background: #333;
    padding: 6px 15px;
    border-radius: 30px;
}

.search-input {
    padding: 10px;
    border: none;
    background: none;
    outline: none;
    width: 250px;
    color: #fff;
    font-size: 16px;
}

.search-input::placeholder {
    color: #bbb;
}

.search-button {
    background-color: transparent;
    color: #C9A227;
    border: 2px solid #C9A227;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-button:hover {
    background-color: #C9A227;
    color: #121212;
}

/* Glavni sadržaj */
main {
    flex: 1;
    max-width: 1000px;
    margin: 30px auto;
    padding: 30px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

/* Zlatna boja za naslov rezultata pretrage */
h2 {
    font-size: 36px;
    color: #C9A227;
    margin-bottom: 25px;
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
}

/* Lista recepata */
.recipe-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

/* Stil kartice recepta */
.recipe-card {
    background: white;
    border-radius: 15px;
    width: 300px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    border: 2px solid #d4af37;
}

.recipe-card:hover {
    transform: translateY(-5px); /* Efekt podizanja kartice */
}

/* Link koji pokriva cijelu karticu */
.recipe-card a {
    text-decoration: none;
    color: inherit;
    display: block;
}

/* Slika recepta */
.recipe-image {
    width: 100% !important;  /* Slika će zauzeti punu širinu kartice */
    height: auto !important;  /* Zadržava originalne proporcije */
    border-radius: 10px;  /* Zaobljeni rubovi */
    display: block;  /* Uklanja bijele prostore oko slike */
    object-fit: cover;  /* Sprječava razvlačenje slike */
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); /* Blaga sjena */
}


/* Uklanjamo hover efekt sa slike */
.recipe-card:hover .recipe-image {
    transform: none !important;
}

/* Info sekcija recepta */
.recipe-info {
    padding: 15px;
    text-align: center;
}

/* Animacija naslova recepta */
.recipe-info h3 {
    font-size: 20px;
    color: #333;
    font-weight: bold;
    margin-bottom: 10px;
    text-transform: uppercase;
    transition: color 0.3s ease, transform 0.2s ease;
}

.recipe-info h3:hover {
    color: #d4af37;
    transform: scale(1.1);
}

.no-results {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    color: #C9A227;
}

/* Footer */
footer {
    background-color: #222;
    color: white;
    text-align: center;
    padding: 20px;
    margin-top: 40px;
    border-top: 3px solid #d4af37;
}

    </style>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <img src="logo.png" alt="Logo" class="logo-img">
            <h1>Kuhinjska Čarolija</h1>
        </div>
        <div class="nav-container">
            <a href="index.php" class="home-button">Početna</a>
            <form action="pretraga.php" method="GET" class="search-form">
                <input type="text" name="query" class="search-input" placeholder="Pretraži recepte..." value="<?= htmlspecialchars($query) ?>" required>
                <button type="submit" class="search-button">🔍</button>
            </form>
        </div>
    </div>
</header>

<main>
    <h2>Rezultati pretrage za: "<?= htmlspecialchars($query) ?>"</h2>

    <ul class="recipe-list">
        <?php if (!empty($recepti)): ?>
            <?php foreach ($recepti as $recept): ?>
                <li class="recipe-card">
                    <a href="recept.php?id=<?= $recept['id'] ?>">
                        <img src="<?= !empty($recept['slika']) ? htmlspecialchars($recept['slika']) : 'default.jpg' ?>" alt="Slika recepta" class="recipe-image">
                        <div class="recipe-info">
                            <h3><?= htmlspecialchars($recept['naslov']) ?></h3>
                            <p><?= substr(htmlspecialchars($recept['opis']), 0, 100) ?>...</p>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">Nema rezultata za vašu pretragu.</p>
        <?php endif; ?>
    </ul>
</main>

<footer>
    <p>&copy; 2025 Kuhinjska Čarolija. Sva prava pridržana.</p>
</footer>
</body>
</html>
