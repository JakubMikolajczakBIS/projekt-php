<?php
    require 'funkcje.php';
    if(!isset($_SESSION['uuid'])){
        header("location: login.php");
    }
    if(isset($_GET['logout'])){
        wyloguj();
    }
    if(isset($_GET['anuluj'])){
        anulujTermin($_GET['anuluj']);
    }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja do dentysty</title>
    <link rel="stylesheet" href="css/wyglad.css">
    <script src="js/skrypt.js"></script>
</head>
<body>
    <div class="top">
        <h2>Rejestracja on-line</h2>
        <div>
            <a href="index.php">Rezerwacja terminu</a>
            <a href="?logout">Wyloguj</a>
        </div>

    </div>
    <div class="mid">
        <h1>Moje wizyty</h1>
        <div class="rejestracja-wrapper">
            <div class="wizyty-lista">
                <?php foreach(pobierzWizyty() as $wizyta){ ?>
                    <div class="wizyta">
                        <h3>ID wizyty: <?php echo $wizyta[0] ?></h3>
                        <p>Termin: <?php echo $wizyta[1]." ".$wizyta[2].":00"; ?></p>
                        <?php if(sprawdzDate($wizyta[1])==1){  ?>
                            <div class="wizyta-buttons">
                                <a href="zmiana.php?wizyta_id=<?php echo $wizyta[0]; ?>">Zmień termin</a><a href="?anuluj=<?php echo $wizyta[0]; ?>">Anuluj wizytę</a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="bot">
        <h2>Dr Stomatoligii Jarosław Mikołajczak</h2>
        <div>
            <h3>Gabinet stomatologiczny "Ząbek"</h3>
            <h4>ul. Myśliwska 9, 64-510 Wronki</h4>
        </div>
    </div>
</body>
</html>