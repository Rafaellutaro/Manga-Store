<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cep = trim($_POST['cep']);
    $local = trim($_POST['local']);

    // Basic validation example
    if (empty($cep)) {
        $errors['cep'] = 'CEP obrigatório';
    }
    if (empty($local)) {
        $errors['local'] = 'Local obrigatório';
    }

    // If no errors, update the DB
    if (empty($errors)) {
        include_once 'connection.php';

        //$stmt = $pdo->prepare("UPDATE users SET cep=?, rua=?, bairro=?, cidade=?, estado=?, local=? WHERE id=?");
        //$stmt->execute([$cep, $rua, $bairro, $cidade, $estado, $local, $userId]);

        echo "Endereço atualizado com sucesso!";
        exit;
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
</head>

<body>
    <?php
    include_once 'header.php';
    ?>
    <section id="details">
        <form action="">
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
                            <input type="text" class="rua_input_new" name="rua" readonly required>

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