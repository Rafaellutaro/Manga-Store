<?php
include_once "header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $insertedCode = $_POST['code'];
    $correctCode = $_SESSION['registerCode'];

    if ($insertedCode == $correctCode) {
        include_once "connection.php";

        $client = 1;
        $fk_user = 1;

        $nome_completo = $_SESSION['userData']['nome_completo'];
        $cpf = $_SESSION['userData']['cpf'];
        $formatted_nascimento = $_SESSION['userData']['nascimento'];
        $email = $_SESSION['userData']['email'];
        $hashedPassword = $_SESSION['userData']['senha'];
        $celular = $_SESSION['userData']['celular'];
        $newUserCode = $_SESSION['userData']['newUserCode'];
        $data_atual = date("Y-m-d H:i:s");

        $sql = "INSERT INTO llx_societe (nom, idprof4, idprof5, email, idprof6, phone, client, datec, fk_user_creat, fk_user_modif, code_client) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ssssssisiis", $nome_completo, $cpf, $formatted_nascimento, $email, $hashedPassword, $celular, $client, $data_atual, $fk_user, $fk_user, $newUserCode);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();

            $rua = $_SESSION['userData']['rua'];
            $bairro = $_SESSION['userData']['bairro'];
            $numero_casa = $_SESSION['userData']['numero_casa'];

            $cidade = $_SESSION['userData']['cidade'];
            $estado = $_SESSION['userData']['estado'];
            $cep = $_SESSION['userData']['cep'];
            $local = $_SESSION['userData']['local'];

            $endereco_completo = $rua . ", " . $numero_casa . " - " . $bairro;

            $endereco_query = "UPDATE llx_societe SET address = ?, zip = ?, town = ?, siren = ?, fax = ? WHERE rowid = ?";
            $stmt_endereco = $conn->prepare($endereco_query);
            $stmt_endereco->bind_param("sssssi", $endereco_completo, $cep, $cidade, $estado, $local, $user_id);

            if ($stmt_endereco->execute()) {
                $stmt_endereco->close();
                session_unset();
                session_destroy();
                header("location: success_register.php");
                exit();
            } else {
                echo "Erro ao inserir o endereço: " . $stmt_endereco->error;
            }
        }
    } else {
        $insertedCode = "O codigo inserido está incorreto. ";
    }
} else {
    $insertedCode = 'O codigo inserido é invalido.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/registerCode.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar codigo de registro</title>
</head>

<body>

    <section id="details">
        <form method="POST">
            <div class="container">
                <h2>Validar Email</h2>
                <h4>Um código de verificação foi enviado para o seu email.</h4>
                <h3>Por favor, insira o código abaixo para completar o registro.</h3>
                <label for="code">Codigo:</label>
                <input type="text" id="code" required maxlength="10" name="code" placeholder="<?= $insertedCode ?>">

                <div class="button_col">
                    <button id="your-button">Validar</button>
                </div>
            </div>
        </form>
    </section>

</body>

</html>