<?php

require 'connect_mysql.php';
   
require 'verify_user.php';
$linux_user = 'antond';

    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

    $json_str = file_get_contents('php://input');
    //This will store the data into an associative array
    $json_obj = json_decode($json_str, true);


    //Debugging the server side php is hard

    //Variables can be accessed as such:
    $username = (string)$json_obj['u'];
    $password = (string)$json_obj['p'];
    

    if ($username != "" && $password !=""){ //Just checking to make sure they aren't empty strings
        main();
    }
    else {
        echo json_encode(array(
            "success" => false,
            "description" => "Account creation failed. Input values into both fields!"
        ));
    }



    function main(){

        global $username;
        global $password;

        $a = user_exists($username);
        $b = filter_username($username);

        $success = true;
        $description = "";


        // if ($a) {
        //     $description += "User already exists!";
        //     $success= false;
        // }
        // if (!$b) {
        //     $description += "Username contains invalid characters";
        //     $success= false;
        // }

        if (!user_exists($username) && filter_username($username)){

            create_account($username, $password);
            $success = true;
            $description = "Account created successfully!";
        }
        else {
            $description = "Username already exists, or contains invalid characters!";
            $success = false;
        }

        echo json_encode(array(
            "success" => $success,
            "description" => $description
        ));
        exit;
    }

    function create_account($username, $password){
        global $mysqli;
        global $linux_user;

        $a_user = $mysqli->prepare("insert into users (username,pass_hash) values (?,?)");


        if(!$a_user){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $a_user->bind_param('ss', $username, $pass_hash);

        $executed = $a_user->execute();
    
        $a_user->close();
    }
  
?>
