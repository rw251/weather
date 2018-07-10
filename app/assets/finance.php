<?php

require_once '../../private/weather/vendor/autoload.php';

session_start();
use Symfony\Component\HttpFoundation\Response;

$status='failed';
$message='';

include '../../private/weather/config.php';
  
$con = mysqli_connect(CONST_DB_HOST,CONST_DB_USERNAME,CONST_DB_PASSWORD,CONST_DB_NAME);

if (mysqli_connect_errno()) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    die("Didn't connect to db");
}

$user = $_SESSION['user'];

if ($result = mysqli_query($con,"SELECT f.name, f.url, fv.value, fv.dayChange, fv.updatedToday FROM `finance` f INNER JOIN `users` u on u.id = f.userId INNER JOIN `financeValues` fv on fv.financeId = f.id WHERE googleSub='$user'")) {
  /* fetch object array */
  $values = array();
  while ($row = $result->fetch_row()) {
      $values[] =  array(
          "name" => $row[0], 
          "url" => $row[1], 
          "value" => $row[2], 
          "dayChange" => $row[3], 
          "updatedToday" => $row[4]
      );
  }

  /* free result set */
  mysqli_free_result($result);

  $message = $url;
  $status = 'success';
  
  $response = new Response(
    json_encode($values),
    Response::HTTP_OK,
    array('content-type' => 'application/json') //
  );

  $response->send();
}

?>