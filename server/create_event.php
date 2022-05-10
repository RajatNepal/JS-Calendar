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

    $username = $_SESSION["user"];

    $event_title = (string)$json_obj["event_title"];
    $event_description = (string)$json_obj["event_description"];
    $event_date = $json_obj["event_date"];
    $event_time = $json_obj["event_time"];
    $group_id = (int)$json_obj["group_id"];
    $tag_name = (string)$json_obj["tag_name"];

    //hard coded to 4 cus my groups start at 4 for some reason
    $group_id = 4;
   //$group_id = 1;
    
  
    
    //Inserting Date and Datetime objects as strings


    $q = $mysqli->prepare("insert into event (user, event_title, event_description, event_date, event_time, group_id, group_event_id, tag_name) values (?,?,?,?,?,?,?,?)");
      if(!$q){
        $v=$mysqli->error;
        echo json_encode(array(
            "success" => false,
            "description" => $v,
            "group_id" => $group_id
        ));
        exit;
    }


    $group_event_id = 0;

    $q->bind_param('sssssiis', 
        $username, 
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
            "b" => "b",
            "description" => $v ,
            "group_id" => $group_id
            
        ));
        exit;
    }

    $q->close();

    echo json_encode(array(
        "success" => true,

        "description" => "New event successfully added!"
    ));
   

  
?>
