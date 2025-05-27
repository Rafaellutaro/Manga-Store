<?php
include_once "connection.php";
include_once "product_box.php";

$productWidth = '250px';

// Extrai and limpa o produto ID do URL
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $url = mysqli_real_escape_string($conn, $url); // Limpa o input
} else {
    // Quando o produto não é encontrado
    echo $url;
    exit("Produto não encontrado");
}

// Query o database
$sql = "SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
        FROM llx_product
        JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
        WHERE llx_product.url = '$url'";


$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    // Quando o url não existe dentro de nenhum produto
    exit("Produto não encontrado");
}

$row = mysqli_fetch_assoc($result);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/product_box.css">
    <link rel="stylesheet" type="text/css" href="css/single_manga.css">
    <style>
        :root {
            --product-width: <?php echo $productWidth; ?>;
        }
    </style>
    <title>Produto</title>
</head>

<body>

    <?php
    include_once "header.php";
    // $img = "http://$dbhost/img/" . $row["filepath"] . "/" . $row["filename"];
    $img = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $row["filepath"] . "/" . $row["filename"];
    ?>

    <section id="single_details" class="section-p1">
        <div class="single_image">
            <img src="<?php echo $img ?>" width="100%" alt="">
        </div>

        <div class="details">
            <h2><?php echo $row["label"] ?></h2>
            <p id="stockQuantity" estoque=<?php echo $row["stock"] ?>>Quantidade em estoque: <?php echo $row["stock"] ?></p>
            <h2>R$ <?php echo number_format($row["price"], 2, ',', '.'); ?></h2>
            <input id="productQuantity" type="number" value="1" min="1" oninput="validar_box(this)">
            <button id="addToCartButton" data-product-id="<?php echo $row['rowid']; ?>" onclick="addtocart_singlepage()">Comprar</button>
        </div>
    </section>

    <section id="details_descrisao">
        <div class="descrisao">
            <?php echo $row["description"] ?>
        </div>
    </section>

    <section id="manga" class="section-p1">
        <h3>Produtos Relacionados</h3>

        <div class="manga_box">
            <?php

            $relatedSql = "SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
            FROM llx_product
            JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
            WHERE llx_product.rowid != {$row['rowid']}
            LIMIT 12";

            $relatedResult = $conn->query($relatedSql);

            while ($relatedRow = mysqli_fetch_assoc($relatedResult)) {
                $relatedUrl = $relatedRow["url"];
                // $relatedimg = "http://$dbhost/img/" . $relatedRow["filepath"] . "/" . $relatedRow["filename"];
                $relatedimg = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $relatedRow["filepath"] . "/" . $relatedRow["filename"];
                // Mostra produtos relacionados

            ?>

                <?php
                echo displayProduct($relatedUrl, $relatedimg, $relatedRow);
                ?>

            <?php
            }
            ?>
        </div>
    </section>
    <script src="js/add_carrinho.js"></script>
    <script src="js/single_add_carrinho.js"></script>
    <script src="js/formatting.js"></script>
    <script src="js/piscar.js"></script>

    <?php
    include_once "bottom.php";
    ?>
</body>

</html>