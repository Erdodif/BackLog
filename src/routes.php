<?php

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
};
