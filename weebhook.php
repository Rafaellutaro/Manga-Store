<?php
include_once 'connection.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use GuzzleHttp\Client;

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

                        // $sqlUpdateStock = "UPDATE llx_product SET stock = stock - ? WHERE rowid = ?";
                        // $stmtUpdate = $conn->prepare($sqlUpdateStock);
                        // $stmtUpdate->bind_param("ii", $quantityOrdered, $productId);
                        // $stmtUpdate->execute();
                        // $stmtUpdate->close();

                        $client = new Client([
                            'base_uri' => 'https://' . $_SERVER['HTTP_HOST'] . '/dolibarr/api/index.php/',
                            'headers' => [
                                'DOLAPIKEY' => 'tkNPRZGG75amObI2h84PG88xYp1gf95r',
                                'Content-Type' => 'application/json'
                            ],
                            'verify' => true  // Use false to skip SSL verification (not recommended)
                        ]);

                        try {
                            $response = $client->post('stockmovements', [
                                'json' => [
                                    'fk_product' => $productId,
                                    'fk_entrepot' => 1,
                                    'qty' => -$quantityOrdered,
                                    'label' => 'Venda de manga'
                                ]
                            ]);

                            $statusCode = $response->getStatusCode();
                            $body = $response->getBody()->getContents();

                            file_put_contents('mp_webhook_log.txt', "dolibarr API status: $statusCode" . PHP_EOL, FILE_APPEND);
                            file_put_contents('mp_webhook_log.txt', "dolibarr body: $body" . PHP_EOL, FILE_APPEND);
                        } catch (\GuzzleHttp\Exception\RequestException $e) {
                            file_put_contents('mp_webhook_log.txt', "dolibarr error:" . $e->getMessage() . PHP_EOL, FILE_APPEND);
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
