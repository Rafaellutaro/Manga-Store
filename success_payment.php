<?php
session_start();
include_once 'connection.php';

$paymentId = $_GET['payment_id'] ?? null;
$status = $_GET['status'] ?? null;

if ($paymentId && $status == 'approved') {

    $userid = $_SESSION['user_id'] ?? null;
    $cart = $_SESSION['boughtCard'] ?? [];

    if (empty($order_ref)) {
        $order_ref = 'WEBORDER-' . date('Ymd-His') . '-' . uniqid();
    }

    $sqlOrder = "INSERT INTO llx_commande (ref, fk_soc, date_creation, date_commande, fk_user_author, fk_statut, total_ht, entity)
             VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?)";

    $stmt = $conn->prepare($sqlOrder);

    $stmt->execute([
        $order_ref,
        $userid,
        1,             // fk_user_author (system or admin user)
        1,             // fk_statut: 1 = validated
        0,             // total_ht: 0 for now, will be updated later
        1              // entity = 1 for default
    ]);

    $orderId = $stmt->insert_id; // Get the last inserted order ID

    $sqlLine = "INSERT INTO llx_commandedet (fk_commande, fk_product, qty, subprice, total_ht, product_type)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmtLine = $conn->prepare($sqlLine);

    foreach ($cart as $item) {
        $qty = $item['quantity'];
        $unit_price = $item['unit_price'];
        $line_ht = $qty * $unit_price;

        $stmtLine->execute([
            $orderId,
            $item['id'],
            $qty,
            $unit_price,
            $line_ht,
            0 // product_type: 0 = product
        ]);
    }
    $stmt->close();
    $stmtLine->close();
    $conn->close();
    unset($_SESSION['cart']);
    unset($_SESSION['boughtCard']);
    unset($_SESSION['totalProd']);

    header("Location: success_register.php");
} else {
    header("Location: failed_register.php");
}
?>
