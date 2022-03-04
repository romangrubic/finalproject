<?php 

class Category
{
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

    public static function insert($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                insert into category (name,description)
                values (:name,:description)
        
        ');
        $query->execute($paramaters);
    }

    public static function delete($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                delete from category
                where id=:id
        
        ');
        $query->execute(['id' => $id]);
    }
}