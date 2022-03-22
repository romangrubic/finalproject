<?php 

class Manufacturer
{
    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select * 
                from manufacturer 
                where id=:id
        
        ');
        $query->execute([
            'id'=>$id
        ]);
        return $query->fetch();
    }

    public static function read($category=null)
    {
        if(isset($category)){
            $connection = DB::getInstance();
            $query = $connection->prepare('
            
                    select a.id, a.name
                    from manufacturer a
                    inner join product b on a.id=b.manufacturer
                    inner join category c on b.category=c.id
                    where b.category = :category
                    order by name asc
            
            ');
            $query->bindValue('category', $category);
        }else{
            $connection = DB::getInstance();
            $query = $connection->prepare('
            
                    select id, name
                    from manufacturer
                    order by name asc
            
            ');
        }
        $query->execute();
        return $query->fetchall();
    }
}