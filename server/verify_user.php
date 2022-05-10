<?php

    function check_csrf(){
        //returns true if csrf is valid
        //returns false if csrf indicates request forgery
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $json_str = file_get_contents('php://input');
            $json_obj = json_decode($json_str, true);

            $token = $_SESSION['token'];
            $passed = $json_obj['token'];

            // echo json_encode(array(
            //     "success" => false,
            //     "server_token" => $token,
            //     "passed_token" => $passed
            // ));

            if(!hash_equals($_SESSION['token'], $json_obj['token'])){
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false; //This just means they aren't logged in
        }
        
    }

    function user_exists($username){
        global $mysqli;
        $q_user = $mysqli->prepare("select * from users where (username='$username')");
        //REMEMBER TO ADD SINGLE QUOTES AROUND STRINGS
        if(!$q_user){
            //printf("Query Prep Failed: %s\n", $mysqli->error);
           return false;
        }

        $q_user->execute();
        $result = $q_user->get_result();
        $row = $result->fetch_assoc();
       // var_dump($row);
        if (!$row){ //Remember when using arrays you have to use _
            return false; //User doesn't exist yet
        }
        return true; //User does exist
    }

    function filter_username($username) {
        //Filter user
		if( !preg_match('/^[\w_\-]+$/', $username) ){

			return false;
		}
        return true;
    }

    function verify_group_owner($username, $group_id){
        global $mysqli;
        //Returns true if $username is the group owner
        //Returns false otherwise

        echo json_encode(array(
            "success" => false,
            "description" => "Query prep failed"
        ));

        $q = $mysqli->prepare("select admin_username from groups where (group_id=$group_id)");

        if(!$q){
            echo json_encode(array(
                "success" => false,
                "description" => "Query prep failed"
            ));
           return false;
        }

        $q->execute();
        $result = $q->get_result();
        $admin_user = $result->fetch_assoc()["admin_username"];

        $q->close();

        if ($admin_user != $username){ 
            return false; 
        }
        return true;

    }

?>