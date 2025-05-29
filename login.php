<?php
// Include the necessary connection file
include_once 'connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepare and execute the SQL query to fetch the user by email
    $stmt = $conn->prepare("SELECT * FROM llx_societe WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($senha, $user['idprof6'])) {
            // Password is correct, set session variables
            $_SESSION['loggedIn'] = true;
            $_SESSION['user_id'] = $user['rowid'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to the home page or a logged-in area
            header('Location: profile.php');
            exit();
        } else {
            // Incorrect password
            echo "<script>showToast('Senha incorreta', 'error');</script>";
        }
    } else {
        // User not found
        echo "<script>showToast('Usuario não existe', 'error');</script>";
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}
?>
