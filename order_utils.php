<?php
function saveOrder($conn, $userid, $cart) {
    $order_ref = 'WEBORDER-' . date('Ymd-His') . '-' . uniqid();

    $sqlOrder = "INSERT INTO llx_commande (ref, fk_soc, date_creation, date_commande, fk_user_author, fk_statut, total_ht, entity)
                 VALUES (?, ?, NOW(), NOW(), ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlOrder);
    $stmt->execute([
        $order_ref,
        $userid,
        1,
        1,
        0,
        1
    ]);

    $orderId = $stmt->insert_id;

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

    $stmt->close();
    $stmtLine->close();
    return true;
}
