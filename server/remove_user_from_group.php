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


    
    $username = "";

    if ($json_obj["username"]==""){
        $username = (string)$_SESSION["user"];
    }
    else {
        $username = (string)$json_obj["username"];
    }

    $group_id = (int)$json_obj["group_id"];

    // echo json_encode(array(
    //     "username" => $username
    // ));

    $q = $mysqli->prepare("delete from users_groups_junction where (user='$username' and group_id=$group_id)");


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



    // echo json_encode(array(
    //     "group_id"=> $group_id,
    //     "users" => $users
    // ));



    $q->close();


    echo json_encode(array(
        "success" => true,
        "description" => "Targeted user removed from group"
    ));

?>
