<?php 


class Product
{
    public static function read($category,$search)
    {
        $connection = DB::getInstance();
        if(!isset($category)){
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product                
                where concat(a.name, \' \', ifnull(a.description, \' \')) like :search
            ');

            $search= '%' . $search . '%';
            $query->bindParam('search', $search);
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