
  <?php

    require 'connect_mysql.php';
    require 'verify_user.php';
    $linux_user = 'antond';


    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);

    $username = $json_obj['u'];
    $pass_input = $json_obj['p'];

    if ($username != "" && $pass_input != ""){
        main();
    }
    else {
        echo json_encode(array(
            "success" => false,
            "description" => "Login attempt failed. Input values into both fields!"
        ));
    }

    function main(){
      global $mysqli;
      global $linux_user;
      global $username;
      global $pass_input;

    //   if(!filter_username($username)){
    //     print("<h4>User contains invalid characters, try again.</h4>");
    //     exit();
    //   }

    //   if (!user_exists($username)){
    //     print("<h4>User doesn't exist, try again.</h4>");
    //     exit();
    //   }

        $success= true;
        $description = "";

        $q_hash = $mysqli->prepare("select pass_hash FROM users WHERE username='$username'");
        if(!$q_hash){
            echo json_encode(array(
                "success" => false,
                "description" => "Query Prep Failed"
            ));
            exit;
        }
        
        $q_hash->execute();
        $q_hash -> bind_result($pass_hash);
        $a = $q_hash->fetch();

        if (password_verify($pass_input, $pass_hash)) {

            if (session_status() !== PHP_SESSION_ACTIVE) {
                ini_set("session.cookie_httponly", 1); //I think this is all we need for httponly cookie session
                session_start();
                }
          
            $_SESSION["user"] = $username; //Add this user to the session
            $_SESSION["token"] = bin2hex(openssl_random_pseudo_bytes(32));
            //Adds a CSRF token to the users session.
            //All forms submitted on the website will pull the CSRF token from the users session and send it back.
            //If the token we receive from a post request is ever different from our session token.
            //We know theres something suspect afoot.

            $success= true;
            $description = "Logged in successfully!";
        }
        else {
            $success = false;
            $description = "Password verification failed!";
        }

        //Can send token through body of json

        echo json_encode(array(
            "success" => $success,
            "token" => $_SESSION["token"],
            "description" => $description
        ));
    }
  ?>
