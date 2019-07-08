<?php
    session_start();
    include('classes.php');

    $_SESSION['er'] = null;
    if (isset($_POST['loginBtn'])){
        
        $user = $_POST['username'];
        $pwd = $_POST['password'];
        $pass = hash("Whirlpool", $pwd);

        $p->verify_login($user, $pass);
    }else{
        $_SESSION['er'] = "Something is wrong !!!";
        header('Location : ../login.php');
    }
?>