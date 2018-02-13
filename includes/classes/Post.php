<?php

class Post {
    private $user_obj;
    private $connect;

    public function __construct($connect, $user) {
        $this->connect = $connect;
        //$user_details_query = mysqli_query($connect, "SELECT * FROM users WHERE username = '$user' ");
       // $this -> user_obj = mysqli_fetch_array($user_details_query);
        $this->user_obj = new User($connect, $user);
    }

    public function submitPost($body, $user_to) {
        $body = strip_tags($body); // removes html tags
        $body = mysqli_real_escape_string($this->connect, $body);
        $check_empty = preg_replace('/\s+/', '', $body);// Delete all spaces
        
        // if check empty is not equal to nothing 
        // reject posts with black spaces
        if ( $check_empty != '') {

            // Current date and time
            $date_added = date("Y-m-d H:i:s");

            // Get username of the person who posted
            $added_by = $this->user_obj->getUsername();

            // If user is on own profile, user_to is 'none'
            
            if ($user_to == $added_by) {
                $user_to = "none";
            }

            // Insert post into the database
            $query = mysqli_query($this->connect, "INSERT INTO posts VALUES ('', '$body', '$added_by', '$user_to','$date_added', 'no', 'no', '0')");
            
            $returned_id = mysqli_insert_id($this->connect);

            // Insert notification

            // Update post count for user
            $num_posts = $this ->user_obj ->getNumPosts(); 
            $num_posts++; // increase num of posts by one
            // include the new added num of posts into the database
            $update_query = mysqli_query($this->connect, "UPDATE users SET num_posts = '$num_posts' WHERE username = '$added_by'");


        }

    }
    
    public function loadPostsFriends($data, $limit) {
        
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        // if its the first time the page has been loaded, start at the first item on the page
        if ( $page == 1) {
            // start at the element 0 in the table
            $start = 0;
        } else {
            // if the page has not been loaded
            $start = ($page - 1) * $limit;
        }

        $str = ""; // String to return
        $data_query = mysqli_query($this->connect, "SELECT * FROM posts WHERE deleted ='no' ORDER BY id DESC");
        
        if( mysqli_num_rows($data_query) > 0 ) {

            // count the number of times the loop has been round
            $num_iterations = 0; // Number of results checked (not necessarily posted)
            $count = 1;
        
        while ( $row = mysqli_fetch_array( $data_query)){
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];

            // Prepare user_to string so it can be included even if not posted to a user

            if($row['user_to'] == "none"){
                // Posting from my own profile
                $user_to = "";
            } else {
                // Posting through somebody else's profile
                $user_to_obj = new User($connect, $row['user_to']);
                $user_to_name = $user_to_obj-> getFirstAndLastName();
                // return a link of whoever the user is on the page
                $user_to = "to <a href=' " . $row['user_to']. " '>" . $user_to_name . "</a>";
            }


            // Check if user who posted, has their account closed

            $added_by_obj = new User($this ->connect, $added_by);
            if ( $added_by_obj -> isClosed()){
                continue;
            }

            if ( $num_iterations++ < $start){
                continue;
            }

            // Once 10 posts have been loaded, break

            if( $count > $limit) {
                break;
            } else {
                $count++;
            }
           


            $user_details_query = mysqli_query($this ->connect, "SELECT first_name, last_name, profile_pic FROM users WHERE username ='$added_by' " );
            $user_row = mysqli_fetch_array($user_details_query);
            $first_name = $user_row['first_name'];
            $last_name = $user_row['last_name'];
            $profile_pic = $user_row['profile_pic'];

            // Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($date_time); // Time of post
            $end_date = new DateTime($date_time_now); // Current time

            $interval = $start_date -> diff($end_date); //Difference between dates
            // if how long it was posted was at least a year ago or more than 1 year
            if ( $interval ->y >= 1) {
                if ( $interval == 1) {
                    $time_message = $interval -> y . " year ago"; // 1 year ago
                } else {
                    $time_message = $interval -> y . " years ago"; // 1+ year ago
                }
            } else if ( $interval ->m >= 1) {
                if ( $interval ->d == 0) {
                    $days = " ago";
                } else if ( $interval ->d == 1) {
                    $days = $interval ->d . " day ago";
                } 
                else {
                    $days = $interval -> d . " days ago";
                }

                if ( $interval ->m == 1) {
                    $time_message = $interval-> m . " month". $days;
                } else {
                    $time_message = $interval-> m . " months".$days;
                }
            }
            else if ( $interval ->d >= 1) {
                if ( $interval ->d == 1) {
                    $time_message = "Yesterday";
                } else {
                    $time_message = $interval -> d . " days ago";
                }
            }
            else if ( $interval ->h >= 1 ) {
                if ( $interval ->h == 1) {
                    $time_message = $interval -> h . " hour ago";
                } else {
                    $time_message = $interval -> h . " hours ago";
                }
            }
            else if ( $interval ->i >= 1 ) {
                if ( $interval ->i == 1) {
                    $time_message = $interval -> i . " minute ago";
                } else {
                    $time_message = $interval -> i . " minutes ago";
                }
            }
            else {
                if ( $interval ->s < 30 ) {
                    $time_message = "Just now";
                } else {
                    $time_message = $interval -> s . "seconds ago";
                }
            }
            // $str = $str . 
            $str .= "<div class = 'status_post'>
                        <div class = 'post_profile_pic'>
                            <img src = '$profile_pic' width = '50'>

                        </div>

                        <div class = 'posted_by' style 'color: #ACACAC;'>
                            <a href = '$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;
                            $time_message
                        </div>
                        <div id = 'post_body'> $body <br> </div>
                    </div><hr>"; 

        }

        if ( $count > $limit) {
            $str .= "<input type = 'hidden' class = 'nextPage' value = '" . ($page+1) . "' >
                    <input type ='hidden' class = 'noMorePosts'  value = 'false'>";
        } else {
            $str .= "<input type = 'hidden' class 'noMorePosts' value = 'true'>
                    <p style = 'text-align:center;'> Oops...No more posts to show.</p>";
        }

    }
    echo $str;

    
        
    }


}


?>