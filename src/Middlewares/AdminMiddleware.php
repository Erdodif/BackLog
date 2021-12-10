<?php
namespace Hu\Petrik\Middlewares;

use Hu\Petrik\Classes\FCompanion;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\RequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class AdminMiddleware{
    private $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {   
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request, RequestHandler $handler) :Response
    {
        $auth = $request->getHeader("Authorization");
        $user = FCompanion::getUserByToken(FCompanion::getToken(mb_split(" ", $request->getHeader("Authorization")[0])[1]));
        if($user->admin == 0){
            return $handler->handle($request);
        }
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('{"error":"You have no permission to do that!"}');
        return $response->withHeader("Content-Type", "application/json")->withStatus(ERROR_FORBIDDEN);
    }
}