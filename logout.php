<?php
// Start or resume the user's session
session_start();


if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();
    exit;
} 
?>
