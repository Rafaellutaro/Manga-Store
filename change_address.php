<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    // endereço
    $rua = $_POST['rua'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $local = $_POST['local'];
    $cep = $_POST['cep'];
    $numero_casa = $_POST['n_casa'];

    $user_id = $_SESSION['user_id'];

    $endereco_completo = $rua . ", " . $numero_casa . " - " . $bairro;

    if (empty($cep)) {
        $errors['cep'] = 'CEP obrigatório';
    }
    if (empty($local)) {
        $errors['local'] = 'Local obrigatório';
    }

    // If no errors, update the DB
    if (count($errors) === 0) {
        include_once 'connection.php';

        $currentQuery = $conn->prepare("SELECT address, zip, town, siren, fax FROM llx_societe WHERE rowid = ?");
        $currentQuery->bind_param("i", $user_id);
        $currentQuery->execute();
        $currentQuery->store_result();
        $currentQuery->bind_result($current_address, $current_cep, $current_cidade, $current_estado, $current_local);
        $currentQuery->fetch();

        if (
            $endereco_completo === $current_address &&
            $cep === $current_cep &&
            $cidade === $current_cidade &&
            $estado === $current_estado &&
            $local === $current_local
        ) {
            $errors['cep'] = 'Endereço igual ao atual';
        } else {
            $address_query = ("UPDATE llx_societe SET address = ?, zip = ?, town = ?, siren = ?, fax = ? tms = NOW() WHERE rowid = ?");
            $stmt_endereco = $conn->prepare($address_query);
            $stmt_endereco->bind_param("sssssi", $endereco_completo, $cep, $cidade, $estado, $local, $user_id);

            if ($stmt_endereco->execute()) {
                header("location: success_register.php");
                $conn->close();
            } else {
                header("location: failed_register.php");
                $conn->close();
            }

            $stmt_endereco->close();
        }
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mudar endereço</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/user_register.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
    <style>
        /* Override the CSS variables with PHP values */
        :root {
            --margin: 30% auto;

            html,
            body {
                overflow: hidden;
            }
        }
    </style>
</head>

<body>
    <?php
    include_once 'header.php';
    ?>
    <section id="details">
        <form method="POST">
            <div class="main-box">
                <div class="container">
                    <h2>Insira o seu Novo Endereço</h2>
                    <div class="conta">
                        <div id="address-container">

                            <label for="cep" class="cep">CEP:</label>

                            <input type="text" id="cep" oninput="validar_cep(this)" name="cep" required <?php if (isset($errors['cep'])) {
                                                                                                            echo 'class="error-unique" placeholder="' . $errors['cep'] . '"';
                                                                                                        } else {
                                                                                                            echo 'placeholder="Ex: 99999-999"';
                                                                                                        } ?>>
                            <label for="rua" class="rua">Rua:</label>
                            <input type="text" class="rua_input" name="rua" readonly required>

                            <label for="bairro" class="bairro_cidade">Bairro:</label>

                            <label for="cidade" class="bairro_cidade">Cidade:</label>

                            <input type="text" name="bairro" class="no_click" readonly required>

                            <input type="text" name="cidade" class="no_click" readonly required>

                            <label for="estado" class="estado">Estado:</label>

                            <label for="local" class="local">Local:</label>

                            <input type="text" name="estado" class="no_click " readonly required>

                            <input type="text" class="local" id="local" name="local" required maxlength="40" required
                                <?php if (isset($errors['local'])) {
                                    echo 'class="error-unique" placeholder="' . $errors['local'] . '"';
                                } else {
                                    echo 'placeholder="Ex: Casa, Empresa"';
                                } ?>>

                            <label for="n_casa" class="n_casa">Número da casa:</label>
                            <input type="text" name="n_casa" id="n_casa" required>
                        </div>
                        <button type="submit">Concluir</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <script src="js/formatting_cep.js"></script>
    <script src="js/endereco_api.js"></script>
</body>

</html>