<?php
session_start();
include 'spoj.php';

/* dohvacanje korisnika*/
$sql = "SELECT a.id, k.ime AS korisnik_ime, a.tip_aktivnosti, a.detalji, a.datum, 
               COALESCE(r.naslov, 'Nema recepta') AS ime_recepta  
        FROM aktivnosti a
        JOIN korisnici k ON a.korisnik_id = k.id
        LEFT JOIN recepti r ON JSON_UNQUOTE(JSON_EXTRACT(a.detalji, '$.recept_id')) = r.id
        ORDER BY a.datum DESC";

/* izvrsavanje upita */ 
$result = $spoj->query($sql);
if (!$result) {
    die("Greška u SQL upitu: " . $spoj->error); /*ako ne uspije program se pregida i prikazuje gresku*/
}

$aktivnosti = $result->fetch_all(MYSQLI_ASSOC); /* Dobijeni rezultati se prebacuju u asocijativni niz*/
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
   
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Administracija Aktivnosti</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2c2c2c;
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        header {
            background-color: #222;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1);
            padding: 10px;
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
            padding: 0 15px;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo-img {
            max-width: 80px;
            height: auto;
        }
        h1 {
            font-size: 20px;
            color: #fff;
            font-weight: bold;
            margin-left: 10px;
        }
        .admin-back {
            background-color: #000;
            color: #c9a227;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .admin-back:hover {
            background-color: #c9a227;
            color: #000;
        }
        main {
            flex: 1;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #333;
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #ffd700;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .delete-activity-btn, #delete-selected {
            background-color: #ff0000;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-activity-btn:hover, #delete-selected:hover {
            background-color: #cc0000;
        }
        footer {
            text-align: center;
            padding: 10px;
            background: #222;
            color: #fff;
            margin-top: auto;
        }
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            .logo {
                flex-direction: column;
                align-items: center;
            }
            h1 {
                margin-left: 0;
                font-size: 18px;
            }
            .logo-img {
                max-width: 60px;
            }
            main {
                padding: 10px;
            }
            h2 {
                font-size: 22px;
            }
            th, td {
                font-size: 14px;
            }
            .delete-activity-btn {
                padding: 3px 8px;
                font-size: 12px;
            }
            #delete-selected {
                padding: 5px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="logo.png" alt="Kuhinjska Čarolija" class="logo-img">
                <h1>Kuhinjska Čarolija</h1>
            </div>
            <a href="admin.php" class="admin-back">Povratak na Admin Panel</a>
        </div>
    </header>

    <main>
        <h2>Pregled aktivnosti korisnika</h2>
        <button id="delete-selected">Obriši označene</button>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Odaberi</th>
                        <th>ID</th>
                        <th>Korisnik</th>
                        <th>Tip aktivnosti</th>
                        <th>Detalji</th>
                        <th>Datum</th>
                        <th>Akcija</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aktivnosti as $aktivnost): ?>
                    <tr>
                        <td><input type="checkbox" class="delete-checkbox" value="<?= htmlspecialchars($aktivnost['id']) ?>"></td>
                        <td><?= htmlspecialchars($aktivnost['id']) ?></td>
                        <td><?= htmlspecialchars($aktivnost['korisnik_ime']) ?></td>
                        <td><?= htmlspecialchars($aktivnost['tip_aktivnosti']) ?></td>
                        <td><?= htmlspecialchars($aktivnost['ime_recepta']) ?></td>
                        <td><?= htmlspecialchars($aktivnost['datum']) ?></td>
                        <td><button class="delete-activity-btn" onclick="deleteActivity(<?= htmlspecialchars($aktivnost['id']) ?>)">Izbriši</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Kuhinjska čarolija. Sva prava pridržana.
    </footer>

    <script>
        function deleteActivity(aktivnostId) {
            if (confirm("Jeste li sigurni da želite izbrisati ovu aktivnost?")) {
                fetch('izbrisi_aktivnost.php', {
                    method: 'POST',
                    body: new URLSearchParams({ 'aktivnost_id': aktivnostId }),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert("Greška: " + data.error);
                    }
                })
                .catch(error => console.error("Greška pri brisanju:", error));
            }
        }

        document.getElementById("delete-selected").addEventListener("click", function() {
            let checkedBoxes = document.querySelectorAll(".delete-checkbox:checked");
            if (checkedBoxes.length === 0) {
                alert("Odaberite barem jednu aktivnost!");
                return;
            }
            let ids = Array.from(checkedBoxes).map(box => box.value);
            fetch('izbrisi_aktivnost.php', {
                method: 'POST',
                body: new URLSearchParams({ 'aktivnost_ids': JSON.stringify(ids) }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("Greška: " + data.error);
                }
            })
            .catch(error => console.error("Greška pri brisanju:", error));
        });
    </script>
</body>
</html>
