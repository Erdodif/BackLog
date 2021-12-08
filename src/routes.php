<?php

use Hu\Petrik\Token;
use Hu\Petrik\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
//use Slim\Routing\RouteCollectorProxy;
use Slim\App;

define("RESPONSE_OK", 200);
define("RESPONSE_CREATED", 201);
define("RESPONSE_NO_CONTENT", 204);

return function (App $app) {
    $app->get("/", function (Request $req, Response $res) {
        $res->getBody()->write("Csá");
        return $res;
    });
    $app->post("/register", function (Request $req, Response $res) {
        $userData = json_decode($req->getBody(), true);
        $user = new User();
        $user->email = $userData["email"];
        $user->password = password_hash($userData["password"], PASSWORD_DEFAULT);
        $user->save();
        $res->getBody()->write($user->toJson());
        return $res->withHeader('Content-Type', 'application/json')->withStatus(RESPONSE_CREATED);
    });
    $app->post("/login",function(Request $req, Response $res){
        $loginData = json_decode($req->getBody(), true);
        $email = $loginData["email"];
        $password = $loginData["password"];
        $user = User::where("email",$email)->firstOrFail();
        if(!password_verify($password,$user->password)){
            throw new Exception("Hibás email vagy jelszó!");
        }
        $token = new Token();
        $token->user_id = $user->id;
        $token->token = bin2hex(random_bytes(64));
        //pl keresés hogy ne legyen ütközés
        $token->save();
        $res->getBody()->write(json_encode(["email"=>$email, "token"=>$token->token]));
        return $res->withHeader('Content-Type','application/json')->withStatus(RESPONSE_OK);
    });
};
