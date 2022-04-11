<?php

class Operator
{
    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select *
                from operator
                where id=:id
        
        ');
        $query->execute([
            'id'=>$id
        ]);
        return $query->fetch();
    }

    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select *
                from operator
        
        ');
        $query->execute();
        return $query->fetchall();
    }

    public static function insert($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                insert into operator (email, user_password, firstname, lastname, user_role) values
                (:email, :user_password, :firstname, :lastname, :user_role)
        
        ');
        $query->execute([
            'email'=>$paramaters['email'],
            'user_password'=>$paramaters['user_password'],
            'firstname'=>$paramaters['firstname'],
            'lastname'=>$paramaters['lastname'],
            'user_role'=>$paramaters['user_role']
        ]);
    }

    public static function update($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                update operator
                set email = :email, firstname=:firstname, lastname=:lastname, user_role=:user_role, 
                where id = :id
        ');
        $query->execute([
            'email'=>$paramaters['email'],
            'firstname'=>$paramaters['firstname'],
            'lastname'=>$paramaters['lastname'],
            'user_role'=>$paramaters['user_role']
        ]);
    }

    public static function delete($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                delete from operator
                where id=:id
        
        ');
        $query->execute(['id' => $id]);
    }
}