<?php 

class Category
{
    public static function countCategories()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select count(*)
                from category
        
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description,a.lastUpdated, count(b.id) as hasproducts
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
        
                insert into category (name,description, lastUpdated)
                values (:name,:description, now())
        
        ');
        $query->execute([
            'name'=>$paramaters['name'],
            'description'=>$paramaters['description'],
        ]);
    }

    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.lastUpdated, count(b.id) as hasProducts
                from category a
                left join product b on a.id=b.category
                where a.id=:id
                group by a.id, a.name, a.description, a.lastUpdated
        
        ');
        $query->execute(['id' => $id]);
        return  $query->fetch();
    }

    public static function update($parameters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                update category
                set name = :name, description = :description, lastUpdated=now()
                where id = :id
        ');
        $query->execute([
            'name'=>$parameters['name'],
            'description'=>$parameters['description'],
            'id'=>$parameters['id'],
        ]);
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