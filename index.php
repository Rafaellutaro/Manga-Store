<?php
include_once 'connection.php';
include_once 'header.php';
include_once "product_box.php";

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    echo "<script>showToast('$error_message', 'error');</script>";
    unset($_SESSION['error_message']);
}

$selectedMangaIDs = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; // Example manga IDs
$selectedMangaIDsString = implode(', ', $selectedMangaIDs);


$sql = "SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
        FROM llx_product
        JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
        WHERE llx_product.rowid IN ($selectedMangaIDsString)
        LIMIT 10";


$selectedMangas = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Livraria de Manga</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/product_box.css">
    <script src="https://kit.fontawesome.com/2502834e47.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>



    <section id="lua">
        <a href="shop.php" class="banner-to-shop">
            <h4>Killua Adverte</h4>
            <h1>Melhores Preços Nos</h1>
            <h1>Seus Mangás Favoritos</h1>
            <p>Venha comprar com a gente</p>
            <a href="shop.php" class="btn-shop">
                <button>Compre</button>
            </a>
        </a>
    </section>

    <section id="destaques" class="section-p1">
        <div class="box">
            <img src="img/destaques/f1.png" alt="">
            <h6>Frete Gratis</h6>
        </div>

        <div class="box">
            <img src="img/destaques/f2.png" alt="">
            <h6>Receba em Casa</h6>
        </div>

        <div class="box">
            <img src="img/destaques/f3.png" alt="">
            <h6>Economize</h6>
        </div>
        <div class="box">
            <img src="img/destaques/f4.png" alt="">
            <h6>Promoçôes</h6>
        </div>
        <div class="box">
            <img src="img/destaques/f5.png" alt="">
            <h6>Suporte 24h</h6>
        </div>
    </section>

    <section id="manga" class="section-p1">

        <h2>Mais Vendidos</h2>
        <p>Os melhores já feitos</p>

        <div class="manga_box">

            <?php
            while ($row = $selectedMangas->fetch_assoc()) {
                $productUrl = $row["url"];
                //$img = "http://$dbhost/img/" . $row["filepath"] . "/" . $row["filename"];
                $img = "https://" . $_SERVER['HTTP_HOST'] . "/img/" . $row["filepath"] . "/" . $row["filename"];
            ?>

                <?php
                echo displayProduct($productUrl, $img, $row);
                ?>

            <?php
            }
            ?>
        </div>

    </section>

    <script src="js/add_carrinho.js"></script>
    <script src="js/piscar.js"></script>

    <?php
    include_once "bottom.php";

    if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    echo "<script>showToast('$error_message', 'error');</script>";
    unset($_SESSION['error_message']);
}
    ?>
</body>

</html>