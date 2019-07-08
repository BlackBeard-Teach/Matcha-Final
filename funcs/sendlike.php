<?php
    session_start();
    if(isset($_POST['friend_id'])){
        $user_id = $_SESSION['id'];
        $friend_id = $_POST['friend_id'];
        include("classes.php");

        if($p->check_column($user_id,'pro_pic')){
            $p->send_like($friend_id, $user_id);
        } else {
            echo '<script>alert("Upload profile picture to like a friend!!!");</script>';
        }
    }
?>