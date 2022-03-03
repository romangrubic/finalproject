<?php 

class Category
{
    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select * 
                from category
        
        ');
        $query->execute();
        return $query->fetchall();
    }

    public static function insert($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                insert into category (name,description)
                values (:name,:description)
        
        ');
        $query->execute($paramaters);
    }
}