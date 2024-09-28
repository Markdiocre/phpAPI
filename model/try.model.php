<?php

interface IExample{
    public function getAll();
}


class Example implements IExample
{
    protected $pdo, $glb;

    protected $table_name = "item";

    public function __construct(\PDO $pdo, GlobalMethods $glb){
        $this->pdo = $pdo;
        $this->glb = $glb;
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
                    return $this->glb->responsePayload($data, "success","Successfully pulled all data", 200);
                }else{
                    return $this->glb->responsePayload(null, "fauled","No data exisiting", 404);
                }
            }

        }catch(\PDOException $e){
            echo $e->getMessage();
        }
    }
}