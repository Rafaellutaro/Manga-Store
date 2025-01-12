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
            
            header("Location: reset_password.php?verify=correct");
            exit;
        }else{
            header("Location: reset_password.php?verify=expired");
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
    }

    if (isset($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }

    if (isset($_GET["verify"]) && $_GET["verify"] === "correct") {
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
    } else if (isset($_GET["verify"]) && $_GET["verify"] === "expired"){
        echo "Seu codigo expirou";
    } else{
        echo "Seu codigo é invalido";
    }
    ?>

</body>

</html>