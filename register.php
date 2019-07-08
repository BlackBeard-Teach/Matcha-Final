<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="img/favicon.ico">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Caveat|Monoton&display=swap" rel="stylesheet">
    <title>Login</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/home.css" rel="stylesheet">
    <!-- <link href="css/login.css" rel="stylesheet"> -->

    <style>
        body
        {
            font-family: 'Monoton', cursive;
        }
    </style>

</head>

<body onload="$('#btnsign').addClass('active')">
<div class="container">
    <div class="panel-body" id="main_page">
        <nav class="navbar navbar-default navbar-expand-lg" id="nav_load">
            <div class="container-fluid">
                <div class="nav navbar-toggler" id="navbarSupportedContent">
                    <ul class="nav navbar-nav">
                        <li id="btnlog"><a href="login.php"><span><i class="fa fa-sign-in"></i></span> Sign In</a>
                        </li>
                        <li id="btnsign"><a href="register.php"><span><i class="fa fa-user"></i></span> Sign Up
                            </a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="inner cover bar" id="signup" style="font-size: larger;" <?php echo 'align="center"'; ?>>
                        <form class="form-horizontal" method="post" action="funcs/signup.php" id="sign_up_form">

                            <h1 class="cover-heading" style="padding-top:15px">Welcome to Matcha</h1>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="email" class="form-control" id="signupEmail" name="Email"
                                           placeholder=" Email address" required>
                                    <span id="email-info"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="text" class="form-control" id="username" name="Username"
                                           placeholder="  Username" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="text" class="form-control" id="firstname" name="Firstname"
                                           placeholder="  First Name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="text" class="form-control" id="lastname" name="Lastname"
                                           placeholder="  Last Name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <select class="browser-default custom-select" id="loc" required>
                                        <option value="">Choose your location</option>
                                        <option value="East London, South Africa"><i class="fa fa-compass"></i> East London, South Africa</option>
                                        <option value="Port Elizaberth, South Africa"><i class="fa fa-compass"></i> Port Elizaberth, South Africa
                                        </option>
                                        <option value="Bloemfontein, South Africa"><i class="fa fa-compass"></i> Bloemfontein, South Africa</option>
                                        <option value="Welkom, South Africa"><i class="fa fa-compass"></i>Welkom, South Africa</option>
                                        <option value="Soweto, South Africa">Soweto, South Africa</option>
                                        <option value="Pretoria, South Africa">Pretoria, South Africa</option>
                                        <option value="Johannesburg, South Africa">Johannesburg, South Africa</option>
                                        <option value="Germiston, South Africa">Germiston, South Africa</option>
                                        <option value="Krugersdorp, South Africa">krugersdorp, South Africa</option>
                                        <option value="Durban, South Africa">Durban, South Africa</option>
                                        <option value="Durban, South Africa">Silverlakes, South Africa</option>
                                        <option value="Durban, South Africa">Kempton Park, South Africa</option>
                                        <option value="Durban, South Africa">South Side, South Africa</option>
                                        <option value="Ladysmith, South Africa">Ladysmith, South Africa</option>
                                        <option value="Pietermaritzburg, South Africa">Pietermaritzburg, South Africa
                                        </option>
                                        <option value="Polokwane, South Africa">Polokwane, South Africa</option>
                                        <option value="Giyani, South Africa">Giyani, South Africa</option>
                                        <option value="Thohoyandou, South Africa">Thohoyandou, South Africa</option>
                                        <option value="Nelspruit, South Africa">Nelspruit, South Africa</option>
                                        <option value="Emalahleni, South Africa">Emalahleni, South Africa</option>
                                        <option value="Mahikeng, South Africa">Mahikeng, South Africa</option>
                                        <option value="Rustenburg, South Africa">Rustenburg, South Africa</option>
                                        <option value="Potchestroom, South Africa">Potchestroom, South Africa</option>
                                        <option value="Kimberly, South Africa">Kimberly, South Africa</option>
                                        <option value="Kuruman, South Africa">Kuruman, South Africa</option>
                                        <option value="Cape Town, South Africa">Cape Town, South Africa</option>
                                        <option value="George, South Africa">George, South Africa</option>
                                        <option value="Stellenbosch, South Africa">Stellenbosch, South Africa
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="password" class="form-control" id="password1" name="Password"
                                           placeholder="Password" required>
                                </div>
                                <span id="password-info"></span>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="password" class="form-control" id="password2" name="cPassword"
                                           placeholder="Confirm Password" required>
                                </div>
                                <span id="password-info"></span>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-3">
                                    <input type="text" id="location" name="location" style="visibility: hidden">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-danger" value="Sign Up" id="signUpBtn" name="submit"
                                    data-toggle="tooltip" data-placement="left" title="Click here to sign up"><span
                                        ><i class="fa fa-user-plus"></i></span> Register
                            </button>

                        </form>
                        <br>
                        <?php
                        if (isset($_SESSION['signup_success'])) {
                            echo "Registration successful. Check your email for verification link.";
                            $_SESSION['signup_success'] = null;
                        } else {
                            echo $_SESSION['error'];
                            $_SESSION['error'] = null;
                        }
                        ?>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
        <div class="mastfoot">
            <div class="inner">
                <p style="color:red;" <?php echo 'align="center"'; ?>><i class="fa fa-barcode"></i>Matcha Project by kmfoloe Copyright© 2018</p>
            </div>
        </div>
    </div>
</div>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/location.js"></script>
<script>
    var signupdiv = $('#signup');
    var logindiv = $('#login');

    function load() {
        logindiv.hide();
        $('#btnsign').addClass("active");

    }

</script>
</body>

</html>