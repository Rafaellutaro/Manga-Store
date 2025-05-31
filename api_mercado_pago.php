<?php
session_start();
include_once 'connection.php';

require 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;


MercadoPagoConfig::setAccessToken("TEST-833496997971285-051417-d8bdcea6415385b019f549ad1e7d720b-1201195997");

// Get the selected address from the POST request
$selected_address = $_POST['selected_address'] ?? null;

if (!$selected_address) {
    error_log("Endereço não selecionado.");
}

$order_ref = 'WEBORDER-' . date('Ymd-His') . '-' . uniqid();
$userid = $_SESSION['user_id'] ?? null;
$cart = $_SESSION['cart'];
$items = [];

foreach ($cart as $item) {
    $item = [
        "id" => $item['id'],
        "title" => $item['label'],
        "picture_url" => "https://" . $_SERVER['HTTP_HOST'] . "/imgs/" . $item["filepath"] . "/" . $item["filename"],
        "quantity" => $item['quantity'],
        "currency_id" => "BRL",
        "unit_price" => $item['price']
    ];
    $items[] = $item;
    $total += $item['unit_price'] * $item['quantity'];
}

// SQL query to get the user data
$sqlUser = "SELECT * FROM llx_societe WHERE rowid = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $userid);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();
$stmtUser->close();

// SQL query to insert the order
$sqlOrder = "INSERT INTO llx_commande (ref, fk_soc, date_creation, date_commande, fk_user_author, fk_statut, total_ht, entity, delivery_address)
                 VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlOrder);
    $stmt->execute([
        $order_ref,
        $userid,
        1,
        0,
        $total,
        1,
        $selected_address
    ]);

$orderId = $stmt->insert_id;
$stmt->close();

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
    "success" => "https://" . $_SERVER['HTTP_HOST'] . "/success_register.php",
    "failure" => "https://test.com/failure",
    "pending" => "https://test.com/pending"
),
"notification_url" => "https://" . $_SERVER['HTTP_HOST'] . "/weebhook.php", // ✅ Webhook goes here
"external_reference" => $orderId,
"differential_pricing" => array(
    "id" => 1,
),
"expires" => false,
"items" => $items,
"payer" => array(
    "name" => $userData['nom'],
    "email" => $userData['email'],
    "phone" => array(
        "number" => $userData['phone'],
    ),
    "identification" => array(
        "type" => "CPF",
        "number" => $userData['idprof4'],
    ),
    "address" => array(
        "street_name" => $selected_address,
    )
),
    ]);

//echo json_encode($preference, JSON_PRETTY_PRINT);

$initPoint = $preference->init_point;

header("Location: {$initPoint}");
unset($_SESSION['cart'], $_SESSION['boughtCard']);
?>