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

    ini_set("session.cookie_httponly", 1);//to prevent session hijacking
    if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
} //start a session for the user

    
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    $username=(string)$_SESSION["user"];
    $stmt = $mysqli->prepare("select event.event_id, event.event_title, event.event_date, event.event_time, event.tag_name, event.user, users.username
                                    FROM event 
                                    left join users on (event.user=users.username)
                                    order by ?");

        if(!$stmt){
            echo json_encode(array(
                "success" => false,
                "message" => "Query Prep Failed: $mysqli->error"
            ));
            exit;
        }

        $stmt->bind_param('s', $date);
        $stmt->execute();
        $stmt->bind_result($events_id, $events_title, $events_date, $events_time, $events_tag, $event_user, $user_username);

        $response_array=array();

        $ite=0;
        while($stmt->fetch()){

            if ($events_user == $_SESSION['user']){
                $current_event=array(
                    "response_id" => $ite, 
                    "event_id" => $events_id,
                    "event_title" => htmlentities($events_title), 
                    "event_date" => $events_date,
                    "event_time" => $events_time, 
                    "event_tag" => htmlentities($events_tag),
                    "event_user" => htmlentities($events_user), 
                    "user_username" => htmlentities($user_username)
                );

                $current_event_id="event_".$ite;
                $response_array[$current_event_id]=$current_event;
                $ite=$ite+1; 
            }
        }

        $stmt->close();

        $response_array["success"]=true;
        echo json_encode($response_array);
    
    ?>