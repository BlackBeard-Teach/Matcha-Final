<?php
    session_start();
    include('classes.php');

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $p->activate($token);
    } else {
        $_SESSION['error'] = "Please register an account";
        header("Location: ../register.php");
    }
?>