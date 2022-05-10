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

    $username = (string)$_SESSION["user"]; //Not going to use this here

    $group_id = (int)$json_obj["group_id"];

    $range_start = intval($json_obj["range_start"]);
    $range_end = intval($json_obj["range_end"]); 
    $month_num = intval($json_obj["month_num"]);

    //IF range start and end are both 0 then we just want to return the month.

    // select  groups.admin_username, groups.group_id, groups.group_name, groups.group_description 
    // from  users_groups_junction join groups 
    // on (groups.group_id=users_groups_junction.group_id) where (user = '$username' AND users_groups_junction.group_id > 1)\

    //select * from group_event_junction join group on (groups.group_event_id = group_event_junction.group_event_id)


    $q="";
    if ($range_start == 0 && $range_end == 0){
        $q = $mysqli->prepare("select * from event where (group_id=$group_id and  MONTH(event_date) = $month_num)");
    }
    else {
        $q = $mysqli->prepare("select * from event where (group_id=$group_id and  MONTH(event_date) = $month_num and DAY(event_date) >= $range_start and DAY(event_date) <= $range_end)");
     
    }

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
    $events = $q->get_result(); //just need to turn this into an array format 

    $array[] = array();
    $count = 0;

    while ($row = $events->fetch_assoc()){
        $array[$count] = $row;
        $count = $count +1;
    }

    $q->close();


    //Get all dates in month
    //parse each one's day

    echo json_encode(array(
        "success" => true,
        "mnum" => $month_num,
        "description" => "Returning all events for this user",
        "events" => $array,
        "range start" => $range_start,
        "range end" => $range_end,
        "month num" => $month_num,
        "group id" => $group_id
    ));

    //Syntax error in JSON coming from check_csrf, will figure out later
?>
