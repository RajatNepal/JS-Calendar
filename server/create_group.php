<?php

require 'connect_mysql.php';
   
require 'verify_user.php';

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

    $username = (string)$_SESSION["user"];

    $group_name = (string)$json_obj["group_name"];
    
    $group_description = (string)$json_obj["group_description"];
    

    $q = $mysqli->prepare("insert into groups (admin_username, group_name, group_description) values ('$username', '$group_name', '$group_description')");


    if(!$q){
        echo json_encode(array(
            "success" => false,
            "description" => "Failed query prep"
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

    //Note: Upon group creation, this user will be automatically added to the group

    //Problem: Might have to run another query to get group_id.

    $q = $mysqli->prepare("select MAX(group_id) from groups order by MAX(group_id)");


    if(!$q){
        echo json_encode(array(
            "success" => false,
            "description" => "Failed query prep"
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

    $group_id = $q->get_result()->fetch_assoc()["MAX(group_id)"];

    // echo json_encode(array(
    //     "group_id" => $group_id["MAX(group_id)"],
    // ));

    //This find the id of the group we just added, current max, and stores it in a variable

    $q->close();


    $q = $mysqli->prepare("insert into users_groups_junction (user, group_id) values ('$username', $group_id)");


    if(!$q){
        $v=$mysqli->error;
        echo json_encode(array(
            "success" => false,
            "description" => $v
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

    echo json_encode(array(
        "success" => true,
        "description" => "New group successfully added! This user has been added to this group!",
        "group_id" => $group_id
    ));

?>
