<?php
    require 'funkcje.php';
    if(!isset($_SESSION['uuid'])){
        header("location: login.php");
    }
    if(isset($_GET['logout'])){
        wyloguj();
    }
    if(isset($_GET['rezerwuj'])){
        print_r(rezerwujTermin($_GET['rezerwuj']));
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
            <a href="konto.php">Moje wizyty</a>
            <a href="?logout">Wyloguj</a>
        </div>

    </div>
    <div class="mid">
        <h1>Rezerwacja terminu</h1>
        <div class="rejestracja-wrapper">
            <div class="datamenu">
                <?php $i=0; foreach(terminy() as $termin) {?>
                    <div class="datamenu-dzien" data-selected="false" onclick="datamenu(<?php echo $i;?>);"><?php echo $termin;?></div>
                <?php $i++; }?>
            </div>
            <?php $d=0; foreach(terminy() as $termin) { $terminyGodzina=terminyGodzina($termin); $godzinyZajete=array(); foreach($terminyGodzina as $row){ $godzinyZajete[]=$row[0];}?>
                <div class="terminy" data-selected="false">
                    <div class='terminy-tytul'>
                        <h3><?php echo $termin;?></h3>
                        <p>Dostepne godziny:</p>
                    </div>
                    <div class="terminy-lista">
                    <?php for($i=8; $i<17; $i++){ ?>
                            <?php if(!in_array($i,$godzinyZajete)){?>
                                <a href='?rezerwuj=<?php echo $d.'-'.$i; ?>'>
                                <div class="termin wolny"><span><?php echo $termin;?></span> <span><?php echo $i;?>:00</span></div>
                                </a>
                            <?php } else{ ?>
                                <div class="termin zajety"><span><?php echo $termin;?></span> <span><?php echo $i;?>:00</span></div>
                            <?php }
                        } ?>
                    </div>
                </div>
            <?php $d++; }?>
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