<?php
session_start();
include_once 'connection.php';

require 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;


MercadoPagoConfig::setAccessToken("TEST-7044352387989428-022013-88e564687f1086f98eef38226c079b2a-1201195997");

$cart = $_SESSION['cart'];
$items = [];

foreach ($cart as $item) {
    $item = [
        "id" => $item['id'],
        "title" => $item['label'],
        "description" => 'Manga top',
        "picture_url" => "http://$dbhost/img/" . $item["filepath"] . "/" . $item["filename"],
        "category_id" => "Manga",
        "quantity" => $item['quantity'],
        "currency_id" => "BRL",
        "unit_price" => $item['price']
    ];
    $items[] = $item;
    $_SESSION['boughtCard'] = $items;
}


$client = new PreferenceClient();

$preference = $client->create([
"back_urls"=>array(
    "success" => "http://localhost:3000/success_payment.php",
    "failure" => "https://test.com/failure",
    "pending" => "https://test.com/pending"
),
"notification_url" => "https://yourdomain.com/webhook.php", // ✅ Webhook goes here
"differential_pricing" => array(
    "id" => 1,
),
"expires" => false,
"items" => $items,
    ]);

//echo json_encode($preference, JSON_PRETTY_PRINT);

$initPoint = $preference->init_point;

header("Location: {$initPoint}");
?>