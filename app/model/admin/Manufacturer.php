<?php

class Manufacturer
{
    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.id, a.name, a.description, a.lastUpdated, count(b.id) as hasProducts
        from manufacturer a
        left join product b on a.id=b.manufacturer
        where a.id=:id
        group by a.id, a.name, a.description, a.lastUpdated
        
        ');
        $query->execute(['id' => $id]);
        return  $query->fetch();
    }

    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.lastUpdated, count(b.id) as hasproducts
                from manufacturer a
                left join product b on a.id=b.manufacturer
                group by a.id, a.name, a.description, a.lastUpdated;
        
        ');
        $query->execute();
        return $query->fetchall();
    }

    public static function insert($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                insert into manufacturer (name,description, lastUpdated)
                values (:name,:description, now())
        
        ');
        $query->execute([
            'name'=>$paramaters['name'],
            'description'=>$paramaters['description'],
        ]);
    }

    public static function update($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                update manufacturer
                set name = :name, description = :description, lastUpdated=now()
                where id = :id
        ');
        $query->execute([
            'id'=>$paramaters['id'],
            'name'=>$paramaters['name'],
            'description'=>$paramaters['description'],
        ]);
    }

    public static function delete($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                delete from manufacturer
                where id=:id
        
        ');
        $query->execute(['id' => $id]);
    }
}