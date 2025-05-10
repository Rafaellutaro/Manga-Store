<?php
session_start();
include_once 'connection.php';

require 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;


MercadoPagoConfig::setAccessToken("TEST-7044352387989428-022013-88e564687f1086f98eef38226c079b2a-1201195997");

$order_ref = 'WEBORDER-' . date('Ymd-His') . '-' . uniqid();
$userid = $_SESSION['user_id'] ?? null;

$sqlOrder = "INSERT INTO llx_commande (ref, fk_soc, date_creation, date_commande, fk_user_author, fk_statut, total_ht, entity)
                 VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlOrder);
    $stmt->execute([
        $order_ref,
        $userid,
        1,
        0,
        0,
        1
    ]);

$orderId = $stmt->insert_id;
$stmt->close();

$cart = $_SESSION['cart'];    
$items = [];

foreach ($cart as $item) {
    $item = [
        "id" => $item['id'],
        "title" => $item['label'],
        "picture_url" => "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $item["filepath"] . "/" . $item["filename"],
        "quantity" => $item['quantity'],
        "currency_id" => "BRL",
        "unit_price" => $item['price']
    ];
    $items[] = $item;
}

$_SESSION['boughtCard'] = $items;
$boughtCard = $_SESSION['boughtCard'];

$sqlLine = "INSERT INTO llx_commandedet (fk_commande, label, fk_product, qty, subprice, total_ht, product_type)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtLine = $conn->prepare($sqlLine);

    foreach ($boughtCard as $item) {
        $qty = $item['quantity'];
        $unit_price = $item['unit_price'];
        $line_ht = $qty * $unit_price;

        $stmtLine->execute([
            $orderId,
            $item['title'],
            $item['id'],
            $qty,
            $unit_price,
            $line_ht,
            0
        ]);
    }

    $stmtLine->close();

$client = new PreferenceClient();

$preference = $client->create([
"back_urls"=>array(
    "success" => "https://" . $_SERVER['HTTP_HOST'] . "/manga/success_register.php",
    "failure" => "https://test.com/failure",
    "pending" => "https://test.com/pending"
),
"notification_url" => "https://" . $_SERVER['HTTP_HOST'] . "/manga/weebhook.php", // ✅ Webhook goes here
"external_reference" => $orderId,
"differential_pricing" => array(
    "id" => 1,
),
"expires" => false,
"items" => $items,
    ]);

//echo json_encode($preference, JSON_PRETTY_PRINT);

$initPoint = $preference->init_point;

header("Location: {$initPoint}");
unset($_SESSION['cart'], $_SESSION['boughtCard']);
?>