
<?php
// Datoteka za kreiranje baze i tablice

$host = "localhost";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Greška pri spajanju na MySQL: " . $conn->connect_error);
}

// Kreiraj bazu
$sql = "CREATE DATABASE IF NOT EXISTS ivana";
if ($conn->query($sql) === TRUE) {
    echo "Baza podataka 'ivana' je uspješno kreirana!<br>";
} else {
    echo "Greška pri kreiranju baze: " . $conn->error . "<br>";
}

$conn->select_db("ivana");

// Kreiraj tablicu korisnika
$sql = "CREATE TABLE IF NOT EXISTS korisnici (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    lozinka VARCHAR(255) NOT NULL,
    uloga ENUM('admin', 'korisnik') DEFAULT 'korisnik'
)";

if ($conn->query($sql) === TRUE) {
    echo "Tablica 'korisnici' je uspješno kreirana!<br>";
} else {
    echo "Greška pri kreiranju tablice: " . $conn->error . "<br>";
}

$conn->close();
?>

<?php