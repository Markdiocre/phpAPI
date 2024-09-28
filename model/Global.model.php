<?php

require_once($apiPath . '/interfaces/Responses.php');

class ResponseMethods implements ResponseInterface
{
    public function responsePayload($payload, $remarks, $message, $code){
        $status = array("remarks" => $remarks, "message" => $message);
        http_response_code($code);
        return array("status" => $status, "payload" => $payload, "timestamp" => date_create(), "prepared_by" => "Mark Thaddeus Manuel");
    }

    public function notFound(){
        echo json_encode([
            "msg"=>"Your endpoint does not exist"
        ]);
        http_response_code(403);
    }
}