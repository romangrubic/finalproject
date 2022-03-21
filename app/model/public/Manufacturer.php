<?php 

class Manufacturer
{
    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select * 
                from manufacturer
                order by name asc
        
        ');
        $query->execute();
        return $query->fetchall();
    }
}