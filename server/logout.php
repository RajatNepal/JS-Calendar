<?php


$linux_user = 'antond';
require 'verify_user.php';
      
      if (!check_csrf()){
        echo json_encode(array(
            "success" => false,
            "description" => "REQUEST FORGERY DETECTED WHAT THE HECK MAN WHY WOULD YOU DO THAT!!!!",
        ));
        exit;
      }

      header("Content-Type: application/json");

         if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION["token"]="";

        //Code taken from StackOverflow
        //https://stackoverflow.com/questions/3989347/php-why-cant-i-get-rid-of-this-session-id-cookie
        $params = session_get_cookie_params();
        setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
        //Citation over

        session_destroy(); //Not sure why session_destroy() isn't setting the contents of the $_SESSION array to empty

        $active = false;
        if (session_status() === PHP_SESSION_ACTIVE) {
          $active = true;
      }

        echo json_encode(array(
            "success" => true,
            "description" => "Session ended successfully",
            "server_token" => $_SESSION["token"],
            "session_active" => $active
        ));
?>