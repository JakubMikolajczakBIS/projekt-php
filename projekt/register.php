<?php
    require 'funkcje.php';
    if(isset($_SESSION['uuid'])){
        header("location: index.php");
    }
    if(isset($_POST['submit'])){
        $odpowiedz = rejestracja($_POST['imie'],$_POST['nazwisko'],$_POST['pesel'],$_POST['telefon'], $_POST['haslo'], $_POST['haslo_confirm']);;
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
        </div>

    </div>
    <div class="mid">
        <div class="login-wrapper">
            <div>
                <h2>Rejestracja</h2>
            </div>
            <form action="" method="POST">
                <label for="imie">Imię:<input type="text" name="imie" value="<?php echo @$_POST['imie']; ?>"></label>
                <label for="nazwisko">Nazwisko:<input type="" name="nazwisko" value="<?php echo @$_POST['nazwisko']; ?>"></label>
                <label for="pesel">PESEL:<input type="number" name="pesel" value="<?php echo @$_POST['pesel']; ?>"></label>
                <label for="telefon">Numer telefonu:<input type="number" name="telefon" value="<?php echo @$_POST['telefon']; ?>"></label>
                <label for="haslo">Hasło:<input type="password" name="haslo" value="<?php echo @$_POST['haslo']; ?>"></label>
                <label for="haslo_confirm">Potwierdź hasło:<input type="password" name="haslo_confirm" value="<?php echo @$_POST['haslo_confirm']; ?>"></label>
                <p class="error">
                    <?php
                        if(@$odpowiedz == "success"){
                            header("location: login.php");
                        }else{
                            echo @$odpowiedz;
                        }
                    ?>
                </p>
                <button type="submit" name="submit">Zarejestruj się</button>
            </form>
            <a href="login.php">Zaloguj się</a>
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