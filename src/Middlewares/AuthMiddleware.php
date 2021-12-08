<?php
namespace Hu\Petrik\Middlewares;

use Exception;
use Hu\Petrik\Token;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Group;
use Slim\App;

class AuthMiddleware{
    public function __invoke(Request $req, RequestHandler $handler) :Response
    {
        $auth = $req->getHeader('Authorization');
        if(count($auth) !==1){
            throw new Exception("Hibás a kérés feljéce!");
        }
        $authArray = mb_split(" ", $auth[0]);
        if($authArray[0]!== 'Bearer'){
            throw new Exception("Nem támogatott autentikaciós módszer!");
        }
        $tokenStr = $authArray[1];
        Token::where("token", $tokenStr)->firstOrFail();
        return $handler->handle($req);
    }
}