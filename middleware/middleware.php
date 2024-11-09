<?php

class Middleware{
    protected $headers, $auth; 

    public function __construct(Auth $auth){
        $this->headers = apache_request_headers();
        $this->auth = $auth;
    }

    public function isAuthenticated(){
        if(isset($this->headers['Authorization'])){
            $data = explode(' ', $this->headers['Authorization']);

            if($data[0] !== "Bearer") return false;
            $payload = $this->auth->verifyToken($data[1]);

            if(!$payload["is_valid"]) return false;

            return true;
        }

        return false;
    }
}

