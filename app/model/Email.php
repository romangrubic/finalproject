<?php 

class Email
{
    public static function readOne($parameter)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select email from newsletter where email=:email
        
        ');
        $query->execute([
            'email'=>$parameter
        ]);

        return $query->fetchColumn();
    }

    public static function insert($email)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            insert into newsletter (email) values (:email)
        
        ');
        $query->execute([
            'email'=>$email
        ]);
        return  $query->fetchAll();
    }
}