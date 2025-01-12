<!DOCTYPE html>
<html>

<head>
    <title>Registro</title>
    <link rel="stylesheet" type="text/css" href="css/user.css">
    <script src="https://kit.fontawesome.com/2502834e47.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <?php
    include_once 'header.php';
    ?>


    <section id="details">
        <form action="login.php" method="POST">
            <div class="container">
                <label for="email">Email:</label>
                <input type="text" id="email" required maxlength="50" name="email" required>

                <label for="senha" class="senha">Senha:</label>
                <input type="password" id="senha" required maxlength="20" name="senha" required>

                <div class="button_col">
                    <button id="your-button">Logar</button>
                </div>

                <p>Não tem conta? <a href="user_register.php">Registre</a></p>
            </div>
        </form>
    </section>
</body>

</html>