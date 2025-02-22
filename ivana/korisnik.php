<!DOCTYPE html>
<html>
<head>
    <title>Recepti</title>
</head>
<body>
    <h1>Dobrodošli na stranicu o kuhanju!</h1>
    <form method="GET" action="pretraga.php">
        <input type="text" name="query" placeholder="Pretraži recepte...">
        <button type="submit">Pretraži</button>
    </form>
    <div>
        <!-- Prikaz recepata iz baze -->
        <?php
        include 'spoj.php';  // Uključi spajanje na bazu
        $sql = "SELECT * FROM recepti";  // SQL upit
        $result = $conn->query($sql);  // Izvrši upit
        
        // Prikazivanje recepata
        while ($recept = $result->fetch_assoc()) {
            echo "<h2>{$recept['naslov']}</h2>";  // Naslov recepta
            echo "<p>{$recept['opis']}</p>";  // Opis recepta
        }
        ?>
    </div>
</body>
</html>
