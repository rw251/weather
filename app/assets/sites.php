<?php
session_start();

if(!isset($_SESSION["sites"])) {
  $host = "http://datapoint.metoffice.gov.uk/public/data/val/wxfcs/all/json/sitelist?key=" . getenv('MET_OFFICE_KEY');
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $host);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // enable thie following for debugging
  // curl_setopt($ch, CURLOPT_VERBOSE, true);
  
  $_SESSION["sites"] = curl_exec($ch);
  curl_close($ch);

}

header('Content-Type: application/json');
echo $_SESSION["sites"]

?>
