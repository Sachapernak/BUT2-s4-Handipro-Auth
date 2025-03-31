<?php
require_once __DIR__."/AuthController.php";
require_once __DIR__."/../Models/User.php";
class EndpointController
{
    private AuthController $authController;
    private User $userModel;
    public function __construct()
    {
        $this->authController = new AuthController();
        $this->userModel = new User();
    }

    /**
     * @param $data
     * @return void
     */
    function creationToken($data): array
    {
        if ($data == null || $data['login'] == null || $data['password'] == null) {
            return array("code" => 400, "message"=> "informations de connexions manquantes", "data"=>null);
        }

        $user = $this->userModel->findByUsername($data['login']);

        if ($this->authController->verifyPasswordFromUser($user, $data['password'])) {
            $role = $this->authController->getRoleFromUser($user);

            $headers = array('alg' => 'HS256', 'typ' => 'JWT');

            $payload = array('login' => $data['login'],
                'role' => $role,
                'exp' => time() + dureeValid);

            $token = generate_jwt($headers, $payload, SECRET);
            return array("code" =>200, "message"=> "Authentification OK", "data"=> $token);

        }
        return array("code" => 401, "message"=> "login et/ou mot de passe incorrect", "data"=> null);
    }

    function unsuported_response($http_method): array {
        return array("code" => 405, "message"=> "Methode $http_method non prise en charge", "data"=> null);
    }

    function verificationToken($token): array
    {

        if ($token == null) {
            $data = array('isMissing' => true, 'isExpiredOrInvalid' => false, 'role' => null, 'id' => null);
            return array("code" => 401, "message"=> "token manquant", "data"=> $data);

        } else if (!is_jwt_valid($token, SECRET)) {
            $data = array('isMissing' => false, 'isExpiredOrInvalid' => true, 'role' => null);
            return array("code" => 401, "message"=> "Requete valide. Le token est invalid ou expiré", "data"=> $data);

        } else {
            $data = array('isMissing' => false, 'isExpiredOrInvalid' => false, 'role' => getTokenRole($token));
            return array("code" => 200, "message"=> "Requete valide. Token OK", "data"=> $data);
        }
    }

    function deliver_response($status_code, $status_message, $data=null){

        // Paramétrage de l'entête HTTP
        http_response_code($status_code); //Utilise un message standardisé en fonction du code HTTP

        //header("HTTP/1.1 $status_code $status_message");//Permet de personnaliser le message associé au code HTTP

        header("Access-Control-Allow-Origin: *");
        header("Content-Type:application/json; charset=utf-8;");//Indique au client le format de la réponse
        $response['status_code'] = $status_code;
        $response['status_message'] = $status_message;
        $response['data'] = $data;

        /// Mapping de la réponse au format JSON
        $json_response = json_encode($response);
        if($json_response===false)
            die('json encode ERROR : '.json_last_error_msg());
        /// Affichage de la réponse (Retourné au client)
        echo $json_response;
    }

}