<?php
function displayProduct($productUrl, $img, $row) {
    ob_start(); // Start output buffering
    ?>
    <div class="manga_img">
        <a href="sproduct.php?url=<?php echo $productUrl ?>">
            <img src="<?php echo $img ?>" alt="">
        </a>
        <div class="manga_descrisao">
            <span>Mangá</span>
            <h5><?php echo $row["label"] ?></h5>
            <div class="star">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <h4>R$<?php echo number_format($row["price"], 2, ',', '.'); ?></h4>
        </div>
        <a class="addToCart" data-product-id="<?php echo $row['rowid']; ?>">
            <i class="fa-solid fa-cart-shopping manga_carrinho"></i>
        </a>
    </div>
    <?php
    return ob_get_clean(); // Return the buffered output
}
?>
