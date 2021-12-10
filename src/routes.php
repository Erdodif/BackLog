<?php

use Hu\Petrik\Middlewares\AdminMiddleware;
use Hu\Petrik\Middlewares\AuthMiddleware;
use Hu\Petrik\Token;
use Hu\Petrik\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Group;
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
    $app->group("/api",function (Group $group){
        $group->get("/hello", function (Request $req, Response $res) {
            $res->getBody()->write('{"hello":"world"}');
            return $res->withHeader("Content-Type","application/json")->withStatus(RESPONSE_OK);
        });
        $group->get("/admin",function(Request $req, Response $res){
            $res->getBody()->write('{"hello":"admin"}');
            return $res->withHeader("Content-Type","application/json")->withStatus(RESPONSE_OK);
        })->add(new AdminMiddleware($group->getResponseFactory()));
    })->add(new AuthMiddleware($app->getResponseFactory()));
};
/*
Feladat:
* A users táblát egészítsd egy admin BOOL (nem null) mezővel.
* Az /api alatti végpontokat csak bejelentkezés után, _admin_ jogosultággal lehessen meghívni.
  Ha normál user szeretné meghívni, akkor kapjon 403-as hibakódot!
    -> Ezt a middleware generálja!
* Készíts egy /api/deleteuser/{userid} végpontot, amelyre DELETE kérést küldve törli az adott ID-jű usert
  Admint ne lehessen törölni!
  Eredményként nincs kimenetünk (204-es státusz kód).
* Készíts egy /api/setadmin/{userid} végpontot, amelyre POST kérést küldhetünk. A kérés body-ja:
  { "admin": <bool> }, amely beállítja, hogy az adott user admin legyen, vagy sem.
  Az utolsó admintól ne lehessen elvenni az admin jogot!
  Eredményként listázzuk ki a user új adatait.
* Készíts egy /users végpontot, ami listázza az usereket. A kimenet az alábbi struktúrájú legyen:
  {
    "data": [
      { "id": 1, "email": "email@example.com", "admin": true },
      { "id": 2, "email": "normaluser@example.com", "admin": false }	  
    ]
  }
  Vagyis a data változó a userek listáját tartalmazza.
  A kimeneten ne szerepljen a jelszó, a created_at és az updated_at mezők tartalma!
*/