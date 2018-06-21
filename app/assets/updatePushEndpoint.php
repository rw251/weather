<?php

require_once '../../private/weather/vendor/autoload.php';

session_start();
use Symfony\Component\HttpFoundation\Response;

// Get $id_token via HTTPS POST.
$data = json_decode(file_get_contents('php://input'), true);

$url = $data['endpoint'];
$p256dh = $data['keys']['p256dh'];
$auth = $data['keys']['auth'];
$status='failed';
$message='';

if($url) {

  include '../../private/weather/config.php';
    
  $con = mysqli_connect(CONST_DB_HOST,CONST_DB_USERNAME,CONST_DB_PASSWORD,CONST_DB_NAME);
  
  if (mysqli_connect_errno()) {
      error_log("Failed to connect to MySQL: " . mysqli_connect_error());
      die("Didn't connect to db");
  }

  $user = $_SESSION['user'];
  $query = "UPDATE users SET pushEndpointUrl='$url', p256dh='$p256dh', auth='$auth' WHERE googleSub='$user'";
  mysqli_query($con, $query);
  $message = $url;
  $status = 'success';
} else {
  $message = 'No url';
}

$response = new Response(
  '{"status":"'. $status .'","message":"' . $message . '"}',
  Response::HTTP_OK,
  array('content-type' => 'application/json') //
);

$response->send();

?>