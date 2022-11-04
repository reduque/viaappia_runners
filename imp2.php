<?php
$cuerpo="sum dolor sit amet, consectetur adipiscing elit";
$params=[
    "c" => 1,
    "p" => 28,
    "t" => $cuerpo
];

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
    exit;

    /*
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $respuesta = curl_exec($ch);
    curl_close($ch);
    */

} catch (Exception $e) {
   echo '<h4>Error de conexión con servidor. Favor intenta mas tarde</h4>'; exit;
}
echo $respuesta;