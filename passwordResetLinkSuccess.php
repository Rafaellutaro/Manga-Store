<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Email</title>
</head>

<body>

    <?php
    if (isset($_GET['useremail'])) {
        $useremail = $_GET['useremail'];
    }

    if (isset($_GET['status']) && $_GET['status'] === 'success') {
        echo "<p>Um email foi enviado para $useremail. Por favor verifique o seu inbox.</p>";
    } else {
        echo "<p>Houve um erro tentanto mandar o email. Tente novamente.</p>";
    }

    ?>

    <a href="profile.php">Volta para o seu perfil</a>

</body>

</html>