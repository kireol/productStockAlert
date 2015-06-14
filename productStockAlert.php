<?php

require 'vendor/autoload.php';
date_default_timezone_set('America/Detroit');
$HIPSHROOT = dirname(__FILE__) . "/";
require_once $HIPSHROOT . "utils.php";
$CONFIG = getConfig();
$productsToCheck = $CONFIG['products'];

foreach ($productsToCheck as $productToCheck) {
    $currentTimeStamp = array_sum(explode(' ', microtime())) * 10000;
    $url = $productToCheck['siteUrl'] . $currentTimeStamp;
    $contents = file_get_contents($url);

    $json = json_decode($contents);
    $inStockProducts = array();

    foreach ($json->data->products as $product) {
        if (strcmp($product->product_status, "expired") == 0) {
            $inStockProducts[] = $product;
        }
    }

    if (count($inStockProducts) > 0) {
        $output = "In stock:\n";
        foreach ($inStockProducts as $product) {
            $output .= $product->full_name . "\n";
            $output .= $productToCheck['siteBaseUrl'] . $product->url . ".html\n";
            $output .= "\n";
        }
        mail($productToCheck['email'], $productToCheck['productName'] . " in stock", $output);
    }
}
