<!-- 
    Logic
!-->

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

include_once "header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once "connection.php"; // Connect to your database

    $user_id = $_SESSION['user_id'];

    // Get user email from the database
    $stmt = $conn->prepare("SELECT email FROM llx_societe WHERE rowid = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if ($user_data) {
        $userEmail = $user_data['email'];
        $resetCode = bin2hex(random_bytes(5)); // Generate a random code

        // Store the reset code in the database (with expiration)
        $stmt = $conn->prepare("UPDATE llx_societe SET reset_code = ?, reset_expires_at = NOW() + INTERVAL 30 MINUTE WHERE rowid = ?");
        $stmt->bind_param("si", $resetCode, $user_id);
        $stmt->execute();

        // Send the password reset email
        sendPasswordResetEmail($userEmail, $resetCode);
    }
}

function sendPasswordResetEmail($userEmail, $resetCode)
{
    $mail = new PHPMailer(true);

    try {
        // Set up the SMTP server
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'rafaelzinhodostuto12@gmail.com'; // Your email address
        $mail->Password = 'egfovsmmyecnbchk'; // Your email password, it must be the app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
        $mail->Port = 587; // The SMTP port

        // Set email details
        $mail->setFrom('rafaelzinhodostuto12@gmail.com', '本屋くん'); // Sender info
        $mail->addAddress($userEmail); // Recipient

        // Email content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Pedido de troca de senha';
        $mail->Body    = '
                        <p>Olá,</p>
                        <p>Você solicitou uma troca de senha para a sua conta. Clique no link abaixo para iniciar o processo. Lembre-se, você tem 30 minutos até que o código expire.</p>
                        <p><a href="https://' . $_SERVER['HTTP_HOST'] . '/manga/reset_password.php?code=' . $resetCode . '">Trocar senha</a></p>
                        <p>Caso você não tenha feito essa solicitação, você pode ignorá-la. Caso ache que a sua conta esteja em perigo, entre em contato com o <a href="https://' . $_SERVER['HTTP_HOST'] . '/manga/contact.php">Suporte ao Cliente</a>.</p>
                        <p>Atenciosamente,</p>
                        <p>本屋くん</p>
                    ';

        $mail->send(); // Send the email--++-
        header("Location: email.php?status=success&useremail=" . $userEmail);
    } catch (Exception $e) {
        header("Location: email.php?status=error");
    }
}
?>

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

    if ($_GET['status'] === 'success') {
        echo "<p>Um email foi enviado para $useremail. Por favor verifique o seu inbox.</p>";
    } else {
        echo "<p>Houve um erro tentanto mandar o email. Tente novamente.</p>";
    }

    ?>

    <a href="profile.php">Volta para o seu perfil</a>

</body>

</html>