<?php

session_start();
require 'vendor/autoload.php';
include_once 'connection.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

MercadoPagoConfig::setAccessToken("TEST-833496997971285-051417-d8bdcea6415385b019f549ad1e7d720b-1201195997");

// 1. Get order ID
$orderId = $_SESSION['orderId'] ?? null;

if (!$orderId) {
    die("Pedido inválido.");
}

// 2. Fetch user info (based on order)
$sql = "SELECT s.nom, s.email, s.phone, s.idprof4, c.delivery_address
        FROM llx_commande c
        JOIN llx_societe s ON c.fk_soc = s.rowid
        WHERE c.rowid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

// 3. Fetch line items for the order
$sql = "SELECT p.rowid AS id, p.label, p.stock, f.filepath, f.filename, cd.qty, cd.subprice
        FROM llx_commandedet cd
        JOIN llx_product p ON cd.fk_product = p.rowid
        JOIN llx_ecm_files f ON p.rowid = f.src_object_id
        WHERE cd.fk_commande = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($item = $result->fetch_assoc()) {
    $stock = $item['stock'];

    if ($stock <= 0) {
        $_SESSION['error_message'] = "Produto {$item['label']} fora de estoque.";
        header("Location: index.php");
        exit;
    }
    
    $items[] = [
        "id" => $item['id'],
        "title" => $item['label'],
        "picture_url" => "https://" . $_SERVER['HTTP_HOST'] . "/img/{$item['filepath']}/{$item['filename']}",
        "quantity" => $item['qty'],
        "currency_id" => "BRL",
        "unit_price" => $item['subprice']
    ];
}
$stmt->close();

// 4. Create Mercado Pago preference
$client = new PreferenceClient();
$preference = $client->create([
    "back_urls" => [
        "success" => "https://" . $_SERVER['HTTP_HOST'] . "/manga/success_register.php",
        "failure" => "https://test.com/failure",
        "pending" => "https://test.com/pending"
    ],
    "notification_url" => "https://" . $_SERVER['HTTP_HOST'] . "/manga/weebhook.php",
    "external_reference" => $orderId,
    "expires" => false,
    "items" => $items,
    "payer" => [
        "name" => $userData['nom'],
        "email" => $userData['email'],
        "phone" => [
            "number" => $userData['phone']
        ],
        "identification" => [
            "type" => "CPF",
            "number" => $userData['idprof4']
        ],
        "address" => [
            "street_name" => $userData['delivery_address']
        ]
    ]
]);

$initPoint = $preference->init_point;
header("Location: {$initPoint}");
unset($_SESSION['orderId']);
exit;
?>
