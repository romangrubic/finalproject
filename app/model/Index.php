<?php

class Index
{
    public static function newestProductList()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select a.id, a.name, a.description, a.price
            from product as a
            order by a.dateadded desc
            limit 4
        
        ');
        $query->execute();
        return  $query->fetchAll();
    }

    public static function mostSoldProductList()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select a.id, a.name, a.description, a.price, a.dateadded, count(a.id) as quantitySold
            from product as a
            inner join cart as b on a.id=b.product
            group by a.id, a.name, a.description, a.price, a.dateadded
            order by quantitySold desc
            limit 4; 
        
        ');
        $query->execute();
        return  $query->fetchAll();
    }
}