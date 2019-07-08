<?php

    session_start();
    include('../config/db.php');
    include('classes.php');

    // Check if image file is a actual image or fake image
    if(isset($_POST["upload"])) {

        $target_dir = "../img/";
        $imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));
        $target_file = $target_dir.$_SESSION['username'].'.'.$imageFileType;
        $target_file1 = "img/".$_SESSION['username'].'.'.$imageFileType;

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"){
            $_SESSION['er'] = "Sorry, only JPG, JPEG, PNG files are allowed.";
        } else if ($_FILES["file"]["size"] > 512000) {
            $_SESSION['er'] =  "Sorry, your file is too large. 500kb is max";
        } else {
            $p->update_column($_SESSION['id'], "pro_pic", $target_file1);
            move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
        }
    }

    if (isset($_POST['status'])){
        $p->update_column($_SESSION['id'], "profile_status", $_POST['profile_status']);
    }

    if(isset($_POST['update'])){
        $columns = array("gender", "sexuality", "town", "age", "sport", "music", "movie");
        foreach ($columns as $column){
            $p->update_column($_SESSION['id'], $column, $_POST[$column]);
        }
    }
    header("Location: ../user_profile.php");
?>