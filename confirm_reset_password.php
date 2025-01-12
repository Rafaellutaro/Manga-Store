<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "header.php";

$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only retrieve these values if the form was submitted

    $password = $_POST['nova_senha'];
    $confirm_password = $_POST['confirmar_senha'];
    $code = $_POST["code"];

    if ($password != $confirm_password) {
        $errors['confirmar_senha'] = "As duas senhas não são iguais";
    }

    if (count($errors) === 0) {
        include_once "connection.php";

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE llx_societe SET idprof6 = ? WHERE reset_code = ?");
        $stmt->bind_param("ss", $hashed_password, $code);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: confirm_reset_password.php?confirm=correct");
            exit; // Important to exit after redirect
        } else {
            echo "Houve problemas em alterar a sua senha";
        }
    } else {
        $_SESSION["errors"] = $errors;
        header("Location: reset_password.php?code=" . urlencode($code));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Senha</title>
</head>

<body>
    <?php
    if (isset($_GET["confirm"]) && $_GET["confirm"] === "correct") {
        echo "<p>Sua senha foi alterada com sucesso</p>";
    }
    ?>
</body>

</html>