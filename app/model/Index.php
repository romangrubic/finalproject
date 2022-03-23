<?php

class Index
{
    public static function newestProductList()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select a.id, a.name, a.description, b.imageurl as imageurl, a.price
            from product as a
            inner join productimage as b on b.product=a.id
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
        
            select a.id, a.name, a.description,b.imageurl as imageurl, a.price, count(a.id) as soldNumber
            from product as a
            inner join productimage as b on b.product=a.id
            group by a.name
            order by count(a.id) desc
            limit 4
        
        ');
        $query->execute();
        return  $query->fetchAll();
    }
}