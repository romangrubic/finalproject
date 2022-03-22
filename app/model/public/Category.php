<?php 

class Category
{
    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select * 
                from category 
                where id=:id
        
        ');
        $query->execute([
            'id'=>$id
        ]);
        return $query->fetch();
    }

    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, count(b.id) as hasproducts
                from category a
                left join product b on a.id=b.category
                group by a.id, a.name, a.description;
        
        ');
        $query->execute();
        return $query->fetchall();
    }
}