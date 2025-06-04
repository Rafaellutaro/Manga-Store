<?php
include_once 'connection.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

require_once 'vendor/autoload.php';

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Log the raw webhook for debugging
file_put_contents('mp_webhook_log.txt', date('Y-m-d H:i:s') . " - RAW: " . $rawInput . PHP_EOL, FILE_APPEND);

if (isset($data['type']) && $data['type'] === 'payment' && isset($data['data']['id'])) {
    $paymentId = $data['data']['id'];

    // Set your Mercado Pago access token
    MercadoPagoConfig::setAccessToken("TEST-833496997971285-051417-d8bdcea6415385b019f549ad1e7d720b-1201195997");

    $paymentClient = new PaymentClient();

    try {
        // Get full payment info from Mercado Pago
        $payment = $paymentClient->get($paymentId);

        // Log the payment info
        file_put_contents('mp_webhook_log.txt', date('Y-m-d H:i:s') . " - PAYMENT ID $paymentId: " . json_encode($payment) . PHP_EOL, FILE_APPEND);

        if ($payment->status === 'approved') {

            $externalReference = $payment->external_reference ?? null;

            if ($externalReference) {
                // Update order status and delivery address
                $sql = "UPDATE llx_commande SET fk_statut = 1 WHERE rowid = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $externalReference);
                $stmt->execute();
                $stmt->close();

                // Fetch order details to update stock
                $sqlDetails = "SELECT fk_product, qty, label FROM llx_commandedet WHERE fk_commande = ?";
                $stmtDetails = $conn->prepare($sqlDetails);
                $stmtDetails->bind_param("i", $externalReference);
                $stmtDetails->execute();
                $result = $stmtDetails->get_result();

                while ($row = $result->fetch_assoc()) {
                    $productId = $row['fk_product'];
                    $quantityOrdered = $row['qty'];
                    $title = $row['label'];

                    if ($productId) {


                        // $sqlUpdateStock = "UPDATE llx_product SET stock = stock - ? WHERE rowid = ?";
                        // $stmtUpdate = $conn->prepare($sqlUpdateStock);
                        // $stmtUpdate->bind_param("ii", $quantityOrdered, $productId);
                        // $stmtUpdate->execute();
                        // $stmtUpdate->close();

                        // New insert

                        try{
                        // $conn->begin_transaction();
                        $inventoryCode = date('Ymd') . sprintf('%06d', mt_rand(0, 999999));
                        $label = "Da correção para o produto $title";
                        
                        $fk_entrepot = 1;
                        $fk_user_author = 1;

                        // 1. Insert into stock_mouvement
                        $sqlInsertMovement = "INSERT INTO llx_stock_mouvement 
                        (tms, datem, fk_product, fk_entrepot, value, inventorycode, fk_user_author, label, type_mouvement, fk_origin, fk_projet) 
                        VALUES (NOW(), NOW(), ?, ?, ?, ?, ?, ?, 0, 0, 0)";
                        $stmtMovement = $conn->prepare($sqlInsertMovement);
                        $negativeQuantity = -$quantityOrdered;
                        file_put_contents('mp_webhook_log.txt', "Attempting stock movement insert for product ID: $productId, quantity: $negativeQuantity, inventoryCode: $inventoryCode, label: $label, fk_entrepot: $fk_entrepot, fk_user_author: $fk_user_author" . PHP_EOL, FILE_APPEND);
                        $stmtMovement->bind_param("iiisis", $productId, $fk_entrepot, $negativeQuantity, $inventoryCode , $fk_user_author, $label);
                        $stmtMovement->execute();
                        if ($stmtMovement->affected_rows <= 0){
                            file_put_contents('mp_webhook_log.txt', "ERROR: " . $stmtMovement->error . PHP_EOL, FILE_APPEND);
                        }
                        $stmtMovement->close();

                        // // 2. Update warehouse stoc
                        // $sqlUpdateWarehouse = "UPDATE llx_product_stock SET reel = reel - ? WHERE fk_product = ? AND fk_entrepot = ?";
                        // $stmtWarehouse = $conn->prepare($sqlUpdateWarehouse);
                        // $stmtWarehouse->bind_param("iii", $quantityOrdered, $productId, 1);
                        // $stmtWarehouse->execute();
                        // $stmtWarehouse->close();

                        // // 3. Update global product stock
                        // $sqlUpdateGlobal = "UPDATE llx_product SET stock = (SELECT IFNULL(SUM(reel), 0) FROM llx_product_stock WHERE fk_product = ?) WHERE rowid = ?";
                        // $stmtGlobal = $conn->prepare($sqlUpdateGlobal);
                        // $stmtGlobal->bind_param("ii", $productId, $productId);
                        // $stmtGlobal->execute();
                        // $stmtGlobal->close();

                        // $conn->commit();

                        file_put_contents('mp_webhook_log.txt', "query Complete" . PHP_EOL, FILE_APPEND);
                        }catch (Exception $e) {
                            $conn->rollback();
                            file_put_contents('mp_webhook_log.txt', "query error: ". $e->getMessage() . PHP_EOL, FILE_APPEND);
                        }
                        

                        
                    }
                }

                $stmtDetails->close();
            } else {
                file_put_contents('mp_webhook_log.txt', "Missing externalReference or address." . PHP_EOL, FILE_APPEND);
            }
        }
    } catch (Exception $e) {
        file_put_contents('mp_webhook_log.txt', "Exception: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
}

http_response_code(200);
echo "OK";
