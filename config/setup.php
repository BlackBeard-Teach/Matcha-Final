<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="well col-md-offset-4 col-md-4 text-success" <?php echo 'align="center"';?>>
    <?php
    echo '<br><br>';
    $dbc = new PDO("mysql:host=localhost", "root", 'pass123');
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //database creation
    try {
        $sql = "CREATE DATABASE IF NOT EXISTS matcha";
        $dbc->exec($sql);
        echo "Database matcha created successfully<br>";
    } catch (PDOException $e) {
        echo "ERROR CREATING DB: \n".$e->getMessage();
        exit(1);
    }

    include('db.php');

    //Tables creation
    //Table structure for table `block list`
    try{
        $sql = "CREATE TABLE `block_list`
        (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `block_id` int(11) NOT NULL,
          `user_id` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $conn->exec($sql);
        echo "Block List table created successfully<br>";
    } catch (PDOException $e){
        echo "Error creating Block List table: \n".$e->getMessage();
        exit(1);
    }

    //Table structure for table `friends`
    try{
        $sql = "CREATE TABLE `friends`
        (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `user_id` int(11) NOT NULL,
          `friend_id` int(11) NOT NULL,
          `friend_since` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $conn->exec($sql);
        echo "Friends table created successfully<br>";
    } catch (PDOException $e){
        echo "Error creating Friends table: \n".$e->getMessage();
        exit(1);
    }

    //Dumping data for table `friends`
    try{
        $sql = "INSERT INTO `friends` (`user_id`, `friend_id`, `friend_since`) VALUES
        (2, 49, '2019-05-24 23:23:57'),
        (43, 4, '2019-05-24 23:23:57'),
        (1, 49, '2019-05-24 23:23:57'),
        (49, 1, '2019-05-24 23:23:57'),
        (50, 56, '2019-05-24 23:23:57'),
        (56, 50, '2019-05-24 23:23:57'),
        (1, 50, '2019-05-24 23:23:57'),
        (50, 1, '2019-05-24 23:23:57');";
        $conn->exec($sql);
        echo "Friends table info uploaded successfully<br>";
    } catch (PDOException $e){
        echo "Error uploading Friends info: \n".$e->getMessage();
        exit(1);
    }

    //Table structure for table `friend_requests`
    try{
        $sql = "CREATE TABLE `friend_requests`
        (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `sent_to_id` int(11) NOT NULL,
          `sent_from_id` int(11) NOT NULL,
          `sent_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $conn->exec($sql);
        echo "Friend requests table created successfully<br>";
    } catch (PDOException $e){
        echo "Error creating Friend requests table: \n".$e->getMessage();
        exit(1);
    }

    //Dumping data for table `friend_requests`
    try{
        $sql = "INSERT INTO `friend_requests` (`sent_to_id`, `sent_from_id`, `sent_time`) VALUES
        (50, 49, '2019-05-24 19:47:21'),
        (56, 1, '2019-05-24 19:52:07');";
        $conn->exec($sql);
        echo "Request uploaded successfully<br>";
    } catch (PDOException $e){
        echo "Error uploading friend request info table: \n".$e->getMessage();
        exit(1);
    }

    //Table structure for table `messages`
    try{
        $sql = "CREATE TABLE `messages`
        (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `sender_id` int(11) NOT NULL,
          `receiver_id` int(11) NOT NULL,
          `message` varchar(255) NOT NULL,
          `sent_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `read_status` int(2) NOT NULL DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $conn->exec($sql);
        echo "Messages table created successfully<br>";
    } catch (PDOException $e){
        echo "Error creating Messages table: \n".$e->getMessage();
        exit(1);
    }


    //Dumping data for table `messages`
    try{
        $sql = "INSERT INTO `messages` (`sender_id`, `receiver_id`, `message`, `sent_time`, `read_status`) VALUES
        (1, 49, 'Hey there 49 are you going or coming', '2019-05-15 13:51:58', 0),
        (49, 1, 'Hey there are 1 you going or coming', '2019-05-15 13:51:58', 0),
        (2, 49, 'Hey there 49 are you going or coming', '2019-05-15 13:52:18', 0),
        (49, 2, 'Hey there 2 are you going or coming', '2019-05-15 13:52:18', 0),
        (49, 2, 'ok we will meet tomorrow', '2019-05-16 07:28:12', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 08:00:31', 0),
        (1, 49, 'ok this is a wrap', '2019-05-16 08:01:12', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 08:41:21', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 08:41:24', 0),
        (49, 34, 'ok this is a wrap its 34', '2019-05-16 08:41:28', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 08:41:32', 0),
        (34, 49, 'ok this is a wrap', '2019-05-16 08:41:36', 0),
        (1, 49, 'Finally here', '2019-05-16 08:50:45', 0),
        (1, 49, 'ok am coming too', '2019-05-16 08:51:01', 0),
        (49, 1, 'where are we gonna meet', '2019-05-16 08:51:20', 0),
        (49, 2, 'ok this is a wrap', '2019-05-16 11:51:36', 0),
        (49, 34, 'ola', '2019-05-16 17:46:41', 0),
        (49, 34, 'ok this is a wrap', '2019-05-16 18:09:09', 0),
        (49, 2, 'ok this is a wrap', '2019-05-16 18:14:57', 0),
        (49, 2, 'halo', '2019-05-16 18:15:06', 0),
        (49, 34, 'awe', '2019-05-16 18:15:14', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 18:15:29', 0),
        (49, 2, 'where are we gonna meet', '2019-05-16 18:22:46', 0),
        (49, 2, 'where are we gonna meet', '2019-05-16 18:22:50', 0),
        (49, 34, 'where are we gonna meet', '2019-05-16 18:42:34', 0),
        (1, 49, '8ta', '2019-05-16 18:47:46', 0),
        (49, 1, 'halo', '2019-05-16 18:47:56', 0),
        (1, 49, 'ok am coming too', '2019-05-16 18:48:06', 0),
        (1, 49, 'ok this is a wrap', '2019-05-16 18:49:06', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 18:49:20', 0),
        (1, 49, 'why', '2019-05-16 18:51:34', 0),
        (1, 49, 'ok this is a wrap', '2019-05-16 18:51:51', 0),
        (49, 1, 'where are we gonna meet', '2019-05-16 18:52:34', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 18:53:03', 0),
        (1, 49, 'ok this is a wrap', '2019-05-16 18:53:14', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 18:53:24', 0),
        (49, 1, 'ok this is a wrap', '2019-05-16 18:53:54', 0),
        (1, 49, 'where are we gonna meet', '2019-05-16 18:54:02', 0),
        (1, 49, 'ok this is a wrap', '2019-05-16 18:54:08', 0),
        (49, 1, 'where are we gonna meet', '2019-05-16 18:54:15', 0),
        (1, 49, 'ok this is a wrap', '2019-05-16 18:54:48', 0),
        (49, 1, 'hey wena', '2019-05-17 06:21:10', 0),
        (1, 49, 'obatlang', '2019-05-17 06:22:27', 0),
        (49, 1, 'ne ke go cheka net', '2019-05-17 06:23:03', 0),
        (49, 1, 'ah mf2', '2019-05-17 13:24:06', 0),
        (49, 2, 'ok this is a wrap', '2019-05-18 08:55:18', 0),
        (1, 49, 'awe', '2019-05-18 10:06:32', 0),
        (49, 1, 'where are we gonna meet', '2019-05-18 10:31:27', 0),
        (1, 49, 'hjgjnk,', '2019-05-18 10:31:38', 0),
        (49, 1, 'hey lknjn', '2019-05-18 14:38:12', 0),
        (1, 49, 'ok this is a wrap', '2019-05-18 15:02:39', 0),
        (49, 1, 'halo', '2019-05-18 15:03:04', 0),
        (49, 1, 'whatsapp', '2019-05-19 09:51:44', 0),
        (49, 1, 'uhyfhjlk', '2019-05-20 09:30:04', 0),
        (1, 49, 'ok am coming too', '2019-05-20 09:33:02', 0),
        (49, 1, 'i was kidding mf2', '2019-05-21 15:01:42', 0),
        (1, 50, 'ok this is a wrap', '2019-05-21 19:56:36', 0),
        (50, 56, 'awe', '2019-05-23 15:22:34', 0);";
        $conn->exec($sql);
        echo "Friends table created successfully<br>";
    } catch (PDOException $e){
        echo "Error uploading messages: \n".$e->getMessage();
        exit(1);
    }

    //Table structure for table `notifications`
    try{
        $sql = "CREATE TABLE `notifications`
        (
          `notificationid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `notifieduser` int(11) NOT NULL,
          `stalker` int(11) NOT NULL,
          `thenotification` varchar(1000) NOT NULL,
          `status` varchar(7) NOT NULL DEFAULT 'unread',
          `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $conn->exec($sql);
        echo "Table structure for table `notifications` successful<br>";
    } catch (PDOException $e){
        echo "Error Table structure for table `notifications` failed: \n".$e->getMessage();
        exit(1);
    }

    //Dumping data for table `notifications`
    try{
        $sql = "INSERT INTO `notifications` (`notifieduser`, `stalker`, `thenotification`, `status`, `date`) VALUES
        (44, 50, 'report', 'unread', '2019-05-22 06:12:13'),
        (50, 49, 'unliked', 'unread', '2019-05-24 19:47:16'),
        (50, 1, 'like', 'unread', '2019-05-24 19:51:35');";
        $conn->exec($sql);
        echo "Dumping data for table notifications successful<br>";
    } catch (PDOException $e){
        echo "Error Dumping data for table notifications failed: \n".$e->getMessage();
        exit(1);
    }

    //Table structure for table `users`
    try{
        $sql = "CREATE TABLE `users`
        (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `username` varchar(256) NOT NULL,
          `first_name` varchar(256) NOT NULL,
          `last_name` varchar(256) NOT NULL,
          `email` varchar(256) NOT NULL,
          `password` varchar(1024) NOT NULL,
          `token` varchar(255) NOT NULL,
          `verified` int(1) NOT NULL DEFAULT '0',
          `joining_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `gender` varchar(255) DEFAULT NULL,
          `profile_status` varchar(256) DEFAULT NULL,
          `last_seen` varchar(50) NOT NULL DEFAULT 'offline',
          `pro_pic` varchar(256) DEFAULT NULL,
          `sexuality` varchar(255) DEFAULT NULL,
          `town` varchar(255) DEFAULT NULL,
          `age` varchar(255) DEFAULT NULL,
          `famerating` varchar(256) DEFAULT NULL,
          `sport` varchar(256) DEFAULT NULL,
          `music` varchar(256) DEFAULT NULL,
          `hobby` varchar(256) DEFAULT NULL,
          `movie` varchar(256) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $conn->exec($sql);
        echo "Table structure for table users successful<br>";
    } catch (PDOException $e){
        echo "Error Table structure for table users failed: \n".$e->getMessage();
        exit(1);
    }

    //Dumping data for table `users`
    try{
        $sql = "INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `token`, `verified`, `joining_time`, `gender`, `profile_status`, `last_seen`, `pro_pic`, `sexuality`, `town`, `age`, `famerating`, `sport`, `music`, `hobby`, `movie`) VALUES
        (1, 'Given', 'Paez', 'Makola', 'tango@mailinator.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '', 1, '2019-01-30 14:22:05', 'female', 'Holla there', '2019-05-24 19:59:40', 'img/Given.png', 'bisexual', 'johannesburg', '25', '28%', 'soccer', 'hip-hop', 'dance', 'run'),
        (2, 'Keamogetswe', 'Sibonelo', 'Mthethwa', 'thami@shh.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '', 1, '2019-01-30 14:22:05', 'female', 'Suck that up', 'online', NULL, 'bisexual', 'johannesburg', '40', '20%', 'cricket', 'hip-hop', 'read', 'rude'),
        (4, 'Alfred', 'Alfred', 'Magongwa', 'gfhff', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '', 1, '2019-01-30 14:22:05', 'male', 'I am here', 'online', NULL, 'bisexual', 'johannesburg', '50', '0%', 'cricket', 'kwaito', 'dance', 'nancy'),
        (34, 'Thabo', 'Andile', 'Jali', 'fjhjjh', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '', 1, '2019-01-30 14:22:05', 'male', 'Gotcha', 'offline', NULL, 'bisexual', 'mafikeng', '30', '0%', 'soccer', 'house', 'outing', 'nancy'),
        (43, 'bravo', 'Mogale', 'Brooke', 'bravo@jembut142.cf', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '12885667725c52a8a7826af8.25850260', 1, '2019-01-31 07:49:59', NULL, 'Hello world', 'today', NULL, NULL, NULL, NULL, '12%', NULL, NULL, NULL, NULL),
        (44, 'alpha', 'man', 'team', 'alpha@jembut142.cf', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '1710025685c52dac7a10f80.36388243', 1, '2019-01-31 11:23:51', NULL, 'Cool air in the sky', 'last year', NULL, NULL, NULL, NULL, '0%', NULL, NULL, NULL, NULL),
        (49, 'Tango', 'Gaustavo', 'Tango', 'tango@jembut142.cf', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '13948553415c52df1a943c76.76733393', 1, '2019-01-31 11:42:18', 'Male', 'Wooooza mzansi', '2019-05-24 20:53:55', 'img/Tango.png', 'I am here', 'Rustenburg', '', '28%', 'Rugby', 'Gospel', NULL, 'Dragon city'),
        (50, 'Given', 'X', 'Elex', 'example@mailinator.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '19109502165ce438a4947889.50416723', 1, '2019-05-21 17:43:00', 'Female', 'Yesss girl', 'online', 'img/Exabanis.jpeg', 'Bisexual', 'Mafikeng', '29', '28%', 'Golf', 'Jazz', NULL, 'Robocop'),
        (51, 'Texon', 'Xabanisa', 'Thami', 'texon@mailinator.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '20062564625ce68c3ddf28a8.11480505', 1, '2019-05-23 12:04:13', NULL, NULL, 'online', NULL, NULL, 'Soweto, South Africa', NULL, NULL, NULL, NULL, NULL, NULL),
        (55, 'tex', 'Elex', 'Mogale', 'tex@mailinator.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '17300937115ce6b652707b11.38841587', 1, '2019-05-23 15:03:46', NULL, NULL, 'online', NULL, NULL, 'Johannesburg, South Africa', NULL, NULL, NULL, NULL, NULL, NULL),
        (56, 'TexMan', 'Pro-gamer', 'Max', 'funnyuser@webmail.co.za', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '16731189765ce6b7ff5730f0.30088831', 1, '2019-05-23 15:10:55', NULL, NULL, '2019-05-24 10:23:34', NULL, NULL, 'Johannesburg, South Africa', NULL, '14%', NULL, NULL, NULL, NULL),
        (57, 'Pro-g', 'Gopolang', 'Morena', 'pros@mailinator.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '7488448555ce7aa64268d23.67398005', 1, '2019-05-24 08:25:08', NULL, NULL, 'online', NULL, NULL, 'East Rand, South Africa', NULL, NULL, NULL, NULL, NULL, NULL),
        (59, 'bush', 'bucks', 'ranger', 'Slick@mailinator.com', 'b28849753b0d128e73af2c62e996add42e323bfb892b3886b88964b5f734a76ca1d593eb3f0c350caa5ff577f6e2aff6ce722d394f4f579baec7a9141d90e45e', '6445510985ce832285be9e1.23421188', 1, '2019-05-24 18:04:24', NULL, NULL, 'online', NULL, NULL, '', NULL, '0%', NULL, NULL, NULL, NULL);";
        $conn->exec($sql);
        echo "Dumping data for table users successful<br>";
    } catch (PDOException $e){
        echo "Error Dumping data for table users failed: \n".$e->getMessage();
        exit(1);
    }
    ?>
    <h3>
        <button class="btn btn-md btn-primary">
            <a style="text-decoration: none; color: whitesmoke" href="../index.php">Start Evaluation</a>
        </button>
    </h3>
</div>
</body>
</html>