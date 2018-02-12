<?php

if ( isset($_POST['login_button'])){
    // FILTER_SANITIZE_EMAIL -> makes sure email is in right format
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL);

    $_SESSION['log_email'] = $email; // Store email into session variable 
    $password = md5($_POST['log_password']); // Get password
    
    $check_database_query = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND password ='$password' ");
    // 
    $check_login_query = mysqli_num_rows($check_database_query); // check results of the query above
     // check_login_query should return 0 (-> login unsuccessful) or 1 (-> login successful)
    if ( $check_login_query == 1) { // 
        // logged in successfully
        // fetch array -> make use of the values returned by the queries
        $row = mysqli_fetch_array($check_database_query);
        $username = $row['username'];


        // if account is closed, then reopen it
        $user_closed_query = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND user_closed = 'yes' ");
         if ( mysqli_num_rows($user_closed_query) == 1) {
             $reopen_account = mysqli_query($connect, "UPDATE users SET user_closed= 'no' WHERE email = '$email' ");
         }
         
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        array_push( $error_array, "Email or password was incorrect<br>");
    }
}

?>