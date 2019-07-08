<?php
    try{
        $user = 'root';
        $pass = 'pass123';
        $dbname = "matcha";
        $conn = new PDO('mysql:host=localhost;dbname='.$dbname, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }catch(PDOException $ex){
        print_r($ex);
        die();
    }
?>