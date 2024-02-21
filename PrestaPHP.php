<?php

function getDolarVenta() {
    $url = 'https://www.infodolar.com/cotizacion-dolar-provincia-cordoba.aspx';
    $response = file_get_contents($url);
    $dom = new DOMDocument();
    libxml_use_internal_errors(true); 
    $dom->loadHTML($response);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $dolar_blue_venta = $xpath->query("//table[@id='BluePromedio' and @class='cotizaciones']//td[@class='colCompraVenta']/following-sibling::td[1]")->item(0)->textContent;
    $dolar_blue_venta = explode('=', explode('$', $dolar_blue_venta)[1])[0];
    $dolar_blue_venta = str_replace(",", ".", str_replace(".", "", trim($dolar_blue_venta)));

    if(is_numeric($dolar_blue_venta)) {
        return (float)$dolar_blue_venta;
    } else {
        return 0; // Valor predeterminado si no se puede convertir a flotante
    }
}

$password = 'PASSWORD_API';
$dolar_venta = getDolarVenta();

//TABLE WITH ID
$xmlString = '<?xml version="1.0" encoding="UTF-8"?><currency><id>2</id> <conversion_rate>ss</conversion_rate></currency>';

$nuevaString = str_replace("ss", $dolar_venta, $xmlString);
echo $nuevaString;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://itechstore.ar/api/currencies/2');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, $nuevaString);
curl_setopt($ch, CURLOPT_USERPWD, "$password");

$response = curl_exec($ch);
if($response === false) {
    $error = curl_error($ch);
    echo "Error en la solicitud CURL: " . $error;
} else {
    echo $response;
}

curl_close($ch);

?>
