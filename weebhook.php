<?php
include_once 'connection.php';
include_once 'order_utils.php';

$rawInput = file_get_contents('php://input');

$data = json_decode($rawInput, true);

file_put_contents('mp_webhook_log.txt', date('Y-m-d H:i:s') . " - RAW: " . $rawInput . PHP_EOL, FILE_APPEND);

if (isset($data['type']) && $data['type'] === 'payment' && isset($data['data']['id'])) {
    $paymentId = $data['data']['id'];

    $accessToken = 'TEST-7044352387989428-022013-88e564687f1086f98eef38226c079b2a-1201195997'; // Replace with real token
    $ch = curl_init("https://api.mercadopago.com/v1/payments/$paymentId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $paymentData = json_decode($response, true);

    file_put_contents('mp_webhook_log.txt', date('Y-m-d H:i:s') . " - PAYMENT INFO: " . $response . PHP_EOL, FILE_APPEND);

    if ($paymentData['status'] === 'approved') {
        $externalReference = $paymentData['external_reference'] ?? null;
        
        if($externalReference) {
            $orderId = $externalReference;
            $sql = "UPDATE llx_commande SET fk_statut = 1 WHERE rowid = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$orderId]);
            $stmt->close();

            unset($_SESSION['cart'], $_SESSION['boughtCard'], $_SESSION['totalProd']);
        }
    }
}

http_response_code(200);
echo "OK";
?>
