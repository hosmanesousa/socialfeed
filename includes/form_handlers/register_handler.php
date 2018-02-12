<?php

// Declaring variables to prevent errors

$fname = "";
$lname = "";
$email = "";
$email2 = "";
$password = "";
$password2 = "";
$date = "";

$error_array = array(); // Holds error messages


if ( isset($_POST['register_button'])){
    // Registration form values
    // First Name
    $fname = strip_tags($_POST['reg_fname']) ;//store in the variable the value sent from the form
    $fname = str_replace(' ', '', $fname); // replace space with no space
    $fname = ucfirst(strtolower($fname)); // lower all and then upper the first
    $_SESSION['reg_fname'] = $fname; // Stores first name into session variables
    
    // Last Name
    $lname = strip_tags($_POST['reg_lname']) ;//store in the variable the value sent from the form
    $lname = str_replace(' ', '', $lname); // replace space with no space
    $lname = ucfirst(strtolower($lname)); // lower all and then upper the first 
    $_SESSION['reg_lname'] = $lname; // Stores last name into session variables
    
    // Email 
    $email = strip_tags($_POST['reg_email']) ;//store in the variable the value sent from the form
    $email = str_replace(' ', '', $email); // replace space with no space
    $email = ucfirst(strtolower($email)); // lower all and then upper the first 
    $_SESSION['reg_email'] = $email; // Stores email into session variables

     
    // Email 2
    $email2 = strip_tags($_POST['reg_email2']) ;//store in the variable the value sent from the form
    $email2 = str_replace(' ', '', $email2); // replace space with no space
    $email2 = ucfirst(strtolower($email2)); // lower all and then upper the first 
    $_SESSION['reg_email2'] = $email2; // Stores email2 into session variables

    
    $password = strip_tags($_POST['reg_password']) ;//store in the variable the value sent from the form
    $password2 = strip_tags($_POST['reg_password2']) ;//store in the variable the value sent from the form
     
    $date = date("Y-m-d"); // Current date
     
    if ( $email == $email2) {
        // Check if email is in valid format
        if ( filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            // check if email already exists
            $email_check = mysqli_query($connect, "SELECT email FROM users WHERE email = '$email'");
            // Count the number of rows returned 
            $num_rows = mysqli_num_rows($email_check); // count the number of rows the email above produced,

            if ( $num_rows > 0) {
                //echo "Email already in use";
                array_push($error_array, "Email already in use<br>");
            }
        } else {
            //echo 'Invalid format';
            array_push($error_array, "Email Invalid format<br>");
        }

    } else {
       // echo 'Emails do not match';
       array_push($error_array, "Emails do not match<br>");
    }

    if( strlen( $fname) > 25 || strlen($fname) < 2) {
        //echo "Your first name must be between 2 and 25 characters";
        array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
    }
    if( strlen( $lname ) > 25 || strlen($lname) < 2) {
        //echo "Your last name must be between 2 and 25 characters";
        array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
    }

    if ( $password != $password2) {
        //echo "Passwords do not match";
        array_push($error_array, "Passwords do not match<br>");
    } else {
        if (preg_match('/[^A-Za-z0-9]/', $password)){
        //echo "Your password can only contain numbers or English characters";
        array_push($error_array, "Your password can only contain numbers or English characters<br>");
    }
}

    if ( strlen($password) > 30 || strlen($password) < 5) {
        //echo "Password must be between 5 and 30 characters";
        array_push($error_array, "Password must be between 5 and 30 characters<br>");
    }
    // code handling the values being inserted into the DB
    if ( empty( $error_array)) {
        $password = md5($password) ;// Encrypt password before sending to the database
        // Generate username by concatenating first and last name
        $username = strtolower($fname . "_" . $lname);
        // check to see if the username is already inserted into the database
        $check_username_query = mysqli_query($connect, "SELECT username FROM users WHERE username = '$username'");

        $i = 0;
        // if username exist add number to username
        while ( mysqli_num_rows($check_username_query) != 0){ // while the num of of rows does not equal 0 
           $i++ ;
           $username = $username . "_" . $i;
           $check_username_query = mysqli_query($connect, "SELECT username FROM users WHERE username = '$username' ");
        }
        
        // Assign default profile picture
        $random = rand(1, 2); // Random number between 1 and 2
        if ( $random == 1) {
            $profile_pic = "assets/images/profile_pics/defaults/head_alizarin.png";
        } else {
            $profile_pic = "assets/images/profile_pics/defaults/head_amethyst.png";
        }
        
        // insert the values into the database
        // '' -> id, no need because it auto increments
        // 1st 0 -> number of posts 
        // 2nd 0 -> number of likes
        // is user closed -> no
        // ',' -> friend array
        $query = mysqli_query($connect, "INSERT INTO users VALUES ('','$fname', '$lname', '$username', '$email', '$password', '$date', '$profile_pic', '0', '0', 'no',',')");

        array_push($error_array, "<span style = 'color:#14C800;'> Ready to log in </span><br>");

        // clear session variables

       $_SESSION['reg_fname'] = "";
       $_SESSION['reg_lname'] = "";
       $_SESSION['reg_email'] = "";
       $_SESSION['reg_email2'] = "";
    }
  }


?>