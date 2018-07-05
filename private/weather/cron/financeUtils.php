<?php

defined('__ROOT__') or define("__ROOT__", dirname(dirname(__FILE__)));
require_once __ROOT__.'/cron/db.php';

function getExistingFinancialData($con) {
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

    return $urls;
}

function getNewFinancialData($urls) {
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

    for($i = 0; $i < $url_count; $i++)
    {
        $html = curl_multi_getcontent  ( $curl_arr[$i]  );

        $ft_doc = new DOMDocument();
        
        libxml_use_internal_errors(TRUE); //disable libxml errors
        
        if(!empty($html)){ //if any html is actually returned
        
            $ft_doc->loadHTML($html);
            libxml_clear_errors(); //remove errors for yucky html
            
            $ft_xpath = new DOMXPath($ft_doc);

            $oneYearChange = '?';
            $price = '?';
            $todayChange = '?';
            
            for($j = 1; $j < 5; $j++) {
                $title = $ft_xpath->query("//*[contains(concat(' ', @class, ' '), ' mod-tearsheet-overview__quote__bar ')]/li[$j]/span[1]");
                $value = $ft_xpath->query("//*[contains(concat(' ', @class, ' '), ' mod-tearsheet-overview__quote__bar ')]/li[$j]/span[2]");

                if($title->length > 0) {
                    $title = $title[0]->textContent;
                    $value = $value[0]->textContent;
                    if(preg_match("/(?:1|one).*year/i", $title)) {
                        $oneYearChange = preg_replace("/^[^\d]*([\d.]+)%/", "$1", $value);
                        if(!is_numeric($oneYearChange)) $oneYearChange = '?';
                    } else if(preg_match("/price/i", $title)) {
                        $price = str_replace("," ,"", $value);
                        if(!is_numeric($price)) $price = '?';
                    } else if(preg_match("/today/i", $title)) {
                        $todayChange = preg_replace("/^[^\/]*\/ *([^%]+)%/", "$1", $value);
                        if(!is_numeric($todayChange)) $todayChange = '?';
                    }
                }
            }

            $newDate = new DateTime();
            $newDate = $newDate->format('Y-m-d H:i:s');

            $data[] = array(
                "id" => $urls[$i]['id'],
                "name" => $urls[$i]['name'],
                "oldPrice" => $urls[$i]['value'],
                "oldDate" => $urls[$i]['date'],
                "newPrice" => $price,
                "newDate" => $newDate,
                "changeToday" => $todayChange,
                "changeYear" => $oneYearChange
            );
        }
    }

    return $data;
}

function getAllFinancialData() {
    $con = dbConnect();
    $urls = getExistingFinancialData($con);
    $data = getNewFinancialData($urls);
    return $data;
}

// print_r(getAllFinancialData());

?>