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

    $username = (string)$_SESSION["user"];

    $event_id = (int)$json_obj["event_id"];

    foreach ($json_obj as $k => $v) {

        if ((($k!="group_event_id" && $k!="token") && $k!="event_id") && $v!="") {
            $q = $mysqli->prepare("update event set $k='$v' where event_id=$event_id");

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
        "description" => "Event successfully edited!"
    ));

?>
