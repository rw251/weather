<?php

defined('__ROOT__') or define("__ROOT__", dirname(dirname(__FILE__)));
require __ROOT__.'/cron/financeUtils.php';
require __ROOT__.'/cron/notificationUtils.php';

$data = getAllFinancialData();

$notifications = array();
$queries = array();
for($i=0; $i < count($data); $i++) {
    //$notifications[] = $data[$i]['id'] . ' was ' . $data[$i]['oldPrice'] . ' is now ' . $data[$i]['newPrice'] . '.';
    $changeToday = $data[$i]['changeToday'] == '?' ? "NULL" : $data[$i]['changeToday'];
    $changeYear = $data[$i]['changeYear'] == '?' ? "NULL" : $data[$i]['changeYear'];
    if($data[$i]['oldPrice']!=$data[$i]['newPrice'] && (date('H')=='08' || date('H')=='12' || date('H')=='17'  )) {
        $notifications[] = $data[$i]['name'] . ' was ' . $data[$i]['oldPrice'] . ' is now ' . $data[$i]['newPrice'] . '.' . date('H') . '(' . $changeToday . '%)';
    }
    $queries[] = "({$data[$i]['id']}, '{$data[$i]['newDate']}', {$data[$i]['newPrice']}, {$changeToday}, {$changeYear})";
}

$sql = "INSERT INTO `financeValues` (`financeId`, `date`, `value`, `dayChange`, `yearChange`) VALUES ";
$q = join(',', $queries);

if(strlen($q) > 0) {
    $sql = $sql . $q . "ON DUPLICATE KEY UPDATE `date`=VALUES(`date`),`value`=VALUES(`value`),`dayChange`=VALUES(`dayChange`),`yearChange`=VALUES(`yearChange`)";
    $con=dbConnect();
    mysqli_query($con, $sql);
}

if(sizeof($notifications) === 0) {
    exit();
}

notify($notifications);



?>
