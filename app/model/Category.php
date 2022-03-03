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
}