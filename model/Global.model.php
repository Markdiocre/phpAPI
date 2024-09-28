<?php

interface IGlobal{
    public function responsePayload($payload, $remarks, $message, $code);
}

class GlobalMethods implements IGlobal
{
    public function responsePayload($payload, $remarks, $message, $code){
        $status = array("remarks" => $remarks, "message" => $message);
        http_response_code($code);
        return array("status" => $status, "payload" => $payload, "timestamp" => date_create(), "prepared_by" => "Mark Thaddeus Manuel");
    }
}