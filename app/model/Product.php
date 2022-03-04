<?php 


class Product
{
    public static function read($category)
    {
        $connection = DB::getInstance();
        if(!isset($category)){
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product
                group by a.id
                
        
            ');
            $query->execute();
        }else{         
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product
                where a.category = :category
                group by a.id
                
            ');
            $query->execute(['category' => $category]);
        }
        
        return $query->fetchAll();
    }
}