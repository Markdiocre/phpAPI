<?php

interface IUser{
    public function getAll();
}

class User implements IUser{

    protected $pdo,$gm, $middleware;

    protected $table_name = "user";

    public function __construct(\PDO $pdo, ResponseMethods $gm, Middleware $md)
    {
        $this->pdo = $pdo;
        $this->gm = $gm;
        $this->middleware = $md;
    }

     public function getAll()
     {
        if(!$this->middleware->isAuthenticated()) return;

        $sql = "SELECT * FROM ".$this->table_name;
        try{
            $stmt = $this->pdo->prepare($sql);

            if($stmt->execute()){
                $data = $stmt->fetchAll();
                if($stmt->rowCount() >= 1){
                    return $this->gm->responsePayload($data, "success","Successfully pulled all data", 200);
                }else{
                    return $this->gm->responsePayload(null, "fauled","No data exisiting", 404);
                }
            }

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
     }
}