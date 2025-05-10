<?php
include_once 'connection.php';

function saveOrder($conn, $orderId, $cart) {
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
            0
        ]);
    }

    $stmtLine->close();
    return true;
}
