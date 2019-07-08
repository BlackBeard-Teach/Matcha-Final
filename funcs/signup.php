<?php

    session_start();
    include('classes.php');

    if(isset($_POST['submit'])) {

        if(isset($_POST['loc']))
            $location = $_POST['loc'];
        else if(isset($_POST['location']))
            $location = $_POST['location'];

        $mail = $_POST['Email'];
        $username = $_POST['Username'];
        $pwd = $_POST['Password'];
        $cpwd = $_POST['cPassword'];
        $fname = $_POST['Lastname'];
        $lname = $_POST['Firstname'];

        $_SESSION['error'] = null;

        if(empty($mail) || empty($username) || empty($pwd) || empty($cpwd) || empty($fname) || empty($lname))
        {
            $_SESSION['error'] = "Field/s cannot be empty";
            header("Location: ../register.php");
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
        {
            $_SESSION['error'] = "Enter a valid Email";
            header("Location: ../register.php");
        }

        if (!preg_match("#[a-zA-Z]+#", $pwd))
        {
            $_SESSION['error'] = "Password shoud contain at least Lowercase, Uppercase";
            header("Location: ../register.php");
        }

        if (!preg_match("#[0-9]+#", $pwd))
        {
            $_SESSION['error'] = "Password shoud contain at least a digit ";
            header("Location: ../register.php");
        }

        if (strlen($pwd) < 4)
        {
            $_SESSION['error'] = "Password should contain at least 4 chars";
            header("Location: ../register.php");
        }

        if (strlen($username) < 4)
        {
            $_SESSION['error'] = "username should contain at least 4 chars";
            header('Location: ../register.php');
        }

        if (!preg_match("/^[a-zA-Z0-9]{4,}$/", $username))
        {
            $_SESSION['error'] = "Username should contain Lower/Upper case and should have atleast 3 chars";
            header('Location: ../register.php');
        }

        if ($pwd != $cpwd)
        {
            $_SESSION['error'] = "Passwords dont match try again";
            header('Location: ../register.php');
        }
        $url = $_SERVER['HTTP_HOST'] . str_replace("signup.php", "", $_SERVER['REQUEST_URI']);

        $p->verify_sign_up($mail, $username, $pwd, $url, $lname, $fname, $location);

    } else {
        $_SESSION['error'] = "What the fuck did you just try!!!";
    }
    header("Location: ../register.php");
?>