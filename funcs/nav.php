<?PHP
    session_start();
    include('classes.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $p->notif_counter();
?>