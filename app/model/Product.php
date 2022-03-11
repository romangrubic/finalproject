<?php 

class Product
{


    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, b.name as category, a.price, a.inventoryquantity, c.imageurl as imageurl
                from product a
                inner join category b on a.category=b.id
                inner join productimage c on c.product=a.id
        
        ');
        $query->execute();
        return $query->fetchall();
    }
}