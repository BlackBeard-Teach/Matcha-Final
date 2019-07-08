<?php
session_start();
include('config/db.php');
include('funcs/classes.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['id'])){
    header("Location: login.php");
}else{
	$user_id = $_SESSION['id'];
	$_SESSION['request'] = $p->get_outgoing_likes($user_id);
	$_SESSION['incoming'] = $p->get_incoming_likes($user_id, 1);
	$_SESSION['friends'] = $p->friends_ids($user_id);
    $_SESSION['blocked'] = $p->get_blocked_ids($user_id);
}
?>
<html>
    <head>

        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="img/favicon.ico">

		<title>Messages</title>

        <!-- Custom styles for this search -->
        <link href="css/message.css" rel="stylesheet">

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet">
        
        <!-- Custom styles for this search -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <style>
            body
            {
                font-family: 'Monoton', cursive;
            }

        </style>

    </head>
    <body>

    <div class="container">
        <div class="panel-body" id="main_page">
            <nav class="navbar navbar-default navbar-expand-lg" id="nav_load">	
				<?php $p->nav(); ?>
			</nav>
            <div class="messaging">
                <div class="inbox_msg">
                    <div class="inbox_people">
                        <div class="headind_srch">
                            <div class="recent_heading">
                                <h4><i class="fa fa-commenting"></i>Messages</h4>
                            </div>
                        </div>

                        <?php $friends_details = $p->friends_details($_SESSION['friends']);?>
                        <div class="inbox_chat">
                            <?php
                                $count = 0;
                                if($friends_details[0] != 0){
                                    foreach ($friends_details as $details){
                                        if ($count == 0 && !isset($_SESSION['receiver_id'])) {
                                            $_SESSION['receiver_id'] = $details['id'];
                                            echo "<div class='chat_list active_chat' id='active' title='" . $details['id'] . "' onclick='switch_on(this.title,this.id)'>";
                                        } else if ($_SESSION['receiver_id'] != null && $details['id'] == $_SESSION['receiver_id']) {
                                            echo "<div class='chat_list active_chat' id='active' title='" . $details['id'] . "' onclick='switch_on(this.title,this.id)'>";
                                        } else
                                            echo "<div class='chat_list' title='" . $_SESSION['receiver_id'] . "' id='" . $details['id'] . "' onclick='switch_on(this.title,this.id)'>";
                                        ?>
                                            <div class='chat_people' id='switch_convo'>
                                            <div class='chat_img'><img src='<?php
                                        if ($p->check_column($details['id'], 'pro_pic')) {
                                            echo $p->get_column($details['id'], 'pro_pic');
                                        } else {
                                            echo "img/demo.png";
                                        }
                                        ?>' alt='profile_pic'></div><?php
                                            echo "<div class='chat_ib'>
                                                        <h5>
                                                        <a style='text-decoration: none;' href='http://localhost:8080/matcha/user_profile.php?id=" . $details['id'] . "'>" . $details['username'] . "</a><span class='chat_date'>" . $details['last_seen'] . "</span></h5>
                                                        <p>" . $details['profile_status'] . "</p>
                                                    </div>
                                                </div>
                                            </div>";
                                    $count++;
                                    }
                                } else {
                                    echo '<script>alert("You dont have any messages, start a chat with friend!!!");window.location.href ="index.php";</script>';
                                }
                            ?>
                        </div>
                    </div>

                    <div class="mesgs">
                        <div class="msg_history" id='conversation'>
                            <?php $p->fetch_convo();?>
                        </div>
                        <div class="type_msg">
                            <div class="input_msg_write">
                                <form action="funcs/send_msg.php" method="POST" id="send_msg">
                                    <input type="text" name="msg" class="write_msg" placeholder="Type a message"/>
                                    <button id="sendButton" class="msg_send_btn" name="submit" type="submit">
                                        <i class="fa fa-paper-plane-o fa-lg" aria-hidden="true"></i>
                                    </button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mastfoot">
            <div class="inner">
                <p style="color:red;" <?php echo 'align="center"' ?>><i class="fa fa-barcode"></i> Matcha Project by Kmfoloe Copyright© 2018</p>
            </div>
        </div>

    </div>
        <script>
			//use the setInterval function - first param. requires the function
			//second param. the value of the miliseconds
			//loads data from fetchconversation.php using the load function (method of jquery)
			$(document).ready(function() {
				setInterval(function () {
					$('#conversation').load('funcs/fetch_convo.php');
				}, 1000);
			});

            $(document).ready(function() {
				setInterval(function () {
					$('#notifs').load('funcs/nav.php');
				}, 1000);
			});

            function reloader(){
				$('#displaynotification').load('funcs/notifications.php');
			}

            /**
            off == current receiver
            on == receiver to switch to
            switch active chats
            switch receiver id
            rename tag attributes

             */
            function switch_on(off, on){
                if(on != "active"){
                    document.getElementById(on).classList.add('active_chat');
                    document.getElementById("active").classList.remove('active_chat');
                    document.getElementById("active").id=off;
                    document.getElementById(on).id="active";
                    $('*[title]').prop('title', on);
                    $.ajax({
                        url : 'funcs/switch_id.php',
                        method : 'GET',
                        data : {id: on}
                    });
                    $('#conversation').load('funcs/fetch_convo.php');
                    $('#conversation').animate({scrollTop:$('#conversation').height()},100);
                }
                $('#conversation').animate({scrollTop:$('#conversation').height()},100);
            }

            //scroll to the last message
            var objDiv = document.getElementById("conversation");
            objDiv.scrollTop = objDiv.scrollHeight;
		</script>
    </body>
</html>