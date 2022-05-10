<?php

require 'connect_mysql.php';
   
require 'verify_user.php';

//BEFORE TESTING THIS I SHOULD HAVE ADDED A GROUP 0 MANUALLY

    if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$linux_user = 'antond';


    if (!check_csrf()){
        //Note: Not necessarilly a request forgery, might just be not logged in, deal with this later
        echo json_encode(array(
            "success" => false,
            "description" => "REQUEST FORGERY DETECTED WHAT THE HECK MAN WHY WOULD YOU DO THAT!!!!"
        ));
        exit; //Uncomment once you're ready to start testing the frontend
    }    

    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

    
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);

    $username = $_SESSION["user"]; //Not using this for this one



    $event_title = (string)$json_obj["event_title"];
    $event_description = (string)$json_obj["event_description"];
    $event_date = $json_obj["event_date"];
    $event_time = $json_obj["event_time"];
    $tag_name = (string)$json_obj["tag"];

    $group_id = (int)$json_obj["group_id"];

    // if (!verify_group_owner($username, $group_id)){
    //     echo json_encode(array(
    //         "success" => false,
    //         "description" => "This user is not the group's administrator"
    //     ));
    // }

    //Record this event in group_event_junction so that we can generate a unique group_event_id

    $q = $mysqli->prepare("insert into group_event_junction (group_id) values ($group_id)");

    if(!$q){
        echo json_encode(array(
            "success" => false,
            "description" => "Failed query prep 1"
        ));
        exit;
    }
    
    $executed = $q->execute();

    if (!$executed){
        $v=$mysqli->error;
        echo json_encode(array(
            "success" => false,
            "description" => $v
        ));
        exit;
    }

    $q->close();

    //Retreive the group_event_id for this event

    $q = $mysqli->prepare("select MAX(group_event_id) from group_event_junction order by MAX(group_event_id)");


    if(!$q){
        echo json_encode(array(
            "success" => false,
            "description" => "Failed query prep 2"
        ));
        exit;
    }
    
    $executed = $q->execute();

    if (!$executed){
        $v=$mysqli->error;
        echo json_encode(array(
            "success" => false,
            "description" => $v
        ));
        exit;
    }

    $group_event_id = $q->get_result()->fetch_assoc()["MAX(group_event_id)"];


    //Get all usernames which are in this group

    $q = $mysqli->prepare("select user from users_groups_junction where (group_id=$group_id)");

    if(!$q){
        echo json_encode(array(
            "success" => false,
            "description" => "Failed query prep 3"
        ));
        exit;
    }
    
    $executed = $q->execute();

    if (!$executed){
        $v=$mysqli->error;
        echo json_encode(array(
            "success" => false,
            "description" => $v
        ));
        exit;
    }

    $users = $q->get_result(); //Associative array of users

    $q->close();
    

    while ($current_user = $users->fetch_assoc()["user"]){ //Add the event for each user
        $q = $mysqli->prepare("insert into event (user, event_title, event_description, event_date, event_time, group_id, group_event_id, tag_name) values (?,?,?,?,?,?,?,?)");


        if(!$q){
            $v=$mysqli->error;
            echo json_encode(array(
                "success" => false,
                "v" => $v,
                "description" => "Failed query prep 4"
            ));
            exit;
        }
    
        
        //$start_datetime = $event_date." $event_start_time";
        //$end_datetime = $event_date." $event_end_time";

        $q->bind_param('sssssiis', 
            $current_user, 
            $event_title,
            $event_description,
            $event_date,
            $event_time,
            
            $group_id,
            $group_event_id, //group_event_id set to 0 as a dummy event_id
            $tag_name); //tag_name is set to 'none' as a dummy tag

        
        $executed = $q->execute();
    
        if (!$executed){
            $v=$mysqli->error;
            echo json_encode(array(
                "success" => false,
                "description" => $v,
                "current_user" => $current_user
            ));
            exit;
        }
    
        $q->close();


    }

 

    echo json_encode(array(
        "success" => true,
        "description" => "New event successfully added, for all users in group!"
    ));

?>
