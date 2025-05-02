<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/contact.css">
    <title>Contato</title>

</head>

<body>
    <?php
    include_once 'header.php';
    ?>
    <div class="contact_container">
        <form action="https://api.web3forms.com/submit" method="POST" id="contact">
            <div class="contact_form">
                <div class="contact_title">
                    <h2>Contate-nos</h2>
                    <!-- <hr> -->
                </div>
                <div class="all_inputs">
                    <input type="hidden" name="access_key" value="80938d9c-9b6c-47f2-bc36-bd6ea0decfcb">
                    
                    <span class="label">Nome</span>
                    <input type="text" name="name" id="name" placeholder="Digite seu nome:" required class="input">

                    <span class="label">E-mail</span>
                    <input type="text" name="email" placeholder="Digite seu e-mail:" required class="input">

                    <span class="label">Assunto</span>
                    <input type="text" class="input" placeholder="Assunto:" name="subject" />

                    <input type="hidden" name="from_name" id="from_name" value="Mission Control">

                    <span class="label">Mensagem</span>
                    <textarea name="message" placeholder="Digite sua Mensagem:" required class="input"></textarea>

                    <input type="checkbox" name="botcheck" class="hidden" style="display: none;">

                    <div class="h-captcha" data-captcha="true"></div>

                    <button type="submit">Enviar</button>
                </div>
            </div>
        </form>
    </div>

    <?php
    include_once "bottom.php";

    if (isset($_SESSION['error_message'])) {
        echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
        unset($_SESSION['error_message']);
    }
    ?>
    
    <script src="/js/piscar.js"></script>
    <script src="https://web3forms.com/client/script.js" async defer></script>
</body>

</html>