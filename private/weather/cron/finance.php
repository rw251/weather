<?php

define("__ROOT__", dirname(dirname(__FILE__)));
include __ROOT__.'/config.php';

$con = mysqli_connect(CONST_DB_HOST,CONST_DB_USERNAME,CONST_DB_PASSWORD,CONST_DB_NAME);

if (mysqli_connect_errno()) {
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    die("Didn't connect to db");
}

$urls = array();

if ($result = mysqli_query($con,'SELECT f.`id`, `name`, `url`, `date`, `value` FROM `finance` f LEFT OUTER JOIN `financeValues` v ON v.financeId = f.id')) {
    /* fetch object array */
    while ($row = $result->fetch_row()) {
        $urls[] =  array(
            "id" => $row[0], 
            "name" => $row[1], 
            "url" => $row[2], 
            "date" => $row[3], 
            "value" => $row[4]
        );
    }

    /* free result set */
    mysqli_free_result($result);
}

$url_count = count($urls);

$curl_arr = array();
$master = curl_multi_init();

for($i = 0; $i < $url_count; $i++) {
    $curl_arr[$i] = curl_init($urls[$i]['url']);
    curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($master, $curl_arr[$i]);
}

do {
    curl_multi_exec($master,$running);
} while($running > 0);

$notifications = array();

$sql = "INSERT INTO `financeValues` (`financeId`, `date`, `value`) VALUES ";
for($i = 0; $i < $url_count; $i++)
{
    $html = curl_multi_getcontent  ( $curl_arr[$i]  );

    $ft_doc = new DOMDocument();
    
    libxml_use_internal_errors(TRUE); //disable libxml errors
    
    if(!empty($html)){ //if any html is actually returned
    
        $ft_doc->loadHTML($html);
        libxml_clear_errors(); //remove errors for yucky html
        
        $ft_xpath = new DOMXPath($ft_doc);
    
        //get all the h2's with an id
        $item = $ft_xpath->query("//*[contains(concat(' ', @class, ' '), ' mod-tearsheet-overview__quote__bar ')]/li[1]/span[2]");

        $newValue = str_replace(",","",$item[0]->textContent);
        $newDate = new DateTime();
        $newDate = $newDate->format('Y-m-d H:i:s');
        $id = $urls[$i]['id'];
        // same value
        if($urls[$i]['value'] != $newValue) {
            $notifications[] = $urls[$i]['name'] . ' was ' . $urls[$i]['value'] . ' is now ' . $newValue . '.';
        }

        $queries[] = "($id, '$newDate', $newValue)";
    }
}

$q = join(',', $queries);

if(strlen($q) > 0) {
    $sql = $sql . $q . "ON DUPLICATE KEY UPDATE `date`=VALUES(`date`),`value`=VALUES(`value`)";
    mysqli_query($con, $sql);
}

if(sizeof($notifications) === 0) {
    exit();
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

$auth = array(
    'GCM' => 'AAAASU6hZZ0:APA91bEcMjc3bOrTaPVBD5_gAoxgkVxNSQjflZD3y-hTUTzOBDVszUKOPpLt3KpGrvjo8EZnDB0QrfVf2JKy8BrauseGt7xNvhgS-wPjCZP3ZMgSr4T_YSLel4N7250hK2syNRUgR91iyA6YL7CxNhem_T0vvxRsmw',
    'VAPID' => array(
        'subject' => 'mailto:1234richardwilliams@gmail.com',
        'publicKey' => "BKhC8j8oicregDpLh2k7qG8Za4Bp8CN_eEQ486lQCzEa1n2PtZ-gFiU4og7sH-qJJtneCP5I8BG3F9O2nGEPPsk",
        'privateKey' => CONST_PUSH_PRIVATE_KEY, // in the real world, this would be in a secret file
    ),
);

$subscription = Subscription::create([
    'endpoint' => $pushUrl,
    'publicKey'=> $p256dh,
    'authToken' => $authToken
]);
$webPush = new WebPush($auth);
$res = $webPush->sendNotification(
    $subscription,
    json_encode($notifications),
    true
);


?>
