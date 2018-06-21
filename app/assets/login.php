<?php

require_once '../../private/weather/vendor/autoload.php';

session_start();
use Symfony\Component\HttpFoundation\Response;

// Get $id_token via HTTPS POST.
$id_token = $_POST['idtoken'];
$status='failed';
$message='';

if($id_token) {
  $client = new Google_Client(['client_id' => '314851812765-7vag9c5sb1fpd9o35rr9t8p6gj5firt9.apps.googleusercontent.com']);
  $payload = $client->verifyIdToken($id_token);
  if ($payload) {
    $userId = $payload['sub'];
    $email = $payload['email'];
    $_SESSION['user'] = $userId;

    include '../../private/weather/config.php';
    
    $con = mysqli_connect(CONST_DB_HOST,CONST_DB_USERNAME,CONST_DB_PASSWORD,CONST_DB_NAME);
    
    if (mysqli_connect_errno()) {
        error_log("Failed to connect to MySQL: " . mysqli_connect_error());
        die("Didn't connect to db");
    }

    $query = "INSERT INTO users SET openid='$email', googleSub='$userId' ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
    mysqli_query($con, $query);
    $id = mysqli_insert_id($con);
    $message = $email;
    $status = 'success';
  } else {
    // Invalid ID token
    $message = 'Invalid id token';
  }
} else {
  $message = 'No id token';
}

$response = new Response(
  '{"status":"'. $status .'","message":"' . $message . '"}',
  Response::HTTP_OK,
  array('content-type' => 'application/json') //
);

$response->send();

?>