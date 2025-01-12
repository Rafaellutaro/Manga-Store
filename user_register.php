<!DOCTYPE html>
<html>

<head>
    <title>Registro</title>
    <link rel="stylesheet" type="text/css" href="css/user_register.css">
    <script src="https://kit.fontawesome.com/2502834e47.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

</head>

<body>
    <?php
    include_once 'header.php';

    if (isset($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']); // limpa os erros da sessão
    }
    ?>


    <section id="details">
        <form action="register_validation.php" method="POST">
            <div class="container">
                <div class="acesso">
                    <h4>Informações de acesso</h4>

                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" required maxlength="50" required
                    <?php if (isset($errors['email'])) {
                    echo 'class="error-unique" placeholder="' . $errors['email'] . '"';
                    } else {
                        echo 'placeholder="Ex: exemplo@email.com.br"';
                        } ?>>
                     <span id="email-error" class="error"></span>
                    

                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required maxlength="20" minlength="6" required
                    placeholder="Minimo de 6 caracteres">
                </div>

                <div class="conta">
                    <h3>Detalhes da conta</h3>

                    <label for="nome">Nome:</label>
                    <label for="sobrenome">Sobrenome:</label>
                    <input type="text" id="nome" name="nome" required maxlength="15" minlength="4" required 
                    <?php if (isset($errors['nome'])) {
                    echo 'class="error-unique" placeholder="' . $errors['nome'] . '"';
                    } ?>>
                    
                    <input type="text" id="sobrenome" name="sobrenome" required maxlength="40" minlength="7" required 
                    <?php if (isset($errors['sobrenome'])) {
                    echo 'class="error-unique" placeholder="' . $errors['sobrenome'] . '"';
                    } ?>>
                    

                    <label for="cpf" class="cpf">CPF:</label>
                    <label for="celular" class="cel">Celular:</label>

                    <input type="text" id="cpf" name="cpf" oninput="validar_cpf(this)" required <?php if (isset($errors['cpf'])) {
                    echo 'class="error-unique" placeholder="' . $errors['cpf'] . '"';
                    } else {
                        echo 'placeholder="Ex: 999.999.999-99"';
                        } ?>>

                    <input type="text" id="cel" name="celular" oninput="validar_cel(this)" required
                    <?php if (isset($errors['celular'])) {
                    echo 'class="error-unique" placeholder="' . $errors['celular'] . '"';
                    } else {
                        echo 'placeholder="Ex: (99) 99999-9999"';
                        } ?>>

                    <label for="nascimento" class="data">Data de Nascimento:</label>
                    <input type="text" id="nascimento" name="nascimento" oninput="validar_nascimento(this)" required <?php if (isset($errors['nascimento'])) {
                    echo 'class="error-unique" placeholder="' . $errors['nascimento'] . '"';
                    } else {
                        echo 'placeholder="Ex: 99/99/9999"';
                        } ?>>

                    <h3>Endereço de entrega</h3>

                    <label for="cep" class="cep">CEP:</label>

                    <input type="text" id="cep" oninput="validar_cep(this)" name="cep" required <?php if (isset($errors['cep'])) {
                    echo 'class="error-unique" placeholder="' . $errors['cep'] . '"';
                    } else {
                        echo 'placeholder="Ex: 99999-999"';
                        } ?>>
                    
                    <div id="address-container">
                        <label for="rua" class="rua">Rua:</label>
                        <input type="text" class="rua_input" name="rua" readonly required>

                        <label for="bairro" class="bairro_cidade">Bairro:</label>

                        <label for="cidade" class="bairro_cidade">Cidade:</label>
                        
                        <input type="text"  name="bairro" class="no_click" readonly required>

                        <input type="text"  name="cidade" class="no_click" readonly required>

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

                    <label for="n_casa" class="n_casa">Número da casa:</label>

                    <input type="text" name="n_casa" id="n_casa" required>

                    <button type="submit">Concluir</button>
                </div>
            </div>
        </form>
    </section>

    <script src="js/formatting.js"></script>
    <script src="js/endereco_api.js"></script>
</body>

</html>