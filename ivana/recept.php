<?php
session_start();  // Pokreće sesiju

include 'spoj.php';  // Uključi vezu s bazom

if (isset($_SESSION['korisnik_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['korisnik_id'];
    $recept_id = $_GET['id'];

    $sql = "INSERT INTO aktivnosti (korisnik_id, tip_aktivnosti, detalji) VALUES (?, 'pregled', ?)";
    $stmt = $spoj->prepare($sql);
    $detalji = json_encode(['recept_id' => $recept_id]);
    $stmt->bind_param("is", $user_id, $detalji);
    $stmt->execute();
}


// Provjera pretraživanja
$search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';  // Ispravljeno ime GET parametra
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalji recepta</title>
    
    <style>


/* Globalni stilovi za tijelo */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f8f8;
    color: #333;
}

/* Header */
header {
    background-color: #222222; /* Crna boja zaglavlja */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
}

/* Kontejner za header */
.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Logo */
.logo {
    display: flex;
    align-items: center;
}

.logo-img {
    width: 55px;
    height: 55px;
    margin-right: 12px;
    border-radius: 50%;
    box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
}

h1 {
    font-size: 26px;
    color: #c9a227; /* Zlatna boja */
    font-weight: bold;
}

/* Forma za pretragu */
.search-form {
    display: flex;
    align-items: center;
    background: #222;
    padding: 5px 10px;
    border-radius: 30px;
    border: 2px solid #c9a227;
    transition: border 0.3s ease;
}

.search-form input {
    padding: 10px;
    border: none;
    border-radius: 25px;
    width: 250px;
    outline: none;
    background: transparent;
    color: #c9a227;
    font-size: 16px;
}

.search-form input::placeholder {
    color: #c9a227;
    opacity: 0.7;
}

.search-form button {
    background-color: #c9a227;
    color: #111;
    border: none;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.search-form button:hover {
    background-color: #b1911e;
    transform: scale(1.1);
}

/* Navigacija */
nav {
    display: flex;
}

nav a {
    color: #c9a227; /* Zlatni tekst */
    text-decoration: none;
    margin: 0 15px;
    font-size: 16px;
    font-weight: bold;
    position: relative;
    transition: color 0.3s ease;
}

nav a:hover {
    color: #fff;
}

nav a::after {
    content: "";
    display: block;
    width: 0;
    height: 2px;
    background: #c9a227;
    transition: width 0.3s ease;
    position: absolute;
    bottom: -4px;
    left: 50%;
    transform: translateX(-50%);
}

nav a:hover::after {
    width: 100%;
}



/* Glavni sadržaj */
main {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
}

/* Container za recept */
.recipe-container {
    max-width: 600px; /* Ograničava širinu kontejnera */
    margin: 0 auto; /* Centriranje */
    padding: 20px;
    text-align: center; /* Osigurava da je slika centrirana */
}
/* Naslov recepta */
.recipe-title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

/* Slika recepta */
.recipe-image {
    width: 100%; /* Omogućuje da slika zauzme punu širinu unutar kontejnera */
    max-width: 500px; /* Ograničava maksimalnu širinu slike */
    height: auto; /* Zadržava proporcije */
    display: block; /* Sprječava dodatni razmak */
    margin: 0 auto; /* Centriranje slike */
    border-radius: 15px; /* Zaobljeni rubovi */
    object-fit: cover; /* Osigurava da slika bude pravilno obrezana */
    border: 5px solid #c9a227; /* Zlatni obrub */
    box-shadow: 0px 0px 10px rgba(212, 175, 55, 0.5); /* Zlatni sjaj */
    padding: 5px; /* Ostavlja malo prostora unutar obruba */
    background: white; /* Poboljšava vidljivost obruba */
}

.recipe-image:hover {
    transform: scale(1.05); /* Lagano povećanje */
    filter: brightness(1.1) contrast(1.2); /* Pojačavanje boja */
    transition: all 0.3s ease-in-out;
}



.recipe-image img {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


/* Opis recepta */
.recipe-description, .recipe-ingredients, .recipe-preparation, .recipe-time {
    margin-bottom: 30px;
}

h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 10px;
}

p, ul {
    font-size: 1.1rem;
    color: #555;
}

ul {
    list-style-type: square;
    margin-left: 20px;
}

/* Lajkovi */
.likes-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    background: linear-gradient(135deg, #d4af37, #ff8c00);

    padding: 12px 20px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.likes-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.like-button {
    background: transparent;
    border: none;
    font-size: 2.2rem;
    color: #fff;
    cursor: pointer;
    transition: transform 0.3s ease, color 0.3s ease;
}

.like-button.liked {
    color: #ffd700;
    transform: scale(1.2);
}

.like-button:hover {
    transform: scale(1.3);
    color: #ffeb3b;
}

.likes-count {
    font-size: 1.4rem;
    font-weight: bold;
    color: #fff;
    text-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
    transition: 0.3s;
}

.likes-count.pulse {
    animation: pop 0.3s ease-in-out;
}

@keyframes pop {
    0% { transform: scale(1); }
    50% { transform: scale(1.4); }
    100% { transform: scale(1); }
}


/* Komentari */
.comments-container {
    margin: 20px 0;
    background-color: #1e1e1e;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}

.comments-container h3 {
    font-size: 1.8rem;
    color: #d4af37;
    margin-bottom: 15px;
}

/* Stil za pojedinačni komentar */
.comment {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #444;
}

.comment .author {
    font-weight: bold;
    color: #d4af37;
    font-size: 1.1rem;
}

.comment .text {
    margin-top: 5px;
    font-size: 1.1rem;
    color: #bbb;
}

.comment .date {
    font-size: 0.9rem;
    color: #777;
}

/* Forma za unos komentara */
.comments-container form {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
}

.comments-container textarea {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
    border-radius: 8px;
    border: 1px solid #d4af37;
    background-color: #222;
    color: white;
    margin-bottom: 10px;
    resize: none;
    transition: border-color 0.3s ease;
}

.comments-container textarea:focus {
    border-color: #ffcc00;
    outline: none;
}

.comments-container button {
    padding: 12px 20px;
    background-color: #d4af37;
    color: black;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.comments-container button:hover {
    background-color: #ffcc00;
    transform: scale(1.05);
}

/* Chat-box za komentare */
.chat-box-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    max-height: 500px;
    background-color: #1e1e1e;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    overflow: hidden;
    display: none;
    flex-direction: column;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.chat-box-container.active {
    opacity: 1;
    transform: translateY(0);
}

.chat-header {
    background-color: #d4af37;
    color: black;
    padding: 15px;
    text-align: center;
    font-weight: bold;
    cursor: pointer;
    font-size: 1.2rem;
}

.chat-messages {
    padding: 10px;
    max-height: 350px;
    overflow-y: auto;
}

.chat-messages .comment {
    border-bottom: 1px solid #444;
    padding: 8px 0;
}

.chat-messages .author {
    font-weight: bold;
    color: #d4af37;
}

.chat-messages .text {
    font-size: 1rem;
    color: #bbb;
}

.chat-input {
    display: flex;
    padding: 10px;
    border-top: 1px solid #444;
    background-color: #222;
}

.chat-input textarea {
    flex: 1;
    border: 1px solid #d4af37;
    border-radius: 5px;
    padding: 8px;
    font-size: 1rem;
    background-color: #1e1e1e;
    color: white;
    resize: none;
}

.chat-input button {
    background-color: #d4af37;
    color: black;
    border: none;
    border-radius: 5px;
    padding: 10px;
    margin-left: 10px;
    cursor: pointer;
    font-weight: bold;
}

.chat-input button:hover {
    background-color: #ffcc00;
}


.comment-button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.comment-button img {
    width: 60px; /* Smanji ako je preveliko */
    height: auto;
    filter: drop-shadow(0 0 5px rgba(212, 175, 55, 0.5)); /* Zlatni sjaj */
}

.comment-button img:hover {
    transform: scale(1.1);
    filter: drop-shadow(0 0 10px rgba(255, 204, 0, 0.7));
}
@media (max-width: 1024px) {
    .header-container {
        flex-direction: column;
        text-align: center;
    }

    .search-form {
        max-width: 80%;
    }
}

@media (max-width: 768px) {
    .header-container {
        padding: 10px;
    }

    .logo-img {
        width: 45px;
        height: 45px;
    }

    .search-form {
        width: 100%;
        max-width: 100%;
    }

    .search-form input {
        width: 80%;
        font-size: 14px;
    }

    .recipe-list {
        flex-direction: column;
        align-items: center;
    }

    .recipe-card {
        max-width: 100%;
    }

    .filters {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-button {
        padding: 10px 15px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 22px;
    }

    h2 {
        font-size: 28px;
    }

    .search-form {
        flex-direction: column;
        align-items: stretch;
    }

    .search-form input {
        width: 100%;
    }

    .search-form button {
        width: 100%;
        border-radius: 8px;
    }

    .recipe-info h3 {
        font-size: 18px;
    }

    .recipe-info a {
        font-size: 12px;
        padding: 8px 12px;
    }

    .modal-content {
        width: 95%;
        padding: 20px;
    }

    .form-section button {
        padding: 10px;
    }
}



    </style>
</head>

<header>
    <div class="header-container">
        <div class="logo">
            <img src="logo.png" alt="Logo" class="logo-img">
            <h1>Recepti</h1>
        </div>

        <form action="pretraga.php" method="GET" class="search-form">
            <input type="text" name="query" placeholder="Pretraži recepte..." value="<?php echo $search_query; ?>" required>
            <button type="submit">🔍</button>
        </form>

        <nav>
            <a href="index.php">Početna</a>
            <a href="predjela.php">Predjela</a>
            <a href="glavna.php">Glavna jela</a>
            <a href="deserti.php">Deserti</a>
            <a href="veganska.php">Veganska jela</a>
        </nav>

        <!-- Dugme za otvaranje chata -->
        <button class="comment-button">
            <img src="c.jpg" alt="Comment Icon">
        </button>

        <!-- Chat box za komentare -->
        <div class="chat-box-container" id="chatBox">
            <div class="chat-header" onclick="toggleChat()">Komentari</div>
            <div class="chat-messages" id="chatMessages">
                <!-- Komentari će se dinamički učitavati ovde -->
            </div>
            <div class="chat-input">
                <textarea id="commentText" placeholder="Dodajte komentar..."></textarea>
                <button onclick="submitComment()">➤</button>
            </div>
        </div>



    </div>
</header>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM recepti WHERE id = ?";
    $stmt = $spoj->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recept = $result->fetch_assoc();

        echo "<div class='recipe-container'>";

        echo "<h1 class='recipe-title'>" . htmlspecialchars($recept['naslov']) . "</h1>";

        if (!empty($recept['slika'])) {
            echo "<div class='recipe-image'>";
            echo "<img src='" . htmlspecialchars($recept['slika']) . "' alt='" . htmlspecialchars($recept['naslov']) . "'>";
            echo "</div>";
        }

        if (!empty($recept['opis'])) {
            echo "<div class='recipe-description'>";
            echo "<h3>Opis</h3>";
            echo "<p>" . nl2br(htmlspecialchars($recept['opis'])) . "</p>";
            echo "</div>";
        }

        if (!empty($recept['sastojci'])) {
            echo "<div class='recipe-ingredients'>";
            echo "<h3>Sastojci</h3>";
            echo "<ul>";
            $sastojci = explode(",", $recept['sastojci']);
            foreach ($sastojci as $sastojak) {
                echo "<li>" . htmlspecialchars($sastojak) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }

        if (!empty($recept['vrijeme_pripreme'])) {
            echo "<div class='recipe-time'>";
            echo "<h3>Vrijeme pripreme</h3>";
            echo "<p>" . htmlspecialchars($recept['vrijeme_pripreme']) . " minuta</p>";
            echo "</div>";
        }

        if (!empty($recept['priprema'])) {
            echo "<div class='recipe-preparation'>";
            echo "<h3>Priprema</h3>";
            echo "<p>" . htmlspecialchars($recept['priprema']) . "</p>";
            echo "</div>";
        }
        
        

        // Broj lajkova
        $sql_lajk = "SELECT COUNT(*) AS broj_lajkova FROM lajkovi WHERE recept_id = ?";
        $stmt_lajk = $spoj->prepare($sql_lajk);
        $stmt_lajk->bind_param("i", $id);
        $stmt_lajk->execute();
        $result_lajk = $stmt_lajk->get_result();
        $broj_lajkova = $result_lajk->fetch_assoc()['broj_lajkova'];

        echo "<div class='likes-container'>";
        echo "<p>Broj lajkova: <span id='like-count'>" . $broj_lajkova . "</span></p>";

        if (isset($_SESSION['korisnik_id'])) {
            echo "<button id='like-button' class='like-button' data-recept-id='" . $id . "'>❤️</button>";
        } else {
            echo "<p>Prijavite se kako biste lajkali recept.</p>";
        }
        echo "</div>";

        // Komentari
        $sql_comments = "
            SELECT komentari.*, korisnici.ime 
            FROM komentari
            JOIN korisnici ON komentari.korisnik_id = korisnici.id
            WHERE recept_id = ? 
            ORDER BY datum DESC";
        $stmt_comments = $spoj->prepare($sql_comments);
        $stmt_comments->bind_param("i", $id);
        $stmt_comments->execute();
        $comments = $stmt_comments->get_result();

        
    }
}
?>
<script>
// Funkcija za učitavanje komentara
function loadComments() {
    var receptId = "<?php echo $id; ?>"; // ID recepta iz PHP-a

    fetch('dohvati_komentare.php?recept_id=' + receptId)
        .then(response => response.json())
        .then(data => {
            var chatMessages = document.getElementById("chatMessages");
            chatMessages.innerHTML = ""; // Brišemo stare komentare

            if (data.error) {
                console.error("Greška:", data.error);
                return;
            }

            data.forEach(comment => {
                var commentDiv = document.createElement("div");
                commentDiv.classList.add("comment");
                commentDiv.innerHTML = `
                    <div class="author"><strong>${comment.ime}</strong> (${comment.datum})</div>
                    <div class="text">${comment.komentar}</div>
                `;
                chatMessages.appendChild(commentDiv);
            });
        })
        .catch(error => console.error("Greška pri učitavanju komentara:", error));
}

// Funkcija za slanje komentara
function submitComment() {
    var receptId = "<?php echo $id; ?>"; // ID recepta iz PHP-a
    var komentar = document.getElementById("commentText").value.trim();

    if (komentar === "") {
        alert("Komentar ne može biti prazan!");
        return;
    }

    fetch('komentiraj.php', {  
        method: 'POST',
        body: new URLSearchParams({
            'recept_id': receptId,
            'komentar': komentar
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(response => response.json()) // Očekujemo JSON
    .then(data => {
        if (data.success) {
            document.getElementById("commentText").value = ""; // Obriši unos
            loadComments(); // Ponovno učitavanje komentara
        } else {
            alert("Greška: " + data.error);
        }
    })
    .catch(error => console.error("Greška pri dodavanju komentara:", error));
}

// Funkcija za prikaz/skrivanje chata
function toggleChat() {
    let chatBox = document.getElementById("chatBox");

    if (chatBox.style.display === "none" || chatBox.style.display === "") {
        chatBox.style.display = "flex";
        setTimeout(() => chatBox.classList.add("active"), 10);
        loadComments(); // Učitaj komentare odmah po otvaranju chata
    } else {
        chatBox.classList.remove("active");
        setTimeout(() => chatBox.style.display = "none", 300);
    }
}

// Povezivanje gumba sa funkcijom
document.querySelector(".comment-button").addEventListener("click", toggleChat);

// Funkcija za lajk
document.getElementById('like-button')?.addEventListener('click', function () {
    var button = this;
    var receptId = button.getAttribute('data-recept-id');

    fetch('lajk.php', {
        method: 'POST',
        body: new URLSearchParams({
            'recept_id': receptId
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())  
    .then(data => {
        if (data.error) {
            console.error('Greška: ', data.error);
            return;
        }

        document.getElementById('like-count').textContent = data.broj_lajkova;

        if (data.lajkano) {
            button.classList.add('liked');
        } else {
            button.classList.remove('liked');
        }
    })
    .catch(error => {
        console.error('Greška:', error);
    });
});
</script>

</body>
</html>
