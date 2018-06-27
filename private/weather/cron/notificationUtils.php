<?php

defined('__ROOT__') or define("__ROOT__", dirname(dirname(__FILE__)));
require_once __ROOT__.'/cron/db.php';
require_once __ROOT__ . '/vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function getNotificationTokens($con) {
    if ($result = mysqli_query($con,'SELECT pushEndpointUrl, p256dh, auth FROM `users` WHERE `id` = 11')) {
        /* fetch object array */
        $row = $result->fetch_row();
        $tokens = array (
            "pushUrl" => $row[0],
            "p256dh" => $row[1],
            "authToken" => $row[2]
        );
        /* free result set */
        mysqli_free_result($result);
    }
    return $tokens;
}

function sendNotification($tokens, $notification) {
    $auth = array(
        'GCM' => 'AAAASU6hZZ0:APA91bEcMjc3bOrTaPVBD5_gAoxgkVxNSQjflZD3y-hTUTzOBDVszUKOPpLt3KpGrvjo8EZnDB0QrfVf2JKy8BrauseGt7xNvhgS-wPjCZP3ZMgSr4T_YSLel4N7250hK2syNRUgR91iyA6YL7CxNhem_T0vvxRsmw',
        'VAPID' => array(
            'subject' => 'mailto:1234richardwilliams@gmail.com',
            'publicKey' => "BKhC8j8oicregDpLh2k7qG8Za4Bp8CN_eEQ486lQCzEa1n2PtZ-gFiU4og7sH-qJJtneCP5I8BG3F9O2nGEPPsk",
            'privateKey' => CONST_PUSH_PRIVATE_KEY,
        ),
    );
    
    $subscription = Subscription::create([
        'endpoint' => $tokens["pushUrl"],
        'publicKey'=> $tokens["p256dh"],
        'authToken' => $tokens["authToken"]
    ]);
    $webPush = new WebPush($auth);
    $res = $webPush->sendNotification(
        $subscription,
        json_encode($notification),
        true
    );
}

function notify($notification) {
    $con = dbConnect();
    $tokens = getNotificationTokens($con);
    sendNotification($tokens, $notification);
}

?>