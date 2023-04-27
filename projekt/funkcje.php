<?php
require 'config.php';

function polaczenie(){
    $mysqli = new mysqli(SERVER,USERNAME,PASSWORD,DATABASE);
    if($mysqli->connect_errno==0){
        return $mysqli;
    }
}

function rejestracja($imie, $nazwisko, $pesel, $telefon, $password, $confirm_password){
    $mysqli = polaczenie();
    $argumenty = func_get_args();

    $argumenty = array_map(function($wartosc){
        return trim($wartosc);
    }, $argumenty);

    foreach ($argumenty as $wartosc) {
        if(empty($wartosc)){
            return  "Wszystkie pola są wymagane";

        }
    }

    if(strlen($imie) < 3){
        return "Imię jest zbyt krótkie";
    }

    if(strlen($imie) > 16){
        return "Imię jest zbyt długie";
    }

    if (!preg_match ('/^[a-ząćęłńóśźż]+$/ui', $imie) ) {  
        return "Niepoprawne imię";  
    }

    if(strlen($imie) > 35){
        return "Nazwisko jest zbyt długie";
    }

    if (!preg_match ('/^[a-ząćęłńóśźż]+$/ui', $nazwisko) ) {  
        return "Niepoprawne nazwisko";  
    }

    if (!preg_match ('/^[0-9]*$/', $pesel) ) {  
        return "Niepoprawny PESEL";  
    } 

    if(strlen($pesel) > 11) {
        return "PESEL jest zbyt długi";
    }

    if(strlen($pesel) < 11) {
        return "PESEL jest zbyt krótki";
    }
    
    if (!preg_match ('/^[0-9]*$/', $telefon) ) {  
        return "Niepoprawny numer telefonu";  
    } 

    if(strlen($telefon) > 9) {
        return "Numer telefonu jest zbyt długi";
    }

    if(strlen($telefon) < 9) {
        return "Numer telefonu jest zbyt krótki";
    }

    $stmt = $mysqli->prepare("SELECT telefon FROM users WHERE telefon = ?");
    $stmt->bind_param("s", $telefon);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane=$wynik->fetch_assoc();
    if($dane != NULL){
        return "Konto z tym numerem telefonu już istnieje";
    }

    if(strlen($password) < 5){
        return "Hasło jest zbyt krótkie";
    }

    if(strlen($password) > 32){
        return "Hasło jest zbyt długie";
    }

    if($password != $confirm_password){
        return "Hasła nie są takie same";
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO `users` (`user_id`, `imie`, `nazwisko`, `pesel`, `telefon`, `haslo`) VALUES (NULL, ?, ?, ?, ?, ?);");
    $stmt->bind_param("ssiis", $imie, $nazwisko, $pesel, $telefon, $password_hash);
    $stmt->execute();
    if($stmt->affected_rows != 1){
        return "Wystąpił błąd";
    } else {
        return "success";
    }


}

function logowanie($telefon, $password){
    $mysqli = polaczenie();

    $telefon = trim($telefon);
    $password = trim($password);

    if($telefon == "" || $password == ""){
        return "Oba pola są wymagane";
    }

    $stmt = $mysqli->prepare("SELECT user_id, telefon, haslo FROM users WHERE telefon = ?");
    $stmt->bind_param("i", $telefon);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if($dane == NULL){
        return "Zły numer telefonu lub hasło";
    }


    if(password_verify($password, $dane['haslo']) == FALSE){
        return "Zły numer telefonu lub hasło";
    }else{
        $_SESSION['uuid']=$dane['user_id'];
        header("location: index.php");
        exit();
    }
}

function wyloguj(){
    session_destroy();
    header('location: index.php');
    exit();
}

function terminy(){
    $days   = [];
    $period = new DatePeriod(new DateTime('tomorrow'), new DateInterval('P1D'),7);

    foreach ($period as $day)
    {
        $days[] = $day->format('d').'.'.$day->format('m').'.'.$day->format('Y')." r.";
    }
    return $days;
}

function terminyGodzina($termin){
    $mysqli = polaczenie();

    $stmt = $mysqli->prepare("SELECT termin_godzina FROM wizyty WHERE termin = ?");
    $stmt->bind_param("s", $termin);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_all();

    if($dane!=NULL){
        return $dane;
    } else{
        return [[0]];
    }
}

function rezerwujTermin($termin){
    $mysqli = polaczenie();


    if(!preg_match('/^[0-8]-[8-9]|1[0-6]$/',$termin)){
        header("location:index.php");
    }

    $termin = explode('-',$termin);

    $terminDzien=terminy()[$termin[0]];
    $terminGodzina=$termin[1];

    if($terminDzien<0 || $terminDzien>8){
        header("location:index.php");
    }

    if($terminGodzina<8 || $terminGodzina>16){
        header("location:index.php");
    }

    $stmt = $mysqli->prepare("SELECT wizyta_id FROM wizyty WHERE termin = ? and termin_godzina = ?");
    $stmt->bind_param("ss", $terminDzien, $terminGodzina);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if($dane!=NULL){
        header("location: index.php");
        exit();
    }

    if(!in_array($terminDzien,terminy())){
        header("location: index.php");
    }

    $stmt = $mysqli->prepare("INSERT INTO wizyty (`wizyta_id`, `termin`, `termin_godzina`, `user_id`) VALUES (NULL, ? , ? , ?);");
    $stmt->bind_param("sii", $terminDzien, $terminGodzina, $_SESSION['uuid']);
    $stmt->execute();

    header("location: konto.php");
}

function pobierzWizyty(){
    $mysqli = polaczenie();

    $stmt = $mysqli->prepare("SELECT wizyta_id, termin, termin_godzina FROM wizyty WHERE user_id = ? order by wizyta_id");
    $stmt->bind_param("s", $_SESSION['uuid']);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane=$wynik->fetch_all();

    return $dane;
}

function sprawdzDate($data){
    $dataDzis = new DateTime();

    $data = substr($data,0,10);
    $data = str_replace(".","-",$data);
    $data = new DateTime($data);

    $interval = $dataDzis->diff($data);

    if($interval->invert==0 && $interval->days>=0){
        return 1;
    } else return 0;
}

function zmienTermin($termin, $wizyta_id){
    $mysqli = polaczenie();


    if(!preg_match('/^[0-8]-[8-9]|1[0-6]$/',$termin)){
        header("location:konto.php");
    }

    if (!preg_match ('/^[0-9]*$/', $wizyta_id) ) {  
        header("location:konto.php"); 
    }

    $stmt = $mysqli->prepare("SELECT termin FROM wizyty WHERE wizyta_id = ?");
    $stmt->bind_param("i", $wizyta_id);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if(sprawdzDate($dane['termin'])==0){
        header("location:konto.php");
        exit;
    }

    $termin = explode('-',$termin);

    $terminDzien=terminy()[$termin[0]];
    $terminGodzina=$termin[1];

    if($terminDzien<0 || $terminDzien>8){
        header("location:konto.php");
    }

    if($terminGodzina<8 || $terminGodzina>16){
        header("location:konto.php");
    }

    $stmt = $mysqli->prepare("SELECT wizyta_id FROM wizyty WHERE termin = ? and termin_godzina = ?");
    $stmt->bind_param("ss", $terminDzien, $terminGodzina);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if($dane!=NULL){
        header("location: konto.php");
        exit();
    }

    $stmt = $mysqli->prepare("SELECT wizyta_id FROM wizyty WHERE user_id = ? and wizyta_id = ?");
    $stmt->bind_param("ii", $_SESSION['uuid'], $wizyta_id);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if($dane==NULL){
        header("location: konto.php");
        exit();
    }

    if(!in_array($terminDzien,terminy())){
        header("location: konto.php");
        exit();
    }

    $stmt = $mysqli->prepare("UPDATE `wizyty` SET `termin` = ?, `termin_godzina` = ? WHERE `wizyty`.`wizyta_id` = ?");
    $stmt->bind_param("sii", $terminDzien, $terminGodzina, $wizyta_id);
    $stmt->execute();

    header("location: konto.php");
}

function anulujTermin($wizyta_id){
    $mysqli = polaczenie();

    if (!preg_match ('/^[0-9]*$/', $wizyta_id) ) {  
        header("location:konto.php"); 
    } 

    $stmt = $mysqli->prepare("SELECT wizyta_id FROM wizyty WHERE user_id = ? and wizyta_id = ?");
    $stmt->bind_param("ii", $_SESSION['uuid'], $wizyta_id);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if($dane==NULL){
        header("location: konto.php");
        exit();
    }

    $stmt = $mysqli->prepare("SELECT termin FROM wizyty WHERE wizyta_id = ?");
    $stmt->bind_param("i", $wizyta_id);
    $stmt->execute();
    $wynik = $stmt->get_result();
    $dane = $wynik->fetch_assoc();

    if(sprawdzDate($dane['termin'])==0){
        header("location:konto.php");
        exit;
    }

    $stmt = $mysqli->prepare("DELETE FROM wizyty WHERE `wizyty`.`wizyta_id` = ?");
    $stmt->bind_param("i", $wizyta_id);
    $stmt->execute();

    header("location: konto.php");
}