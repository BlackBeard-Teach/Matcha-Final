<?PHP
    session_start();
    include('classes.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (isset($_POST['submit']) && !empty($_POST['msg'])){
        $sender_id = $_SESSION['id'];
        $receiver_id = $_SESSION['receiver_id'];
        $message = filter_input(INPUT_POST,'msg');
        $p->send_msg($sender_id, $receiver_id, $message);
        header('Location: ../messages.php');
    }else{
        header('Location: ../messages.php');
    }
?>