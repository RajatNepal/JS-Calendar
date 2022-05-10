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

    $range_start = intval($json_obj["range_start"]);
    $range_end = intval($json_obj["range_end"]); 
    $month_num = intval($json_obj["month_num"]);

    //IF range start and end are both 0 then we just want to return the month.

    $q="";
    if ($range_start == 0 && $range_end == 0){
        $q = $mysqli->prepare("select * from event where (user='$username' and  MONTH(event_date) = $month_num)");
    }
    else {
        $q = $mysqli->prepare("select * from event where (user='$username' and  MONTH(event_date) = $month_num and DAY(event_date) >= $range_start and DAY(event_date) <= $range_end)");
     
        //select * from event where (user='test' and  MONTH(event_date) = 2 and DAY(event_date) >= 24 and DAY(event_date) <= 25);

    }
     //No matter what, we need the month number which the week is located in

       //can only use month in the select argument but not in the where clause???
        //maybe I need to add a month column to specificlaly do this query????
    
    // else if ($range == "week"){ //Might have to be something along the lines of calling the query 7 times for some range of days
    //                             //We can't directly look for matching weeks in a mysql query
    //     $q = $mysqli->prepare("select * from event where (user=$username,  event_date.MONTH() = $month_num, event_date.)");
    // }

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
    //$events = $q->get_result(); //just need to turn this into an array format 
    $q->bind_result($events_user, $events_id, $events_title, $events_description, 
    $events_date, $events_time, $events_timestamp, $goup_id, $group_event_id, $events_tag );

    // $array[] = array();
    // $count = 0;

    // while ($row = $events->fetch_assoc()){
    //     //array_push($array,$row);
    //     $array[$count] = $row;
    //     $count = $count +1;
    // }

    

    $response_array=array();

        $ite=0;
        while($q->fetch()){
            $current_event=array(
                "response_id" => $ite, 
                "event_id" => $events_id,
                "event_title" => htmlentities($events_title), 
                "event_date" => $events_date,
                "event_time" => $events_time, 
                "event_tag" => htmlentities($events_tag),
                "event_user" => htmlentities($events_user), 
                "user_username" => htmlentities($username)
                );
                $current_event_id="event_".$ite;
                $response_array[$current_event_id]=$current_event;
                $ite=$ite+1; 
            
        }
        $q->close();

    //Get all dates in month
    //parse each one's day

    $response_array["success"]=true;
    $response_array["num_events"]=$ite;
    
    $response_array["description"]="Returning all events for user";
    echo json_encode($response_array);
    

    //Syntax error in JSON coming from check_csrf, will figure out later


    
    ?>