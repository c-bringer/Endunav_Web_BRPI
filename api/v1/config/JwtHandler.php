<?php
require __DIR__.'/../libs/vendor/firebase/php-jwt/src/JWT.php';
require __DIR__.'/../libs/vendor/firebase/php-jwt/src/ExpiredException.php';
require __DIR__.'/../libs/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
require __DIR__.'/../libs/vendor/firebase/php-jwt/src/BeforeValidException.php';

use \Firebase\JWT\JWT;

class JwtHandler {
    protected $jwtSecret;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        //Set your default time-zone
        date_default_timezone_set('Europe/Paris');
        $this->issuedAt = time();
        //Token Validity (7884000 second = 3 month)
        $this->expire = $this->issuedAt + 7884000;
        //Set your secret or signature
        $this->jwtSecret = "endunavSignature";  
    }

    //Encoding the token
    public function _jwt_encode_data($iss, $data)
    {
        $this->token = array(
            //Adding the identifier to the token (who issue the token)
            "iss" => $iss,
            "aud" => $iss,
            //Adding the current timestamp to the token, for identifying that when the token was issued.
            "iat" => $this->issuedAt,
            //Token expiration
            "exp" => $this->expire,
            //Payload
            "data" => $data
        );

        $this->jwt = JWT::encode($this->token, $this->jwtSecret);

        return $this->jwt;
    }

    protected function _errMsg($msg)
    {
        return [
            "auth" => 0,
            "message" => $msg
        ];
    }
    
    //Decoding the token
    public function _jwt_decode_data($jwtToken)
    {
        try {
            $decode = JWT::decode($jwtToken, $this->jwtSecret, array('HS256'));

            return [
                "auth" => 1,
                "data" => $decode->data
            ];
        } catch(\Firebase\JWT\ExpiredException $e) {
            return $this->_errMsg($e->getMessage());
        } catch(\Firebase\JWT\SignatureInvalidException $e) {
            return $this->_errMsg($e->getMessage());
        } catch(\Firebase\JWT\BeforeValidException $e) {
            return $this->_errMsg($e->getMessage());
        } catch(\DomainException $e) {
            return $this->_errMsg($e->getMessage());
        } catch(\InvalidArgumentException $e) {
            return $this->_errMsg($e->getMessage());
        } catch(\UnexpectedValueException $e) {
            return $this->_errMsg($e->getMessage());
        }
    }
}