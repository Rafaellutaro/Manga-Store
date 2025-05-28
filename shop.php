<?php
include_once 'connection.php';
include_once "product_box.php";


$productsPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $productsPerPage;

// Get search query if any
$search = $_GET['search'] ?? '';
$search = trim($search);

if ($search !== '') {
    // Prepared statement with search filtering
    $likeSearch = "%{$search}%";

    // Get total filtered products count
    $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM llx_product WHERE label LIKE ?");
    $countStmt->bind_param("s", $likeSearch);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalProductsData = $countResult->fetch_assoc();
    $totalProducts = $totalProductsData['total'];
    $countStmt->close();

    // Get filtered products with join and pagination
    $stmt = $conn->prepare("SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
                            FROM llx_product
                            JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
                            WHERE llx_product.label LIKE ?
                            LIMIT ?, ?");
    $stmt->bind_param("sii", $likeSearch, $offset, $productsPerPage);
    $stmt->execute();
    $allproduct = $stmt->get_result();
    $stmt->close();

    // Check if any products were found
    if ($allproduct->num_rows < 1 && $totalProducts < 1) {
        echo "<script>
            alert('Nenhum produto encontrado para a pesquisa: " . htmlspecialchars($search, ENT_QUOTES) . "');
            window.location.href = 'index.php';
        </script>";
        exit();
    }
} else {
    // No search: get total products count
    $totalProductsQuery = "SELECT COUNT(*) AS total FROM llx_product";
    $totalProductsResult = $conn->query($totalProductsQuery);
    $totalProductsData = $totalProductsResult->fetch_assoc();
    $totalProducts = $totalProductsData['total'];

    // Get products without filtering
    $sql = "SELECT llx_product.*, llx_ecm_files.filepath, llx_ecm_files.filename
            FROM llx_product
            JOIN llx_ecm_files ON llx_product.rowid = llx_ecm_files.src_object_id
            LIMIT $offset, $productsPerPage";
    $allproduct = $conn->query($sql);
}

// $productWidth = '250px'; // Set the desired width

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/product_box.css">
    <script src="https://kit.fontawesome.com/2502834e47.js"></script>
    <!-- <style>
        /* Override the CSS variables with PHP values */
        :root {
            --product-width: <?php echo $productWidth; ?>;
        }
    </style> -->
    <title>Loja de mangas</title>
</head>

<body>
    <?php
    include_once 'header.php';
    ?>

    <section id="shop_lua">
        <!-- <b>
            <h1>漫画の価値</h1><b>
                <b>
                    <p>O valor dos mangas</p><b> -->
    </section>

    <section id="manga" class="section-p1 shop-page">

        <div class="manga_box">
            <?php
            while ($row = mysqli_fetch_assoc($allproduct)) {
                $productUrl = $row["url"];
                // $img = "http://$dbhost/img/" . $row["filepath"] . "/" . $row["filename"];
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

    <section id="pagination" class="section-p1">
        <?php
        // Calculate the total number of pages
        $totalPages = ceil($totalProducts / $productsPerPage);

        // Previous Page Arrow
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "'><i class='fa-solid fa-left-long'></i></a>";
        }

        // Generate pagination links for a limited number of pages
        $maxPagesToShow = 2; // Adjust this number as needed
        $startPage = max(1, $page - floor($maxPagesToShow / 2));
        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $page) {
                echo "<span class='current-page'>$i</span>";
            } else {
                echo "<a href='?page=$i'>$i</a>";
            }
        }

        // Next Page Arrow
        if ($page < $totalPages) {
            echo "<a href='?page=" . ($page + 1) . "'><i class='fa-solid fa-right-long'></i></a>";
        }

        ?>



    </section>

    <?php
    include_once "bottom.php";
    ?>

    <script src="js/add_carrinho.js"></script>
    <script src="js/piscar.js"></script>
    <script src="js/imageShowcase.js"></script>
</body>

</html>