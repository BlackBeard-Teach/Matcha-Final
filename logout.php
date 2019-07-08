<?php
    session_start();
    include('config/db.php');
    include('funcs/classes.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    global $conn;
    $id = $_SESSION['id'];
    $sql = "UPDATE users SET last_seen = CURRENT_TIMESTAMP WHERE id = $id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    session_destroy();
    session_unset();
    header("Location: login.php");
?>