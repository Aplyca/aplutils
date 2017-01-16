<?php


$url = 'http://maps.google.com/maps/geo';
$type = 'csv';

$apiKey = "ABQIAAAAxyO1O8bCzabQ-dcwcFja6xQGSNtzETAwpS-H-PNq1y9sER3ftxQk1Cy__itF7qDDUa5MxOt6v_T5gg" ;
$url = sprintf('%s?q=%s&output=%s&key=%s&oe=utf-8',
                           $url,
                           urlencode($_GET['q']),
                           $type,
                           $apiKey);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;



/*
$url = 'http://maps.google.com/maps/api/geocode/';
$type = 'json';
$apiKey = "ABQIAAAAxyO1O8bCzabQ-dcwcFja6xQGSNtzETAwpS-H-PNq1y9sER3ftxQk1Cy__itF7qDDUa5MxOt6v_T5gg" ;
$encodedAddress = urlencode($_GET['q']);

$url = sprintf('%s?q=%s&output=%s&key=%s&oe=utf-8',
                           $url,
                           urlencode($_GET['q']),
                           $type,
                           $apiKey);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;*/



$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>
