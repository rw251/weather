<?php

define("__ROOT__", dirname(dirname(__FILE__)));
include __ROOT__.'/config.php';

$con = mysqli_connect(CONST_DB_HOST,CONST_DB_USERNAME,CONST_DB_PASSWORD,CONST_DB_NAME);

if (mysqli_connect_errno()) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    die("Didn't connect to db");
}

if ($result = mysqli_query($con,'SELECT pushEndpointUrl, p256dh, auth FROM `users` WHERE `id` = 11')) {
    /* fetch object array */
    $row = $result->fetch_row();
    $pushUrl =  $row[0];
    $p256dh =  $row[1];
    $authToken =  $row[2];
    /* free result set */
    mysqli_free_result($result);
}

require_once __ROOT__ . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$vapid = array(
    'GCM' => '314851812765',
    'VAPID' => array(
        'subject' => 'mailto:1234richardwilliams@gmail.com',
        'publicKey' => 'BKhC8j8oicregDpLh2k7qG8Za4Bp8CN_eEQ486lQCzEa1n2PtZ-gFiU4og7sH-qJJtneCP5I8BG3F9O2nGEPPsk',
        'privateKey' => CONST_PUSH_PRIVATE_KEY, // in the real world, this would be in a secret file
        'contentEncoding' => 'aes128gcm',
    ),
);

$subscription = Subscription::create([
    'endpoint' => $pushUrl,
    'publicKey'=> $p256dh,
    'authToken' => $authToken
]);
$webPush = new WebPush($vapid);

$messages = array();
$messages[] = 'test2';
$messages[] = 'test3';
    
$res = $webPush->sendNotification(
    $subscription,
    json_encode($messages),
    true
);

error_log(var_export($res,true));

?>
