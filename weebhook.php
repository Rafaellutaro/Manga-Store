<?php
session_start();
include_once 'connection.php';
include_once 'order_utils.php';
require_once 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;


$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Log the raw webhook for debugging
file_put_contents('mp_webhook_log.txt', date('Y-m-d H:i:s') . " - RAW: " . $rawInput . PHP_EOL, FILE_APPEND);

if (isset($data['type']) && $data['type'] === 'payment' && isset($data['data']['id'])) {
    $paymentId = $data['data']['id'];

    // Set your Mercado Pago access token
    MercadoPagoConfig::setAccessToken("TEST-7044352387989428-022013-88e564687f1086f98eef38226c079b2a-1201195997");

    $paymentClient = new PaymentClient();

    try {
        // Get full payment info from Mercado Pago
        file_put_contents('mp_webhook_log.txt', date('Y-m-d H:i:s') . " - PAYMENT Client $paymentClient: " . json_encode($paymentClient) . PHP_EOL, FILE_APPEND);
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
                $sqlDetails = "SELECT fk_product, qty FROM llx_commandedet WHERE fk_commande = ?";
                $stmtDetails = $conn->prepare($sqlDetails);
                $stmtDetails->bind_param("i", $externalReference);
                $stmtDetails->execute();
                $result = $stmtDetails->get_result();

                while ($row = $result->fetch_assoc()) {
                    $productId = $row['fk_product'];
                    $quantityOrdered = $row['qty'];

                    if ($productId) {
                        // Update product stock
                        file_put_contents('mp_webhook_log.txt', "Updating stock for product $productId - qty $quantityOrdered" . PHP_EOL, FILE_APPEND);

                        $sqlUpdateStock = "UPDATE llx_product SET stock = stock - ? WHERE rowid = ?";
                        $stmtUpdate = $conn->prepare($sqlUpdateStock);
                        $stmtUpdate->bind_param("ii", $quantityOrdered, $productId);
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
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
