<?php
session_start();

// Get the place to check - 352857 is nlw
$place=!empty($_GET['place']) ? trim($_GET['place']) : "352827";

if(!isset($_SESSION["p".$place]) || (time()/3600 - $_SESSION["t".$place])>1) {
    $host = "http://datapoint.metoffice.gov.uk/public/data/val/wxfcs/all/json/" . $place . "?res=3hourly&key=" . getenv('MET_OFFICE_KEY');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // enable thie following for debugging
    // curl_setopt($ch, CURLOPT_VERBOSE, true);

    $_SESSION["p".$place] = curl_exec($ch);
    $_SESSION["t".$place] = time()/3600;
}

header('Content-Type: application/json');
echo $_SESSION["p".$place];
?>
