<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



header('Content-Type: application/json');

include_once 'connection.php';

$query = $_GET['query'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("
    SELECT llx_product.label, llx_product.rowid, llx_product.url, llx_ecm_files.filepath, llx_ecm_files.filename
    FROM llx_product
    JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
    WHERE llx_product.label LIKE CONCAT('%', ?, '%')
    LIMIT 10
");
$stmt->bind_param("s", $query);
$stmt->execute();

$result = $stmt->get_result();

$results = [];

if ($result->num_rows < 1) {
    $results[] = [
        "label" => "Nenhum resultado encontrado",
    ];
} else {
    while ($row = $result->fetch_assoc()) {
        //$img = "http://$dbhost/img/" . $row["filepath"] . "/" . $row["filename"];
        $img = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $row["filepath"] . "/" . $row["filename"];
        $results[] = [
            "id" => $row['rowid'],
            "url" => $row['url'],
            "label" => $row['label'],
            "img" => $img
        ];
    }
}



echo json_encode($results);

$stmt->close();
$conn->close();
