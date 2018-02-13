<?php

require 'config/config.php';

// if the session variable is set
if( isset($_SESSION['username'])){
    // username of the user logged in
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$userLoggedIn' ");
    $user = mysqli_fetch_array($user_details_query);
} else {
    // if this variable is not set
    // if the user is not logged in, send them back to the register page
    header("Location: register.php"); 
}
?>
<html>
<head>
     <title>socialfeed</title>
     <script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
     <script src = "assets/js/bootstrap.js"></script>
     <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css" />
     <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<div class ="top_bar">
    <div class ="logo">
        <a href="index.php">socialfeed</a>
    </div>   
    <nav>
        <a href="<?php echo $userLoggedIn; ?>"><?php echo $user['first_name']; ?></a>
        <a href = "index.php"><i class="fas fa-home"></i></a>
        <a href="#"><i class="fas fa-envelope"></i></a>
        <a href="#"><i class="far fa-cog"></i></a>
        <a href="#"><i class="fas fa-bell"></i></a>
        <a href="#"><i class="fas fa-users"></i></a>
        <a href="includes/handlers/logout.php"><i class="fas fa-sign-out"></i></a>
        
   </nav>
</div>
<div class = "wrapper"> <!-- wrapper class closed in the header file-->