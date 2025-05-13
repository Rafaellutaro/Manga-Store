<?php
include_once "header.php";

if (isset($_GET["code"])) {
    $code = $_GET["code"];

    include_once "connection.php";

    $stmt = $conn->prepare("SELECT reset_expires_at FROM llx_societe WHERE reset_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $expire_date = new DateTime($row["reset_expires_at"], new DateTimeZone('UTC'));

        $now = new DateTime('now', new DateTimeZone('UTC'));

        if ($now < $expire_date) {
            $_SESSION["code"] = $code;
            $_SESSION["verify"] = "correct";
            
            header("Location: reset_password.php");
            exit;
        }else{
            $_SESSION["verify"] = "expired";
            header("Location: reset_password.php");
            exit;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/user.css">
    <title>Trocar Senha</title>
</head>

<body>

    <?php
    if(isset($_SESSION["code"])){
        $code = $_SESSION["code"];
        unset($_SESSION["code"]);
    }

    if (isset($_SESSION["errors"])) {
        $errors = $_SESSION["errors"];
        unset($_SESSION["errors"]);
    }

    if (isset($_SESSION["verify"])) {
        $verify = $_SESSION["verify"];
        unset($_SESSION["verify"]);
    }

    if ($verify == "correct") {
    ?>
        <section id="details">
            <form action="confirm_reset_password.php" method="POST">
                <input type="hidden" name="code" id="code" value="<?php echo htmlspecialchars($code); ?>">
                <div class="container">
                    <label for="Nova_senha">Nova Senha:</label>
                    <input type="password" id="nova_senha" required maxlength="20" minlength="6" name="nova_senha" required placeholder="Minimo de 6 digitos">

                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" required maxlength="20" minlength="6" name="confirmar_senha" required

                    <?php if (isset($errors['confirmar_senha'])) {
                    echo 'class="error-unique" placeholder="' . $errors['confirmar_senha'] . '"';
                    } ?>>

                    <div class="button_col">
                        <button type="submit" id="your-button">Alterar</button>
                    </div>
                </div>
            </form>
        </section>
    <?php
    } else if ($verify == "expired"){
         echo "<script>showToast('Seu código expirou', 'error');</script>";
         echo "Demorou demais, o código expirou. Você pode solicitar um novo código de troca de senha.";
         echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 10000);</script>";
    } else{
        echo "<script>showToast('Seu código é inválido', 'error');</script>";
        echo "Seu código é invalido. Você pode solicitar um novo código de troca de senha.";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 10000);</script>";
    }
    ?>

</body>

</html>