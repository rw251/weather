<?php

defined('__ROOT__') or define("__ROOT__", dirname(dirname(__FILE__)));
require __ROOT__.'/cron/financeUtils.php';
require __ROOT__.'/cron/notificationUtils.php';

$data = getAllFinancialData();

$notifications = array();
$queries = array();
$oclock09 = date("H",strtotime("today 9 am Europe/London"));
$oclock12 = date("H",strtotime("today 12 pm Europe/London"));
$oclock17 = date("H",strtotime("today 5 pm Europe/London"));
$oclock20 = date("H",strtotime("today 8 pm Europe/London"));
for($i=0; $i < count($data); $i++) {
    $isUpdatedToday = $data[$i]['isUpdatedToday'] ? 'TRUE' : 'FALSE';
    $changeToday = $data[$i]['changeToday'] == '?' ? "NULL" : $data[$i]['changeToday'];
    $changeYear = $data[$i]['changeYear'] == '?' ? "NULL" : $data[$i]['changeYear'];
    if($data[$i]['oldPrice']!=$data[$i]['newPrice'] && (date('H')==$oclock09 || date('H')==$oclock12 || date('H')==$oclock17  )) {
        $notifications[] = $data[$i]['name'] . ' was ' . $data[$i]['oldPrice'] . ' is now ' . $data[$i]['newPrice'] . ' (' . $changeToday . '%).';
    } else if(!$isUpdatedToday && date('H')==$oclock20) {
        $notifications[] = $data[$i]['name'] . ' day change is unreported.';
    } else if(date('H')==$oclock20) {
        $notifications[] = $data[$i]['name'] . ' day change is ' . $changeToday . '%.';
    }
    $queries[] = "({$data[$i]['id']}, '{$data[$i]['newDate']}', {$data[$i]['newPrice']}, {$changeToday}, {$changeYear}, {$isUpdatedToday})";
}

$sql = "INSERT INTO `financeValues` (`financeId`, `date`, `value`, `dayChange`, `yearChange`, `updatedToday`) VALUES ";
$q = join(',', $queries);

if(strlen($q) > 0) {
    $sql = $sql . $q . "ON DUPLICATE KEY UPDATE `date`=VALUES(`date`),`value`=VALUES(`value`),`dayChange`=VALUES(`dayChange`),`yearChange`=VALUES(`yearChange`),`updatedToday`=VALUES(`updatedToday`)";
    $con=dbConnect();
    mysqli_query($con, $sql);
}

if(sizeof($notifications) === 0) {
    exit();
}

notify($notifications);

?>
