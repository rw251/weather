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

print_r($urls);

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
        print_r($urls[$i]['value']);
        print_r($newValue);
        // same value
        if($urls[$i]['value'] != $newValue) {
            $notifications[] = 'It is different!';
        }

        $queries[] = "($id, '$newDate', $newValue)";
    }
}

$q = join(',', $queries);

if(strlen($q) > 0) {
    $sql = $sql . $q . "ON DUPLICATE KEY UPDATE `date`=VALUES(`date`),`value`=VALUES(`value`)";
    mysqli_query($con, $sql);
}

print_r($notifications);

?>
