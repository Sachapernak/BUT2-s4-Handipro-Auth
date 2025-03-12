<?php

require_once '..\Utils\jwt.php';
require_once '..\Controllers\EndpointController.php';
require_once '..\Models\User.php';
require_once '..\Utils\AuthDatabase.php';
require_once '..\..\config\Secrets.php';

const dureeValid = 600;


function gererRequete(){

    $controller = new EndpointController();

    $http_method = $_SERVER["REQUEST_METHOD"];

    switch ($http_method) {

        case "POST":
            $data = recupererData();

            $response = $controller->creationToken($data);
            $controller->deliver_response($response["code"],$response["message"],$response["data"]);
            break;

        case "GET":
            $token = get_bearer_token();
            $response = $controller->verificationToken($token);
            $controller->deliver_response($response["code"],$response["message"],$response["data"]);
            break;

        default:
            $response = $controller->unsuported_response($http_method);
            $controller->deliver_response($response["code"],$response["message"],$response["data"]);

            break;

    }
}

/**
 * @return void
 */



function recupererData() : ?array{
    $postedData = file_get_contents('php://input');
    return json_decode($postedData,true);
}

function getTokenRole($token){
    if ($token == null) {
        return null;
    }
    $payload = explode(".", $token)[1];
    return json_decode(base64_decode($payload))->role;
}




gererRequete();

