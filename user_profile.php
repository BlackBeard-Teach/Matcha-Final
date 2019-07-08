<?php
    session_start();
    include('config/db.php');
    include('funcs/classes.php');

    if(!isset($_SESSION['id'])){
        header("Location: index.php");
    }else{
        $user_id = $_SESSION['id'];
        $_SESSION['request'] = $p->get_outgoing_likes($user_id);
        $_SESSION['incoming'] = $p->get_incoming_likes($user_id, 1);
        $_SESSION['friends'] = $p->friends_ids($user_id);
        $_SESSION['blocked'] = $p->get_blocked_ids($user_id);
    }

    $other_id=$user_id;

    if(isset( $_GET['id']))
    {
        $other_id = trim($_GET['id']);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="img/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Allerta+Stencil|Caveat|Monoton&display=swap" rel="stylesheet">

        <title>Profile</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/home.css" rel="stylesheet">
        <style>
            body
            {
                font-family: 'Allerta Stencil', sans-serif;
            }
            .profile-pic:hover{
                cursor: pointer;
            }
        </style>
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </head>
    <body>
    <div class="container">    
        <div class="panel-body" id="main_page">
            <nav class="navbar navbar-default navbar-expand-lg" id="nav_load">	
				<?php $p->nav(); ?>
			</nav>
            <?php
                if (true){

                    if(is_numeric($other_id)){
                        //check if the provided id is of a valid user
                        if(!($p->is_member($other_id))){
                            echo '<div class="well"><h3 class="alert alert-danger">Wow!!! You managed to hack us, contact us to claim your price!!!</h3></div>';
                            die();
                        }
                        if($other_id != $_SESSION['id'])
                            $p->send_note($other_id, $user_id, "view");
            ?>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3><i class="fa fa-user-o"></i> <?php print($p->full_name($other_id)); ?></h3>
                            <p><?php
                            if($other_id == $_SESSION['id'])
                                echo '<form method="post" action="funcs/update.php" enctype="multipart/form-data">';
                                if($p->check_column($other_id,'profile_status')){
                                    if($other_id == $_SESSION['id']){
                                        echo '<i class="fa fa-terminal"></i> Status : <input type="text" name="profile_status" value="'.$p->get_column($other_id,'profile_status').'">';
                                    } else
                                        echo '<i class="fa fa-terminal"></i> Status : '.$p->get_column($other_id,'profile_status');
                                }else{
                                    if($other_id == $_SESSION['id']){
                                        echo '<i class="fa fa-terminal"></i> Status : <input type="text" name="profile_status" value="I am new on matcha">';
                                    } else
                                        echo '<i class="fa fa-terminal"></i> Status : I am new on matcha';
                                }
                            if($other_id == $_SESSION['id'])
                                echo '  <button type="submit" class="btn btn-sm btn-primary" name="status" ><i class="fa fa-comment"></i>Update Status</button> </form>';
                            ?></p>
                        </div>
                        <div class="panel-body" id="amen">
                            <<?php echo 'center';?> id="div-pic"><img class="img-responsive profile-pic" src="<?php
                            
                                if($p->check_column($other_id,'pro_pic'))
                                {
                                    echo $p->get_column($other_id,'pro_pic');
                                }
                                else{
                                    echo "img/demo.png";
                                }
                                ?>"
                                width="80%" data-toggle="modal" data-target="<?php echo '#'.$other_id; ?>" type="button" />
                                <br>
                                <?php
                                if($user_id != $other_id){?>

                                <button class="btn btn-sm" type="button"><i class="fa fa-handshake-o"></i><?php $p->mutual_frined_num($user_id, $other_id);?> </button><br><br>
                                
                                <!-- friend button status -->
                                <?php if(in_array($other_id, $_SESSION['incoming'])){
                                    ?>
                                        <button class="btn btn-sm btn-success" id="likeBack"><i class="fa fa-heart"></i> Like Back</button>
                                        <button class="btn btn-sm btn-danger" id="rejectLike"><i class="fa fa-frown-o"></i> Reject Like</button>
                                    <?php
                                }
                                else if(in_array($other_id, $_SESSION['request'])){
                                    ?>
                                    <button class="btn btn-sm btn-warning" id="cancelLike"><i class="fa fa-handshake-o"></i> Like back pending</button>
                                    <?php
                                }
                                else
                                { 
                                    if(in_array($other_id, $_SESSION['friends'])){
                                        
                                        echo '<p class="bg-primary" id="friend-status">Connected</p>';
                                        echo '<button class="btn btn-sm btn-danger" id="unLike"><i class="fa fa-thumbs-o-down"></i> Unlike Friend </button>';
                                    
                                    }else if(in_array($other_id, $_SESSION['blocked'])){?>

                                        <button class="btn btn-sm btn-danger" id="unblock"><i class="fa fa-unlock-alt"></i> Unblock</button>
                                    
                                    <?php }else{ ?>

                                        <button class="btn btn-sm btn-primary" id="likeFriend"><i class="fa fa-thumbs-o-up"></i> Like Friend</button>
                                        <button class="btn btn-sm btn-danger" id="block"><i class="fa fa-ban"></i> Block</button>
                                    
                                    <?php }?>

                                    <button class="btn btn-sm btn-warning" id="report"><i class="fa fa-user-times"></i> Report</button>
                                <?php } 
                            }else{
                                if(isset($_SESSION['er'])){
                                    echo '<p style="color:red" >'.$_SESSION["er"].'</p>';
                                    $_SESSION['er'] = null;
                                }
                                echo '<form method="post" action="funcs/update.php" enctype="multipart/form-data">
                                        <div class="c_upload">
                                            Select image to set profile Picture:<br><br>
                                            <input type="file" name="file" id="file" value="-------"><br>
                                            <button type="submit" class="btn btn-sm btn-primary" name="upload" ><i class="fa fa-file-image-o"></i> Upload Profile Picture</button>
                                        </div>
                                    </form>';

                            }?>    
                            </<?php echo'center';?>><br>

                            <ul class="list-group">
                            <?php if($other_id == $_SESSION['id']) {
                                if($_SESSION['er']){
                                    echo $_SESSION['er'];
                                    $_SESSION['er'] = null;
                                }
                                echo '<form method="post" action="funcs/update.php" enctype="multipart/form-data">';
                            }
                                ?>
                                <?php 
                                    $details = array("gender", "sexuality", "town", "age", "famerating", "sport", "music", "movie");
                                    $p->update_fame($other_id);
                                    foreach($details as $d){
                                        echo '<li class="list-group-item">'.ucwords($d).' :  ';
                                            if($p->check_column($other_id,$d)){
                                                if($other_id == $_SESSION['id'] && $d != "famerating"){
                                                    if ($d == "age")
                                                        echo '<input type="number" name="'.$d.'" value="'.$p->get_column($other_id,$d).'">';
                                                    else
                                                        echo '<input type="text" name="'.$d.'" value="'.$p->get_column($other_id,$d).'">';
                                                } else
                                                    echo $p->get_column($other_id,$d);
                                            }
                                            else{
                                                if($other_id == $_SESSION['id'] && $d != "email" && $d != "famerating"){
                                                    echo '<input type="text" name="'.$d.'" value="Not Shared">';
                                                } else
                                                    echo 'Not Shared';
                                            }
                                        echo '</li>'; 
                                    }
                                    if($other_id == $_SESSION['id'])
                                        echo '<li class="list-group-item">
                                                <button type="submit" class="btn btn-sm btn-primary" name="update" ><i class="fa fa-floppy-o" aria-hidden="true"></i> Update Profile Information</button>
                                            </li>
                                        </form>';
                                ?>
                            </ul>
                        </div>
                    </div>
                        <div class="mastfoot">
						    <div class="inner">
						        <p style="color:red;" <?php echo 'align="center"';?>><i class="fa fa-barcode"></i> Matcha Project by kmfoloe Copyright© 2018</p>
						    </div>
					    </div>
                </div>    
            </div>
        </div>
    </div>

    <script>

            $(document).ready(function() {
				setInterval(function () {
					$('#notifs').load('funcs/nav.php');
				}, 1000);
			});

            function reloader(){
                $('#displaynotification').load('funcs/notifications.php');
            }

            var id = <?php echo $other_id; ?>;
            var user_id = <?php echo $_SESSION['id'];?>

            //send like
            $('#editProfile').click(function(){
                $.ajax({
                    url : 'register.php',
                    method : 'POST',
                    data : {id : user_id},
                    success : function(){console.log('updated');},
                    error : function(){alert('Unable to update');},
                    complete : function(){console.log('complete');}
                });
                });

            $('#likeFriend').click(function(){
                $.ajax({
                    url : 'funcs/sendlike.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){window.location.href = "user_profile.php?id="+id;},
                    error : function(){alert('Unable to like');},
                    complete : function(){console.log('complete');}
                    });

                });

            $('#rejectLike').click(function(){
                $.ajax({
                    url : 'funcs/rejectlike.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){window.location.href = "user_profile.php?id="+id;},
                    error : function(){alert('Something happened');},
                    complete : function(){console.log('complete');}
                    });
                });

            $('#likeBack').click(function(){
                $.ajax({
                    url : 'funcs/likeback.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){window.location.href = "user_profile.php?id="+id;},
                    error : function(){alert('Failed to like back');},
                    complete : function(){console.log('complete');}
                    });

                });

            $('#unLike').click(function(){
                $.ajax({
                    url : 'funcs/unlike.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){window.location.href = "user_profile.php?id="+id;},
                    error : function(){alert('Unable to like');},
                    complete : function(){console.log('complete');}
                });

            });

            $('#block').click(function(){
                $.ajax({
                    url : 'funcs/block_user.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){console.log('success'); window.location.href = "user_profile.php?id="+id;},
                    error : function(){console.log('failed');},
                    complete : function(){console.log('complete');}
                });
            });

            $('#unblock').click(function(){
                $.ajax({
                    url : 'funcs/unblock_user.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){window.location.href = "user_profile.php?id="+id;},
                    error : function(){console.log('failed');},
                    complete : function(){console.log('complete');}
                });
            });

            $('#report').click(function(){
                $.ajax({
                    url : 'funcs/reportuser.php',
                    method : 'POST',
                    data : {friend_id : id},
                    success : function(){ alert('Reported');},
                    error : function(){alert('unable to report');},
                    complete : function(){console.log("success");}
                    });
                });

            
    </script>
        
        
         
       <?php }
        else{
            echo '<div class="jumbotron">
                <h3 class="alert alert-danger">Wow!!! You managed to hack us, contact us to claim your price!!!</h3>
                </div>';
        }
        ?>
         <?php } ?>
</body>
</html>