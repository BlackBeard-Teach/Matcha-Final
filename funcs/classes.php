<?php
    session_start();
    include('../config/db.php');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    class FUNCS {

        // verify sign_up
        public function verify_sign_up($mail, $username, $password, $ip, $fname, $lname, $location){
            
            global $conn;

            $mail = strtolower($mail);
            
            try {

                $pdo = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
                $pdo->execute(array($username, $mail));
                $user_exist = $pdo->rowCount();
                if ($user_exist == 1) {
                    $_SESSION['error'] = "Username or Email Already taken";
                    return ;
                }
                $pass = hash("whirlpool", $password);
                $query= $conn->prepare("INSERT INTO users (username, first_name, last_name, email, `password`, town, token) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $token = uniqid(rand(), true);
                $query->execute(array($username, $fname, $lname, $mail, $pass, $location, $token));
                $this->verification_email($mail, $username, $token, $ip);
                $_SESSION['signup_success'] = true;
                return (0);

            } catch (PDOException $e) {
                echo $_SESSION['error'] = "ERROR: $username $mail ".$e->getMessage();
            }
        }

        // sending verification mail
        public function verification_email($mail, $username, $token, $ip){
          
            $to      = $mail; // Send email to our user
            $subject = ' Matcha Signup | Verification'; // Give the email a subject 
            $message = '
            
            Welcome to Matcha '.ucfirst($username).'!
            
            Your account has been created, you can login with your credentials after you have activated your account by clicking the url below.
            
            Please click this link to activate your account:
            http://localhost:8080/matcha/funcs/verify.php?token='.$token.'
            http://' .$ip.'verify.php?token='.$token.'
            
            '; // Our message above including the link
                                
            $headers = 'From:Teach@matcha.com' . "\r\n"; // Set from headers
            mail($to, $subject, $message, $headers); // Send our email
            echo "<script>alert('Hello ')</script>";
        }

        //activate account
        public function activate($token){
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM users WHERE token = ? AND verified = ?");
            $stmt->execute(array($token, '0'));
            $found = $stmt->rowCount();

            if($found == 1)
            {
                $query = $conn->prepare("UPDATE users SET verified = ? WHERE token = ?");
                $query->execute(array('1', $token));
                echo "Account activated, you can now Login ...<br><br>";
                ?>
                <a href="../login.php"><button style="padding:5px">continue to login</button></a>
                <?php
            } else
                echo "Account Already Activated or Not Found";
        }

        // login
        public function verify_login($user, $pass){
            global $conn;
            try{
                $pdo = $conn->prepare("SELECT * FROM `users` WHERE (username = ?) AND (`password` = ?) AND (verified = 1)");
                $pdo->execute(array($user, $pass));
                $row = $pdo->fetch(PDO::FETCH_ASSOC);
                $found= $pdo->rowCount();

                if ($found == 1)
                {
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['mail'] = $row['email'];
                    $_SESSION['id'] = $row['id'];
                    $id = $_SESSION['id'];

                    //set last seen status
                    $sql = "UPDATE users SET last_seen = 'online' WHERE id = $id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    header('Location: ../index.php');
                }
                else
                {
                    $_SESSION['er'] = "Wrong credentials or account doesn't exist";
                    header('Location: ../login.php');
                } 
            }
            catch (PDOEXCEPTION $e)
            {
                $_SESSION['er'] = $e; 
                header('Location: ../login.php');
            }             
        }

        // navigation
        public function nav(){
            ?>
            <html>
            <head>
                <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css?family=Allerta+Stencil&display=swap" rel="stylesheet">

                <style>
                    body
                    {
                        font-family: 'Allerta Stencil', sans-serif;
                    }
                </style>
            </head>
                <div class="container-fluid">
                    <div class="nav navbar-toggler" id="navbarSupportedContent">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"><a href="index.php"><span><i class="fa fa-home"></i></span> Home</a> </li>
                            <li class="nav-item"><a href="messages.php"><span><i class="fa fa-comments"></i></span> Messages</a></li>
                            <li class="nav-item"><a href="index.php"><span><i class="fa fa-search"></i></span> Search</a></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown01" href="notifications.php" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="reloader()">
                                    <span><i class="fa fa-bell"></i></span> Notifications
                                    <?php $this->notif_counter();?>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdown01" id="displaynotification">
                                    <?php $this->notifications($_SESSION['id']);?>
                                </div>
                            </li>
                            <li class="nav-item"><a href="logout.php"><span><i class="fa fa-sign-out"></i></span> Logout</a></li>
                            <li class="nav-item" type="hidden">
                                <form class="navbar-form" action="http://localhost:8080/matcha/" method="POST">
                                    <div class="input-group add-on">
                                        <input class="form-control" placeholder="Search User" name="search_user" type="text">
                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="submit" name="search"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li class="nav-item"><a href="user_profile.php"><span><i class="fa fa-user-secret"></i></span> <?php print($this->get_column($_SESSION['id'],"username"));?></a></li>
                        </ul>
                    </div>
                </div>
            </html>
            <?php
        }

        // count notification and update number
        public function notif_counter(){
            ?>
            <span class="badge" id="notifs">
                <?php 
                    $notes = $this->get_incoming_likes($_SESSION['id'], 2);
                    if($notes[0] == 0)
                        echo 0;
                    else
                        echo count($notes);
                ?>
            </span>
        <?php
        }

        // display notifications
        public function notifications($userid){
            global $conn;
            $stmt =$conn->query("SELECT * FROM `notifications` WHERE status = 'unread' AND notifieduser = $userid ORDER BY `date` DESC LIMIT 10");
            $query = $stmt->fetchAll();
            if(count($query)>0){
                foreach($query as $i){
                    ?>
                    <a style ="
                    <?php
                    if($i['status']=='unread'){
                        echo "font-weight:bold;";
                    }
                    ?>
                            " class="dropdown-item" href="funcs/view.php?id=<?php echo $i['notificationid']?>&sender=<?php echo $i['stalker']?>&to=<?php echo $i['thenotification']?>">
                        <small><i><?php echo date('F j, Y, g:i a',strtotime($i['date'])) ?></i></small><br/>
                        <?php
                        $user_id = $i['stalker'];
                        $stmt = $conn->query("SELECT username FROM users WHERE id = $user_id");
                        $sql = $stmt->fetchAll();
                        foreach ($sql as $user) {
                            if ($i['status'] == 'unread' && $i['thenotification'] == 'message') {
                                echo $user['username']." sent you a message.";
                            }
                            else if ($i['status'] == 'unread' && $i['thenotification'] == 'view')
                            {
                                echo $user['username']." has viewed your profile.";
                            }
                            else if ($i['status'] == 'unread' && $i['thenotification'] == 'like')
                            {
                                echo $user['username']." has liked your profile";
                            }
                            else if ($i['status'] == 'unread' && $i['thenotification'] == 'unliked')
                            {
                                echo $user['username']." has unliked you, shame on you";
                            }
                            else if ($i['status'] == 'unread' && $i['thenotification'] == 'report')
                            {
                                echo "WTF if you do this again, we will ban you ".$user['username']."!!!";
                            }
                        }
                        ?>
                    </a>
                    <div class="dropdown-divider"></div>
                    <?php
                }
            }else{
                echo "No Notification. Hooray!!!";
            }
        }

        //displaying list of friends found
        public function dump_data($suggestedUsers, $user_id){

            ?>
                <button class="btn btn-secondary btn-danger" id="ageSort" value="ageSort"><i class="fa fa-sort-numeric-asc"></i> Sort by Age</button>
                <button class="btn btn-secondary btn-danger" id="locSort" value="locSort"><i class="fa fa-location-arrow"></i> Sort by Location</button>
                <button class="btn btn-secondary btn-danger" id="fameSort" value="fameSort"><i class="fa fa-star"></i> Sort by Fame-rating</button>
                <button class="btn btn-secondary btn-danger" id="tagSort" value="tagSort"><i class="fa fa-transgender-alt"></i> Sort by Common Tags</button><br><br>
            <?php

            global $conn;
            $i = 0;
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = $user_id");
            $stmt->execute();
            $loggedInUser = $stmt->fetch();

            $loggedInUserGender = $loggedInUser["gender"];
            $loggedInUserPref = $loggedInUser["sexuality"];
            $loggedInUserLocation = $loggedInUser["town"];

            foreach ($suggestedUsers as $user) {

                if ($user['id'] == $user_id)
                    continue;

                //Checking friendship
                $stmt = $conn->prepare("SELECT friend_id FROM friends WHERE (friend_id = ? AND user_id = ?) OR (friend_id = ? AND user_id = ?)");
                $stmt->bindParam(1,$user['id']);
                $stmt->bindParam(2,$user_id);
                $stmt->bindParam(3,$user_id);
                $stmt->bindParam(4,$user['id']);
                $stmt->execute();
                $friendship = $stmt->fetch();

                if ($friendship)
                    continue;

                //checking sexual preference
                if ($loggedInUserPref == "homosexual"){
                    if ($loggedInUserGender != $user['gender'] || $user['sexualPref'] != $loggedInUserPref){
                        continue;
                    }
                }elseif ($loggedInUserPref == "heterosexual") {
                    if ($loggedInUserGender == $user['gender'] || $user['sexualPref'] != $loggedInUserPref) {
                        continue;
                    }
                }

                //checking if a user has been blocked
                $stmt = $conn->prepare('SELECT * FROM block_list WHERE (block_id = :block_id AND user_id = :user_id) OR (block_id = :blockedby2 AND user_id = :blockeduser2)');
                $stmt->execute(['block_id' => $user['id'], 'user_id' => $user_id, 'blockedby2' => $user_id, 'blockeduser2' => $user['id']]);
                $hasbeenblocked = $stmt->fetch();

                if ($hasbeenblocked)
                    continue;
                
                $id_user = $user['id'];
                $i++;
                ?>
                    <div class="well col-md-12">
                        <div class="col-md-4 col-md-offset-4">
                            <?php
                                if($this->check_column($id_user, 'pro_pic')){
                                    echo '<img class="img-thumbnail " src="'.$this->get_column($id_user, 'pro_pic').'">';
                                } else
                                    echo '<img class="img-thumbnail " src="img/demo.png">';
                            ?>
                        </div>
                        <div class="col-md-offset-4 col-md-4">
                            <h3>
                                <label class="label label-default">
                                    <?php echo '<a style="text-decoration: none; color: #fff;" href="user_profile.php?id='.$id_user.'">'.$this->get_column($id_user, 'username').'</a>';?>
                                </label>
                            </h3>
                        </div>
                    </div>
                <?php
            }
            if ($i == 0){
                ?>
                    <div class="well">
                        <p>No users found!!!</p>
                    </div>
                <?php
            }
        }
        
        //filter friends by age and fame rating
        public function filter_age_rate($user_id){
            
            global $conn;
            $_SESSION['page'] = "filter_age_rate";

            if(!empty($_POST["optradio5"])){
                $_SESSION['optradio5'] = $_POST["optradio5"];
            }

            if(!empty($_POST['filterlower']) && !empty($_POST['filterhigher'])){
                $_SESSION['filterlower'] = $_POST['filterlower'];
                $_SESSION['filterhigher'] = $_POST['filterhigher'];
            }

            $filterlowernumber = $_SESSION['filterlower'];
            $filterhighernumber = $_SESSION['filterhigher'];
            $post = $_SESSION['optradio5'];
            $checknumbers = 1;

            if (!empty($_POST['order']))
                $order = $_POST['order'];
            else
                $order = "rand()";

            if ($filterhighernumber <= $filterlowernumber) {
                $checknumbers = 0;
                echo "Please make sure you put the lower and higher numbers in the correct blocks and the numbers are not the same<br>";
            } else {
                if ($post == "sortAge") {
                    echo "<h4>People matching age range:</h4>";
                    $stmt = $conn->prepare("SELECT id, last_name, sexuality, gender FROM users WHERE age >= $filterlowernumber AND age <= $filterhighernumber ORDER BY $order ASC");
                    $stmt->execute();
                    $suggestedUsers = $stmt->fetchAll();
                } elseif ($post == "sortFame") {
                    echo "<h4>People matching fame range:</h4>";
                    $stmt = $conn->prepare("SELECT id, last_name, sexuality, gender FROM users WHERE famerating >= $filterlowernumber AND famerating <= $filterhighernumber ORDER BY $order ASC");
                    $stmt->execute();
                    $suggestedUsers = $stmt->fetchAll();
                }

                $this->dump_data($suggestedUsers, $user_id);
            }
            ?>
            <?php
        }

        //filter friends by location and common tags
        public function filter_loc_com($user_id){
            
            global $conn;
            $_SESSION['page'] = "filter_loc_com";

            if(!empty($_POST["optradio5"])){
                $_SESSION['optradio5'] = $_POST["optradio5"];
            }

            $_POST["optradio5"] = $_SESSION['optradio5'];

            if (!empty($_POST['order']))
                $order = $_POST['order'];
            else
                $order = "rand()";

            $stmt = $conn->prepare("SELECT * FROM users WHERE id = $user_id");
            $stmt->execute();
            $loggedInUser = $stmt->fetch();

            $loggedInUserLocation = $loggedInUser['town'];
            $sport = $loggedInUser['sport'];
            $music = $loggedInUser['music'];
            $hobby = $loggedInUser['hobby'];
            $movie  = $loggedInUser['movie'];

            if ($_POST["optradio5"] == "sortLocation") {
                echo "<h4>People nearby you:</h4>";
                $stmt = $conn->prepare("SELECT id, last_name, sexuality, gender, town FROM users WHERE town = ? ORDER BY $order ASC");
                $stmt->bindParam(1,$loggedInUserLocation);
                $stmt->execute();
                $suggestedUsers = $stmt->fetchAll();
            } elseif ($_POST["optradio5"] == "sortTags") {
                echo "<h4>People with Common Tags:</h4>";
                $stmt = $conn->prepare("SELECT id, last_name, sexuality, gender FROM users WHERE `sport` = ? OR `music` = ? OR `hobby` = ? OR `movie` = ? ORDER BY $order ASC");
                $stmt->bindParam(1,$sport);
                $stmt->bindParam(2,$music);
                $stmt->bindParam(3,$hobby);
                $stmt->bindParam(4,$movie);
                $stmt->execute();
                $suggestedUsers = $stmt->fetchAll();
            }

            $this->dump_data($suggestedUsers, $user_id);
        }

        public function search_user($username, $user_id){
            global $conn;
            $sql = "SELECT * FROM users WHERE id=$user_id";
            $query = $conn->prepare($sql);
            $query->execute();
            $suggestedUsers[0] = $query->fetch();
            $i = 0;
            $sql = "SELECT * FROM users";
            $query = $conn->prepare($sql);
            $query->execute();
            $users = $query->fetchAll();

            foreach ($users as $user){
                if(preg_match("/{$username}/i", $user['username']) || preg_match("/{$username}/i", $user['first_name']) || preg_match("/{$username}/i", $user['last_name'])) {
                    $suggestedUsers[$i] = $user;
                    $i++;
                }
            }
            $this->dump_data($suggestedUsers, $user_id);
        }

        //get ids of friends using friend id
        public function friends_ids($user_id){

            global $conn;
            $friend_ids[0] = 0;
            $i = 0;
            $stmt = $conn->prepare("SELECT friend_id FROM friends WHERE `user_id` = $user_id");
            $stmt->execute();
            $friends = $stmt->fetchAll();
            
            if ($friends){
                foreach ($friends as $id){
                    $friend_ids[$i] = $id['friend_id'];
                    $i++;
                }
            }

            $stmt = $conn->prepare("SELECT `user_id` FROM friends WHERE friend_id = $user_id");
            $stmt->execute();
            $friends = $stmt->fetchAll();

            if ($friends){
                foreach ($friends as $id){
                    if (in_array($id['user_id'], $friend_ids))
                        continue;
                    $friend_ids[$i] = $id['user_id'];
                    $i++;
                }
            }
            return $friend_ids;
        }

        //checking mutual friends
        public function mutual_friends($f_id, $f_ids){
            $count = 0;
            $mf_ids = $this->friends_ids($f_id);
            if ($mf_ids[0] != 0){
                foreach ($mf_ids as $user_id){
                    if (in_array($user_id, $f_ids))
                        $count++;
                }
            }
            return $count;
        }

        //looking for people with mutual friends
        public function friends($user_id){

            global $conn;
            $_SESSION['page'] = "friends";

            if (!empty($_POST['order']))
                $order = $_POST['order']." ASC";
            else
                $order = "rand()";

            $common = array();
            $i = 0;
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = $user_id");
            $stmt->execute();
            $loggedInUser = $stmt->fetch();

            $f_ids = $this->friends_ids($user_id);

            $stmt = $conn->prepare("SELECT id FROM users WHERE id != $user_id ORDER BY $order");
            $stmt->execute();
            $users_ids = $stmt->fetchAll();

            foreach ($users_ids as $f_id){
                if (in_array($f_id['id'], $f_ids))
                        continue;
                if ($mutual_count = $this->mutual_friends($f_id['id'], $f_ids) > 0){
                    $common[$i] = (int) $f_id['id'];
                    $i++;
                }

                //limiting number of friend suggestion to 10
                if ($i == 9)
                    break;
            }

            $common = join("','", $common);

            echo "<h4>People you may know:</h4>";
            $stmt = $conn->prepare("SELECT id, last_name, sexuality, gender, town FROM users WHERE id IN ('$common')");
            $stmt->execute();
            $suggestedUsers = $stmt->fetchAll();
            $this->dump_data($suggestedUsers, $user_id);
        }

        //checking user validity
        public function is_member($user_id){

            global $conn;
            $stmt = $conn->prepare("SELECT gender FROM users WHERE `id` = $user_id");
            $stmt->execute();

            if($stmt->rowCount()>0){
                return true;
            }
            else{
                return false;
            }
        }

        //get user full name
        public function full_name($user_id){
            
            global $conn;
            $stmt = $conn->prepare("SELECT first_name ,last_name FROM users WHERE `id` = $user_id");
            $stmt->execute();

            if($stmt->rowCount()>0){
                $result = $stmt->fetchObject();
                $name = $result->first_name . ' ' . $result->last_name;
                echo $name;
            }
            else{
                return false;
            }
        }

        //check if the column in database table has data
        public function check_column($user_id, $column){
            
            global $conn;
            $stmt = $conn->prepare("SELECT $column FROM users WHERE `id` = $user_id");
            $stmt->execute();

            if($stmt->rowCount()>0){
                $result = $stmt->fetchObject();
                if(empty($result->$column))
                {
                return false;
                }
                else
                {
                    return true;
                }
            }
        }

        //collect data from database table column
        public function get_column($user_id, $column){
            
            global $conn;
            $stmt = $conn->prepare("SELECT $column FROM users WHERE `id` = $user_id");
            $stmt->execute();

            if($stmt->rowCount()>0){
                $result = $stmt->fetchObject();
                return $result->$column;
            }else{
                return false;
            }
        }

        //get number of mutual friends
        public function mutual_frined_num($user_id, $other_id){

            $f_ids = $this->friends_ids($user_id);

            $mutual_count = $this->mutual_friends($other_id, $f_ids);

            if ($user_id != $other_id)
                echo '<div class="small">[ '.$mutual_count.' mutual friend(s) ]</div>';
        }

        //get out-going friends likes
        public function get_outgoing_likes($user_id){
            global $conn;
            
            $stmt = $conn->prepare("SELECT sent_to_id FROM friend_requests WHERE sent_from_id = ?");
            $stmt->bindParam(1,$user_id);
            $stmt->execute();
            if($stmt->rowCount()){
                $i=0;
                $request[]="";
                while($r=$stmt->fetch(PDO::FETCH_OBJ))
                {
                    $request[$i] = $r->sent_to_id;
                    $i++;
                }
                return $request;
            }else{
                $request[0] = 0;
                return $request;
            }
        }

        //get incoming friends likes
        public function get_incoming_likes($user_id, $to){
            global $conn;
            $stmt = null;
            if ($to == 1){
                $stmt = $conn->prepare("SELECT sent_from_id FROM friend_requests WHERE sent_to_id = ?");
            } else if ($to == 2){
                $stmt =$conn->query("SELECT * FROM `notifications` WHERE status = 'unread' AND notifieduser = $user_id ORDER BY `date` DESC LIMIT 10");
            }
            $stmt->bindParam(1,$user_id);
            $stmt->execute();

            if($stmt->rowCount()){
                $i=0;
                $request[]="";
                while($r=$stmt->fetch(PDO::FETCH_OBJ))
                {
                    $request[$i] = $to == 1 ? $r->sent_from_id : $r->stalker;
                    $i++;
                }
                return $request;
            }else{
                $request[0] = 0;
                return $request;
            }
        }

        // block user
        public function block_user($friend_id, $user_id){
            global $conn;
            $sql1 = "select * from block_list where (block_id = ? and user_id = ?) or (user_id = ? and block_id = ?)";
            $query1 = $conn->prepare($sql1);
            $query1->bindParam(1, $friend_id);
            $query1->bindParam(2, $user_id);
            $query1->bindParam(3, $user_id);
            $query1->bindParam(4, $friend_id);
            $query1->execute();

            if(!$query1->rowCount()){
                $sql = "insert into block_list (block_id,user_id) values(?,?)";
                $query = $conn->prepare($sql);
                $query->bindParam(1, $friend_id);
                $query->bindParam(2, $user_id);
                $query->execute();
            }
        }

        // unblock user
        public function unblock_user($friend_id, $user_id){
            global $conn;
            $sql1 = "delete from block_list where (block_id = ? and user_id = ?) or (user_id = ? and block_id = ?)";
            $query1 = $conn->prepare($sql1);
            $query1->bindParam(1, $friend_id);
            $query1->bindParam(2, $user_id);
            $query1->bindParam(3, $user_id);
            $query1->bindParam(4, $friend_id);
            $query1->execute();
        }

        //get blocked ids
        public function get_blocked_ids($user_id){
            global $conn;

            $stmt = $conn->prepare("SELECT block_id FROM block_list WHERE user_id = ?");
            $stmt->bindParam(1,$user_id);
            $stmt->execute();
            if($stmt->rowCount()){
                $i=0;
                $request[]="";
                while($r=$stmt->fetch(PDO::FETCH_OBJ))
                {
                    $request[$i] = $r->block_id;
                    $i++;
                }
                return $request;
            }else{
                $request[0] = 0;
                return $request;
            }
        }

        // set fame rating
        public function update_fame($id){
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM users WHERE verified=1");
            $stmt->execute();
            $query = $stmt->fetchAll();

            $column = "famerating";
            $mark = $this->friends_ids($id);
            if ($mark[0] > 0 && count($query) > 0)
                $detail = count($mark) / count($query) * 100;
            else
                $detail = 0;
            $detail = (int)$detail."%";
            $this->update_column($id, $column, $detail);
        }

        // like back the user
        public function like_back($other_id,$user_id){
            global $conn;
            $table = "friends";
            $sql1 = "delete from friend_requests where sent_to_id =? and sent_from_id =?";
            $query1 = $conn->prepare($sql1);
            $query1->bindParam(1, $user_id);
            $query1->bindParam(2, $other_id);
            $query1->execute();
            $sql = "insert into $table (user_id,friend_id) values(?,?)";
            $query = $conn->prepare($sql);
            $query->bindParam(1, $user_id);
            $query->bindParam(2, $other_id);
            $query->execute();
            $query->bindParam(1, $other_id);
            $query->bindParam(2, $user_id);
            if($query->execute()){
                $notification = "like";
                $sql2 = "INSERT INTO `notifications` (`notifieduser`, `stalker`, `thenotification`) VALUES (?,?,?)";
                $query2 = $conn->prepare($sql2);
                $query2->bindParam(1, $other_id);
                $query2->bindParam(2, $user_id);
                $query2->bindParam(3, $notification);
                $query2->execute();
            }
            $this->update_fame($other_id);
            $this->update_fame($user_id);
        }

        //add notification to table
        public function send_note($friend_id, $user_id, $notification){
            global $conn;
            $sql = "select * from notifications where (notifieduser = ? and stalker = ?)";
            $query = $conn->prepare($sql);
            $query->bindParam(1, $friend_id);
            $query->bindParam(2, $user_id);
            $query->execute();

            if(!$query->rowCount()) {
                $sql = "INSERT INTO `notifications` (`notifieduser`, `stalker`, `thenotification`) VALUES (?,?,?)";
                $query = $conn->prepare($sql);
                $query->bindParam(1, $friend_id);
                $query->bindParam(2, $user_id);
                $query->bindParam(3, $notification);
                $query->execute();
            }
        }

        // send like to user
        public function send_like($friend_id, $user_id){
            global $conn;
            $sql1 = "select * from friend_requests where (sent_to_id = ? and sent_from_id = ?) or (sent_to_id = ? and sent_from_id = ?)";
            $query1 = $conn->prepare($sql1);
            $query1->bindParam(1, $friend_id);
            $query1->bindParam(2, $user_id);
            $query1->bindParam(3, $user_id);
            $query1->bindParam(4, $friend_id);
            $query1->execute();

            if(!$query1->rowCount()){
                $sql = "insert into friend_requests (sent_to_id,sent_from_id) values(?,?)";
                $query = $conn->prepare($sql);
                $query->bindParam(1, $friend_id);
                $query->bindParam(2, $user_id);
                if($query->execute()){
                    $this->send_note($friend_id, $user_id, "like");
                }
            }
        }

        // reject the friend request
        public function reject_like($other_id,$user_id){
            global $conn;
            $sql1 = "delete from friend_requests where sent_to_id =? and sent_from_id =?";
            $query1 = $conn->prepare($sql1);
            $query1->bindParam(1, $user_id);
            $query1->bindParam(2, $other_id);
            $query1->execute();
        }

        // unlike the user
        public function unlike($other_id,$user_id){
            global $conn;
            $sql = "delete from friends where (user_id = ? and friend_id = ?) or (user_id = ? and friend_id = ?)";
            $query = $conn->prepare($sql);
            $query->bindParam(1, $user_id);
            $query->bindParam(2, $other_id);
            $query->bindParam(3, $other_id);
            $query->bindParam(4, $user_id);
            $query->execute();

            $sql = "delete from messages where (sender_id = ? and receiver_id = ?) or (sender_id = ? and receiver_id = ?)";
            $query = $conn->prepare($sql);
            $query->bindParam(1, $user_id);
            $query->bindParam(2, $other_id);
            $query->bindParam(3, $other_id);
            $query->bindParam(4, $user_id);
            if($query->execute()){
                $notification = "unliked";
                $sql2 = "INSERT INTO `notifications` (`notifieduser`, `stalker`, `thenotification`) VALUES (?,?,?)";
                $query2 = $conn->prepare($sql2);
                $query2->bindParam(1, $other_id);
                $query2->bindParam(2, $user_id);
                $query2->bindParam(3, $notification);
                $query2->execute();
            }
            $this->update_fame($other_id);
            $this->update_fame($user_id);
        }

        // unlike the user
        public function report($other_id,$user_id){
            global $conn;
            $notification = "report";
            $sql2 = "INSERT INTO `notifications` (`notifieduser`, `stalker`, `thenotification`) VALUES (?,?,?)";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(1, $other_id);
            $query2->bindParam(2, $user_id);
            $query2->bindParam(3, $notification);
            $query2->execute();
        }

        //get friends in messages
        public function friends_details($friend_ids){
            global $conn;
            $friend_details[0] = 0;
            $i = 0;
            foreach($friend_ids as $friend_id){ 
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = $friend_id");
                $stmt->execute();
                $friend = $stmt->fetch();

                $friend_details[$i] = $friend;
                $i++;
            }
            return $friend_details;
        }

        //get messages from database
        public function fetch_messages($to, $from){
            global $conn;
            $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = $to AND receiver_id = $from) OR (sender_id = $from AND receiver_id = $to) ORDER BY sent_time ASC");
            $stmt->execute();
            $messages = $stmt->fetchAll();

            return $messages;
        }

        //refresh conversation
        public function fetch_convo(){
            $convo = $this->fetch_messages($_SESSION['receiver_id'], $_SESSION['id']);
            $pro_pic = null;
            if($this->check_column($_SESSION['receiver_id'],'pro_pic')){
                $pro_pic = $this->get_column($_SESSION['receiver_id'],'pro_pic');
            } else {
                $pro_pic = "img/demo.png";
            }
            foreach($convo as $msg){
                if($msg['receiver_id'] == $_SESSION['id']){
                    echo "<div class='incoming_msg'>
                            <div class='incoming_msg_img'> <img src='".$pro_pic."' alt='sunil'> </div>
                            <div class='received_msg'>
                                <div class='received_withd_msg'><i class=\"fa fa-bolt\"></i>
                                    <p>".$msg['message']."</p>
                                    <span class='time_date'>".$msg['sent_time']."</span>
                                </div>
                            </div>
                        </div>";
                } else {
                    echo "<div class='outgoing_msg'>
                            <div class='sent_msg'><i class=\"fa fa-bolt\"></i>
                                <p>".$msg['message']."</p>
                                <span class='time_date'>".$msg['sent_time']."</span>
                            </div>
                        </div>";
                }
            }
        }

        // send message to database
        public function send_msg($sender_id, $receiver_id, $msg){
            global $conn;
            $sql = "INSERT INTO messages (`sender_id`, `receiver_id`, `message`) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $sender_id);
            $stmt->bindParam(2, $receiver_id);
            $stmt->bindParam(3, $msg);
            $stmt->execute();

            $notification = "message";
            $sql2 = "INSERT INTO `notifications` (`notifieduser`, `stalker`, `thenotification`) VALUES (?,?,?)";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(1, $receiver_id);
            $query2->bindParam(2, $sender_id);
            $query2->bindParam(3, $notification);
            $query2->execute();
        }
        
        //update column in users table
        public function update_column($user_id, $column, $detail){
            
            global $conn;
            $stmt = $conn->prepare("UPDATE users SET $column = ? WHERE `id` = ?");
            $stmt->bindParam(1, $detail);
            $stmt->bindParam(2, $user_id);
            $stmt->execute();
        }
    }

    $p = new FUNCS;

?>