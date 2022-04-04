<?php

class Manufacturer
{
    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, count(b.id) as hasproducts
                from manufacturer a
                left join product b on a.id=b.category
                group by a.id, a.name, a.description;
        
        ');
        $query->execute();
        return $query->fetchall();
    }
}