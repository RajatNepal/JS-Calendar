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
    
    //Inserting Date and Datetime objects as strings

    $q = $mysqli->prepare("delete from event where event_id=$event_id");

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

    echo json_encode(array(
        "success" => true,
        "description" => "Event successfully deleted"
    ));
   
    //This is properly adding events to the database, 
    //right now it is fully possible to have exact duplicate events under the same user, they will always differ by id.
    //The syntax error isn't affecting that much for now, so I'll move on at the moment
  
?>
