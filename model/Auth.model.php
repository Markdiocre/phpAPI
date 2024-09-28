<?php

require_once($apiPath . '/interfaces/Auth.php');
/**
 * User table must have username and password field
 * username
 */


class Auth implements AuthInterface
{
    protected $pdo,$gm;

    protected $table_name = "user";

    public function __construct(\PDO $pdo, ResponseMethods $gm)
    {
        $this->pdo = $pdo;
        $this->gm = $gm;
    }

    public function login($data){

        $sql = "SELECT * FROM ? WHERE username=?";
        try{
            $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([$data->username]);
            if ($stmt->rowCount() > 0) {
                $res = $stmt->fetchAll()[0];
                $stmt->closeCursor();
                if ($this->checkPassword($data->password, $res['password'])) {
                    $token = $this->tokenGen(); // You can insert any non-sensitive data inside the token 
                    return $this->gm->responsePayload(array("token"=>$token),"success","Logged in", 200);
                } else {
                    return $this->gm->responsePayload(null, "failed", "Username and password does not match", 400);
                }
            } else {
                return $this->gm->responsePayload(null, "failed", "Account doesn't exist", 404);
            }

            $stmt->closeCursor();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        }catch(\PDOException $e){
            echo $e->getMessage();
        }

    }

    public function logout(){
        //Logout functionality
    }

    public function register($data){
        //Username must be unique 
        $sql = "INSERT INTO ? (username,password) VALUES(?,?)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $data->password = $this->encrypt_password($data->password);
            if ($stmt->execute([$this->table_name, $data->username, $data->password])) {
                $status = $stmt->fetch();
                $stmt->closeCursor();


                return $this->gm->responsePayload(null, "success", "Successfully registered!", 200);
            }

            return $this->gm->responsePayload(null, "failed", "Cannot register user", 400);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function checkPassword($pword, $db_pword)
    {
        return $db_pword === crypt($pword, $db_pword);
    }

    public function generateSalt($length)
    {
        $str_hash = md5(uniqid(mt_rand(), true));
        $b64string = base64_encode($str_hash);
        $mb64string = str_replace("+", '.', $b64string);
        return substr($mb64string, 0, $length);
    }

    public function encrypt_password($pword)
    {
        $hashFormat = "$2y$10$";
        $saltLength = 22;
        $salt = $this->generateSalt($saltLength);
        return crypt($pword, $hashFormat . $salt);
    }

    public function tokenGen($tokenData = null)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['token_data' => $tokenData, 'exp' => date("Y-m-d", strtotime('+7 days'))]);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, SECRET_KEY, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return array("token" => $jwt);
    }

    public function tokenPayload($payload, $is_valid = false){
        return array(
            "payload"=>$payload,
            "is_valid"=>$is_valid
        );
    }

    public function verifyToken()
    {
        //return true with payload if valid, else false
        $jwt = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
        if ($jwt[0] != 'Bearer') {
            return $this->tokenPayload(null);
        } else {
            $decoded = explode(".", $jwt[1]);
            $payload = json_decode(str_replace(['+', '/', '='], ['-', '_', ''], base64_decode($decoded[1])));
            $signature = hash_hmac('sha256', $decoded[0] . "." . $decoded[1], SECRET_KEY, true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            if ($base64UrlSignature === $decoded[2]) {
                return $this->tokenPayload($payload, true);
            } else {
                return $this->tokenPayload(null);
            }
        }
    }


}