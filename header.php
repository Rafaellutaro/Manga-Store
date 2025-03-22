<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    // User is logged in, set the profile link
    $profileLink = 'profile.php';

    if(isset($_SESSION['cart']) && $_SESSION['cart'] != null) {
        $cartLink = 'api_mercado_pago.php';
    }
        
} else {
    // User is not logged in, set the login link
    $profileLink = 'user.php';
    $cartLink = 'user.php';
    
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://kit.fontawesome.com/2502834e47.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script type="text/javascript" src="js/toast.js"></script>
</head>

<body>
    <section id="header">
        <a href="index.php"><img src="img/killua_logo.png" class="logo" alt="" height="100"></a>

        <input type="text" class="form-control" id="live-search" autocomplete="off" placeholder="Search">

        <div>
            <ul id="navbar">
                <li><a <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo ' class="active"'; ?> href="index.php">Home</a></li>
                <li><a <?php if(basename($_SERVER['PHP_SELF']) == 'shop.php') echo ' class="active"'; ?> href="shop.php">Loja</a></li>
                <li><a <?php if(basename($_SERVER['PHP_SELF']) == 'contact.php') echo ' class="active"'; ?> href="contact.php">Contato</a></li>
                <li><a <?php if(basename($_SERVER['PHP_SELF']) == 'user.php' || basename($_SERVER['PHP_SELF']) == 'profile.php') echo ' class="active"'; ?> href="<?php echo $profileLink; ?>"><i class="fa-regular fa-circle-user user"></i></a></li>
                <li><a <?php if(basename($_SERVER['PHP_SELF']) == 'cart.php') echo ' class="active"'; ?> href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
            </ul>
        </div>
    </section>
</body>

</html>