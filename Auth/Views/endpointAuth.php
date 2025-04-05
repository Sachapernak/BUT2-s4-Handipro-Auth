<?php

require_once __DIR__ . '/../Utils/jwt.php';
require_once __DIR__ . '/../Controllers/EndpointController.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Utils/AuthDatabase.php';
require_once __DIR__ . '/../../config/Secrets.php';

const dureeValid = 3600;

// Headers CORS (accessibles depuis un autre domaine)
setCorsHeaders();

// Gérer les requêtes préflight CORS (méthode OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}

// Traitement des autres requêtes
gererRequete();


// ========== Fonctions ==========

function setCorsHeaders() {
    header("Access-Control-Allow-Origin: *"); // à restreindre si besoin
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Content-Type: application/json; charset=UTF-8");
}

function gererRequete() {
    $controller = new EndpointController();
    $http_method = $_SERVER["REQUEST_METHOD"];

    switch ($http_method) {
        case "POST":
            $data = recupererData();
            $response = $controller->creationToken($data);
            $controller->deliver_response($response["code"], $response["message"], $response["data"]);
            break;

        case "GET":
            $token = get_bearer_token();
            $response = $controller->verificationToken($token);
            $controller->deliver_response($response["code"], $response["message"], $response["data"]);
            break;

        default:
            $response = $controller->unsuported_response($http_method);
            $controller->deliver_response($response["code"], $response["message"], $response["data"]);
            break;
    }
}

function recupererData(): ?array {
    $postedData = file_get_contents('php://input');
    return json_decode($postedData, true);
}

function getTokenRole($token) {
    if ($token == null) {
        return null;
    }
    $payload = explode(".", $token)[1];
    return json_decode(base64_decode($payload))->role ?? null;
}
