<?php
session_start();

// Get the place to check - 352857 is nlw
$place=!empty($_GET['place']) ? trim($_GET['place']) : "352827";
if(!isset($_SESSION[$place])) {

    $host = "http://datapoint.metoffice.gov.uk/public/data/val/wxfcs/all/json/" . $place . "?res=3hourly&key=" . getenv('MET_OFFICE_KEY');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // enable thie following for debugging
    // curl_setopt($ch, CURLOPT_VERBOSE, true);

    $_SESSION[$place] = curl_exec($ch);
}

header('Content-Type: application/json');
echo $_SESSION[$place];

?>
