<?php

$urls = array (
    array(
        "name" => "iShares Global Water UCITS ETF", 
        "url" => 'https://markets.ft.com/data/etfs/tearsheet/summary?s=IH2O:LSE:GBX'
    ),
    array(
        "name" => "Fidelity Institutional Emerging Markets Fund W Acc", 
        "url" => 'https://markets.ft.com/data/funds/tearsheet/summary?s=gb00b9smk778:gbx'
    ),
    array(
        "name" => "FTSE 100", 
        "url" => 'https://markets.ft.com/data/indices/tearsheet/summary?s=FTSE:FSI'
    )
);

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
        
        $values[$urls[$i]['name']] = str_replace(",","",$item[0]->textContent);
    }

}

print_r($values);


?>
