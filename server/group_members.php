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

    $group_id = (int)$json_obj["group_id"];
    


    //Note: Upon group creation, this user will be automatically added to the group

    //Problem: Might have to run another query to get group_id.

    $q = $mysqli->prepare("select user from users_groups_junction where (group_id=$group_id)");


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



    //Why is this null. This has no reason to be null lmao
    $users = $q->get_result();


    // echo json_encode(array(
    //     "group_id"=> $group_id,
    //     "users" => $users
    // ));



    $q->close();

    
    $q = $mysqli->prepare("select admin_username from groups where group_id=$group_id");

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


    
    $array[] = array();

    $admin = $q->get_result()->fetch_assoc()["admin_username"];
    $array[0] = $admin;

    $count = 1;

    while ($current_user = $users->fetch_assoc()){
        if ($current_user!=$admin){
            $array[$count] = $current_user;
            $count = $count +1;
        }
    }

    echo json_encode(array(
        "success" => true,
        "description" => "Group users retrieved",
        "group_members" => $array
    ));

?>
