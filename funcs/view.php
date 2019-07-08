<?php
    session_start();
    include('../config/db.php');
    include('classes.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $userid =  $_SESSION['id'];
    $id = $_GET['id'];
    $user_id = $_GET['sender'];
    $to = $_GET['to'];
    if (!empty($userid))
    {
        $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE notificationid = $id ");
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM notifications WHERE notificationid = $id AND thenotification != 'view'");
        $stmt->execute();

        $_SESSION['receiver_id'] = $user_id;
        if ($to == 'message')
            header("Location: ../messages.php");
        else
            header("Location: ../user_profile.php?id=$user_id");
    }
?>