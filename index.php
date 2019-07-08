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
	<!DOCTYPE html>
	<html lang="en">

	<head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="img/favicon.ico">
        <link href="https://fonts.googleapis.com/css?family=Allerta+Stencil&display=swap" rel="stylesheet">

		<title>Search</title>

		<!-- Bootstrap core CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		
		<!-- Custom styles for this search -->
        <link href="css/home.css" rel="stylesheet">
		<script src="js/jquery.js"></script>
        <style>
            body
            {
                font-family: 'Allerta Stencil', sans-serif;
            }
        </style>
	</head>

	<body>
	<div class="container"> 
		<div class="panel-body" id="main_page">
			<nav class="navbar navbar-default navbar-expand-lg" id="nav_load">	
				<?php $p->nav(); ?>
			</nav>
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-secondary">
						<div id="sort" style="border: 1px solid #ccc; box-shadow: 1px 1px 1px #ccc; padding: 10px;" <?php echo 'align="center"';?>>
							<?php
                                if(isset($_POST['search']) && !empty($_POST['search_user'])){
                                    $p->search_user($_POST['search_user'], $user_id);
                                }else if (isset($_POST['submit']) && $_POST["submit"] == "selectFilter" && isset($_POST["optradio5"]) || isset($_POST["order"]) == "rate"  || isset($_POST["order"]) == "age"){
									if ($_POST["optradio5"] == "sortAge" || $_POST["optradio5"] == "sortFame"){
										$p->filter_age_rate($user_id);
									}else{
										$p->filter_loc_com($user_id);
									}
								}else{
									$p->friends($user_id);
								}
							?>

							<form class="form-group" method="POST" action="#">
								<div class="form-group">
									<h4>Filter friends by:</h4>
									<label class="radio-inline">
										<input type="radio" name="optradio5" value="sortAge" id="ageS" onclick="showBox('ageS')"><i class="fa fa-sort-numeric-asc"></i> Age</label>
									<label class="radio-inline">
										<input type="radio" name="optradio5" value="sortLocation" id="locS" onclick="changeView('locS')"><i class="fa fa-location-arrow"></i> Location</label>
									<label class="radio-inline">
										<input type="radio" name="optradio5" value="sortFame" id="fameS" onclick="showBox('fameS')"><i class="fa fa-star"></i> Fame-Rating</label>
									<label class="radio-inline">
										<input type="radio" name="optradio5" value="sortTags" id="comS" onclick="changeView('comS')"><i class="fa fa-transgender-alt"></i> Common Tags</label>
									<br>
								</div>
								<div class="form-group" id="box" style="visibility: hidden; display:none">
									<h4>Enter range on the textarea:</h4>
									<input type="number" min="1" id="min" class="form-control" name="filterlower" placeholder="Enter minimum number"><br>
									<input type="number" min="1" id="max" class="form-control" name="filterhigher" placeholder="Enter maximum number"><br>
								</div>
								<button class="btn btn-secondary btn-default" type="submit" name="submit" value="selectFilter"><i class="fa fa-check"></i> Apply Filter</button>
							</form>
                        </div>
					</div>
				</div>
			</div>
			<div class="mastfoot">
				<div class="inner">
					<p style="color:red;" <?php echo 'align="center";'?>><i class="fa fa-barcode"></i> Matcha Project by Kmfoloe Copyright© 2018</p>
				</div>
			</div>
		</div>
	</div>
		<script src="js/bootstrap.min.js"></script>
		<script>

			//refresh notification count every second
			$(document).ready(function() {
				setInterval(function () {
					$('#notifs').load('funcs/nav.php');
				}, 1000);
			});

			//loads uread notification when notification is clicked
			function reloader(){
				$('#displaynotification').load('funcs/notifications.php');
			}
			
			//show age and fame search text area
			function showBox(id) {
                var x = document.getElementById("box");
				var min = document.getElementById("min");
				var max = document.getElementById("max");

                if (id == "ageS"){
                    var y = document.getElementById("ageS");
                }else if (id == "fameS"){
                    var y = document.getElementById("fameS");
                }

                if (x.style.visibility === "visible") {
                    x.style.visibility = "hidden";
                    x.style.display = "none";
                    y.checked = false;
                } else {
                    x.style.visibility = "visible";
                    x.style.display = "inline";
					min.required = true;
					max.required = true;
                    y.checked = true;
                }
            }

			function changeView(id){
                var x = document.getElementById("box");
				var y = document.getElementById(id);
				var min = document.getElementById("min");
				var max = document.getElementById("max");
				
				if (x.style.visibility === "visible") {
                    x.style.visibility = "hidden";
                    x.style.display = "none";
					min.required = false;
					max.required = false;
                }
				y.checked = true;
			}

			// sort by age
			$('#ageSort').click(function(){
                $.ajax({
                    url : 'sort.php',
                    method : 'POST',
                    data : {order: "age"},
                    success : function(data){
						$('#sort').html("");
						$('#sort').html(data);
                    },
                    error : function(){alert('Failed to sort');},
                    complete : function(){console.log('complete age');}
                });
			});
			
			// sort by location
			$('#locSort').click(function(){
                $.ajax({
                    url : 'sort.php',
                    method : 'POST',
                    data : {order: "town"},
                    success : function(data){
						$('#sort').html("");
						$('#sort').html(data);
                    },
                    error : function(){alert('Failed to sort');},
                    complete : function(){console.log('complete location');}
                });
			});
			
			// sort by fame rating
			$('#fameSort').click(function(){
                $.ajax({
                    url : 'sort.php',
                    method : 'POST',
                    data : {order: "famerating"},
                    success : function(data){
						$('#sort').html("");
						$('#sort').html(data);
                    },
                    error : function(){alert('Failed to sort');},
                    complete : function(){console.log('complete fame');}
                });
			});
			
			$('#tagSort').click(function(){
                $.ajax({
                    url : 'sort.php',
                    method : 'POST',
                    data : {order: ""},
                    success : function(data){
						$('#sort').html("");
						$('#sort').html(data);
                    },
                    error : function(){alert('Failed to sort');},
                    complete : function(){console.log('complete');}
                });
            });
		</script>
	</body>

</html>