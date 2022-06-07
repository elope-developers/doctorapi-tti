<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    require_once("db_connect.php");

    // GET JSON INPUT
    $POST_INPUT = json_decode(file_get_contents('php://input'));
    
    // Switch on method passed via post
    $method = (isset($POST_INPUT->method)) ? $POST_INPUT->method : "not_set";
    $token = (isset($POST_INPUT->token)) ? $POST_INPUT->token : "not_set";
    $email = (isset($POST_INPUT->email)) ? $POST_INPUT->email : "not_set";
    $pass = (isset($POST_INPUT->pass)) ? $POST_INPUT->pass : "not_set";

    // uncomment for testing 
    //$method = "verify_login";
    //$method = "get_patients";

    switch($method){
            
        // Get Patients
        case "get_patients":
            if(isDoctor($token)){
                echo getPatients();
            }else{
                echo json_encode(array(
                    "message" => "Error: User is not authorized",
                    "status" => 500
                ));
            }
            break;


        // Verify Login
        case "verify_login":
            echo verify($email,$pass);
            break;

        // Default 
        default:
            echo json_encode(array(
                "message" => "Error: incorrect method name passed",
                "success" => 500
            ));
            break;
    }



    /// THESE FUNCTIONS MAKE API CALLS TO THE DATABASE 

    // This function gets the list of patients from the database
    function getPatients(){
        $db_connect = new db_connect();

        // build query
        $params = [
            "sql"=>"select user_id, email from users where role = ?",
            "bind_keys"=>"s",
            "bind_values"=>["patient"]
        ];
       
        $response = $db_connect->query($params);
        if(sizeOf($response) > 0) {
            return json_encode(array(
                "api_response" => $response,
                "message" => "patients retreived",
                "status" => 200
            ));
        } else {

            return json_encode(array(
                "message" => "0 patients retreived",
                "status" => 500
            ));
        } 
    }
    

    // Super secure way of getting username and password
    function verify($email, $pass){
        $db_connect = new db_connect();
        
        $params = [
            "sql" => "select user_id, email from users where email = ? AND password = ?",
            "bind_keys" => "ss",
            "bind_values" => [$email,$pass],
        ];

        $response = $db_connect->query($params);
        if(sizeOf($response) > 0) {
            return json_encode(array(
                "api_response" => $response,
                "message" => "user is verified",
                "status" => 200
            ));
        } else {
            return json_encode(array(
                "message" => "user not found",
                "status" => 500
            ));
        } 
    }    

    // This function checks to see the the user making the call is a doctor
    function isDoctor($token){
        $db_connect = new db_connect();
        $user = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); // decode the passwed jwt
        // build query
        $params = [
            "sql"=>"select user_id from users where role = ? AND user_id = ?",
            "bind_keys"=>"ss",
            "bind_values"=>["doctor",$user->user_id]
        ];
       
        $response = $db_connect->query($params);
        return (sizeOf($response) > 0);
    }
?>