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
                // Start transaction for all the following operations
                $conn->begin_transaction();

                // Lock the order row to check status safely
                $sqlCheck = "SELECT fk_statut FROM llx_commande WHERE rowid = ? FOR UPDATE";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bind_param("i", $externalReference);
                $stmtCheck->execute();
                $stmtCheck->bind_result($status);
                $stmtCheck->fetch();
                $stmtCheck->close();

                if ($status == 1) {
                    // Order already processed, nothing to do
                    $conn->commit();
                    http_response_code(200);
                    exit;
                }

                // Update order status to processed
                $sqlUpdateStatus = "UPDATE llx_commande SET fk_statut = 1 WHERE rowid = ?";
                $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
                $stmtUpdateStatus->bind_param("i", $externalReference);
                $stmtUpdateStatus->execute();
                $stmtUpdateStatus->close();

                // Fetch order details for stock update
                $sqlDetails = "SELECT fk_product, qty, label FROM llx_commandedet WHERE fk_commande = ?";
                $stmtDetails = $conn->prepare($sqlDetails);
                $stmtDetails->bind_param("i", $externalReference);
                $stmtDetails->execute();
                $result = $stmtDetails->get_result();

                $fk_entrepot = 1; // Your warehouse ID
                $fk_user_author = 1; // Your user ID who makes the change

                while ($row = $result->fetch_assoc()) {
                    $productId = $row['fk_product'];
                    $quantityOrdered = $row['qty'];
                    $title = $row['label'];

                    if ($productId && $quantityOrdered > 0) {
                        $inventoryCode = date('Ymd') . sprintf('%06d', mt_rand(0, 999999));
                        $label = "Da correção para o produto $title";

                        // Insert stock movement (negative quantity)
                        $sqlInsertMovement = "INSERT INTO llx_stock_mouvement 
                            (tms, datem, fk_product, fk_entrepot, value, inventorycode, fk_user_author, label, type_mouvement, fk_origin, fk_projet) 
                            VALUES (NOW(), NOW(), ?, ?, ?, ?, ?, ?, 0, 0, 0)";
                        $stmtMovement = $conn->prepare($sqlInsertMovement);
                        $negativeQuantity = -$quantityOrdered;
                        $stmtMovement->bind_param("iiisis", $productId, $fk_entrepot, $negativeQuantity, $inventoryCode, $fk_user_author, $label);
                        $stmtMovement->execute();
                        $stmtMovement->close();

                        // Update warehouse stock
                        $sqlUpdateWarehouse = "UPDATE llx_product_stock SET reel = reel - ? WHERE fk_product = ? AND fk_entrepot = ?";
                        $stmtWarehouse = $conn->prepare($sqlUpdateWarehouse);
                        $stmtWarehouse->bind_param("iii", $quantityOrdered, $productId, $fk_entrepot);
                        $stmtWarehouse->execute();
                        $stmtWarehouse->close();

                        // Update global product stock
                        $sqlUpdateGlobal = "UPDATE llx_product SET stock = (SELECT IFNULL(SUM(reel), 0) FROM llx_product_stock WHERE fk_product = ?) WHERE rowid = ?";
                        $stmtGlobal = $conn->prepare($sqlUpdateGlobal);
                        $stmtGlobal->bind_param("ii", $productId, $productId);
                        $stmtGlobal->execute();
                        $stmtGlobal->close();
                    }
                }

                $stmtDetails->close();

                // Commit transaction after all stock updates
                $conn->commit();

                http_response_code(200);
                exit;
            } else {
                file_put_contents('mp_webhook_log.txt', "Missing externalReference." . PHP_EOL, FILE_APPEND);
            }
        }
    } catch (Exception $e) {
        $conn->rollback();
        file_put_contents('mp_webhook_log.txt', "Exception: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        http_response_code(500);
        exit;
    }
}
?>
