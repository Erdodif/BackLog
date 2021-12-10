<?php
namespace Hu\Petrik\Middlewares;

use Exception;
use Hu\Petrik\Classes\FCompanion;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class AuthMiddleware{
    private $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {   
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request, RequestHandler $handler) :Response
    {
        $auth = $request->getHeader("Authorization");
        try {
            if (count($auth) !== 1) {
                $code = ERROR_BAD_REQUEST;
                $out = '{"error":"Invalid request header!"}';
            } else {
                $authArray = mb_split(" ", $auth[0]);
                if ($authArray[0] !== 'Bearer') {
                    $code = ERROR_METHOD_NOT_ALLOWED;
                    $out = '{"error":"Unsupported method!"}';
                } else {
                    $tokenStr = $authArray[1];
                    if ($tokenStr === "") {
                        $code = ERROR_UNAUTHORIZED;
                        $out = '{"error":"Login reqired!"}';
                    } else {
                        if(FCompanion::getToken($tokenStr)!==false){
                            $out = $handler->handle($request);
                            return $out;
                        }
                        else{
                            $code = ERROR_UNAUTHORIZED;
                            $out =  '{"error":"Invalid or expired Token!"}';
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $code = ERROR_INTERNAL;
            $out =  '{"error":"An internal Error occured!"}';
        }
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($out);
        return $response->withHeader("Content-Type", "application/json")->withStatus($code);
    }
}