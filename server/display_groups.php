<?php

require 'connect_mysql.php';
   
require 'verify_user.php';

    if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
$linux_user = 'antond';

    if (!check_csrf()){
        echo json_encode(array(
            "success" => false,
            "description" => "REQUEST FORGERY DETECTED WHAT THE HECK MAN WHY WOULD YOU DO THAT!!!!"
        ));
        exit; //Uncomment once you're ready to start testing the frontend
    }    

    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

    
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);

    $username = $_SESSION["user"];


    //Query for getting all groups

    $q = $mysqli->prepare("select * from groups where (group_id <> 1)");

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

    $groups = $q -> get_result();
    $all_groups_array = array();

    $count = 0;
    while ($row = $groups->fetch_assoc()){
        $all_groups_array[$count] = $row;
        $count = $count +1;
    }

    $q->close();


    //Query for getting all groups which this user is a part of

//select  users_groups_junction.user, groups.group_id, groups.group_name, groups.group_description from  users_groups_junction join groups on (groups.group_id=users_groups_junction.group_id) where (user = 'rajat');



    $q = $mysqli->prepare("select  groups.admin_username, groups.group_id, groups.group_name, groups.group_description 
    from  users_groups_junction join groups 
    on (groups.group_id=users_groups_junction.group_id) where (user = '$username' AND users_groups_junction.group_id > 1)");

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


    $groups = $q -> get_result();
    $users_groups_array = array();

    $count = 0;
    while ($row = $groups->fetch_assoc()){
        $users_groups_array[$count] = $row;
        $count = $count +1;
    }

    $q->close();

    //Query for getting all groups which this user owns

    $q = $mysqli->prepare("select * from groups where (admin_username = '$username' and group_id <> 1)");

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


    $groups = $q -> get_result();
    $user_owns_groups_array = array();

    $count = 0;
    while ($row = $groups->fetch_assoc()){
        $user_owns_groups_array[$count] = $row;
        $count = $count +1;
    }

    $q->close();
    echo json_encode(array(
        "success" => true,
        "message" =>  "All sets of groups successfully returned",
        "all_groups" => $all_groups_array,
        "users_groups" => $users_groups_array,
        "user_owns_groups" => $user_owns_groups_array
    ));
    exit;
 

    
    ?>