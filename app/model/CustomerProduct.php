<?php 


class CustomerProduct
{
    public static function read($category)
    {
        $connection = DB::getInstance();
        if(!isset($category)){
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product                
        
            ');
            $query->execute();
        }else{         
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product
                where a.category = :category
                
            ');
            $query->execute(['category' => $category]);
        }
        
        return $query->fetchAll();
    }
}