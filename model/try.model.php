<?php

interface IExample{
    public function getAll();
    public function insert($data);
}

class Example implements IExample
{
    protected $pdo, $rm;

    protected $table_name = "item";

    public function __construct(\PDO $pdo, ResponseMethods $rm){
        $this->pdo = $pdo;
        $this->rm = $rm;
    }

    public function hello(){
        $data = [
            "sample"=>"Hello"
        ];
        return $data;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM ".$this->table_name;
        try{
            $stmt = $this->pdo->prepare($sql);

            if($stmt->execute()){
                $data = $stmt->fetchAll();
                if($stmt->rowCount() >= 1){
                    return $this->rm->responsePayload($data, "success","Successfully pulled all data", 200);
                }else{
                    return $this->rm->responsePayload(null, "fauled","No data exisiting", 404);
                }
            }

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }

    public function insert($data){
        $sql = "INSERT INTO ".$this->table_name."(name,price) VALUES(?,?)";
        try{
            $stmt = $this->pdo->prepare($sql);

            if($stmt->execute([$data->name, $data->price])){
                return $this->rm->responsePayload(null, "success","Successfully inserted data", 200);
            }else{
                return $this->rm->responsePayload(null, "failed","Failed to insert data", 400);
            }
        }catch(\PDOException $e){
            echo $e->getMessage();
        }
        return "yo";
    }
}