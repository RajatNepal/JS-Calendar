<?php

require 'connect_mysql.php';
   
require 'verify_user.php';


    //This one is tricky
    //Right now there's no real way to have a handle on an event after its created and passed to all users
    //THe only primary key we have for group events is just their event_id

    //Three events can have the exact same contents, but two can be from a group and 1 won't
    //If we search by matching everything besides event_id we still might not track down the exact group event to update

    //We could create a group_event_id tracker, which creates a new id for each new group event

    //One group maps to multiple group_event_ids

    //Create a table, group_event_junction, primary key is group_event_id, foreign key is group_id


    //Add a group_event_id column to events which is a foreign key to group_event_id in group_event_id_group_junction

    
    //Upon group_event_creation
    //First update group_event_id_group_junction

    //Get the most recent group_event_id from this table

    //Create the event for all users in the group with this group_event_id
    //For creating non group events, just set the group_event_id field to 0. Just like group 1, group_event_id 1 will be a dummy entry
    //That represents that this isn't a group_event


    //Events which are the same group event will have the same group_event_id but different event_id

    //Now we just update where group_event_id = $group_event_id, just like with edit_event.php
    //For this to happen, the frontend needs to keep track of $group_event_id as well
    

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

    $username =(string) $_SESSION["user"];

    $group_event_id = (int)$json_obj["group_event_id"]; //I'm assuming that the frontend is keeping track of the group_event_id for which to alter the group


    foreach ($json_obj as $k => $v) {

        if ((($k!="group_event_id" && $k!="token") && $k!="event_id") && $v!="") {
            $q = $mysqli->prepare("update event set $k='$v' where group_event_id=$group_event_id");

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

    //Just doesn't work nice

    echo json_encode(array(
        "success" => true,
        "description" => "Group vent successfully edited, for all users!"
    ));
?>
