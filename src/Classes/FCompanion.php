<?php
namespace Hu\Petrik\Classes;

use Error;
use Hu\Petrik\Token;
use Hu\Petrik\User;
require_once "./src/responseCodes.php";

class FCompanion{
    static function getToken(string $key): Token|false{
        try{
            return Token::where("token",$key)->firstOrFail();
        }
        catch (Error $e){
            return false;
        }
    }
    static function getUserByToken(Token $token): User|false{
        try{
            return User::where("id",$token->user_id)->firstOrFail();
        }
        catch (Error $e){
            return false;
        }
    }
}