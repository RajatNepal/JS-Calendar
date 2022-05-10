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

    $group_name = (string)$json_obj["group_name"];
    
    $group_description = (string)$json_obj["group_description"];

    // if (!verify_group_owner($username, $group_id)){
    //     echo json_encode(array(
    //         "success" => false,
    //         "description" => "This user is not the group's administrator"
    //     ));
    // }


    foreach ($json_obj as $k => $v) {

        if (($k!="group_id" && $k!="token") && $v!="") {
            $q = $mysqli->prepare("update groups set $k='$v' where group_id=$group_id");

            if(!$q){
                echo json_encode(array(
                    "success" => false,
                    "k" => $k,
                    "v" => $v,
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
        }    
    }
    
    echo json_encode(array(
        "success" => true,
        "description" => "Group successfully edited."
    ));

?>
