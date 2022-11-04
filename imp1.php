<?php

try {

	$url="https://viaappia.ddns.net/imprime2.php";

    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($params)
    ));
    $response = (curl_exec($ch));
    var_dump($response);

} catch (Exception $e) {
    echo '<h4>Error de conexión con servidor. Favor intenta mas tarde</h4>'; exit;
}

/*
try {
	$url="http://viaappia.ddns.net/imprime.php?c=1&p={$_GET['p']}&t=".urlencode(htmlspecialchars($_GET['t'], ENT_QUOTES));
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $pp = curl_exec($ch);
    curl_close($ch);

} catch (Exception $e) {
   echo '<h4>Error de conexión con servidor. Favor intenta mas tarde</h4>'; exit;
}

/*
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://maxchadwick.xyz');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'THIS IS THE REQUEST BODY');

curl_exec($ch);
*/