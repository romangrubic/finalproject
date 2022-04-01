<?php


class Product
{
    public static function totalProducts($search, $category, $manufacturer)
    {
        $connection = DB::getInstance();
        if (!isset($category)) {
            if(isset($manufacturer)){
                $query = $connection->prepare('
            
                select count(id)
                from product             
                where manufacturer =:manufacturer
                ');
                $query->bindParam('manufacturer', $manufacturer);
            }else{
                $query = $connection->prepare('
            
                select count(a.id)
                from product a
                left join productimage b on a.id=b.product
                inner join manufacturer c on a.manufacturer=c.id   
                inner join category d on a.category=d.id        
                where concat(a.name, \' \', ifnull(a.description, \' \'),c.name, d.name) like :search
            ');

            $search = '%' . $search . '%';
            $query->bindParam('search', $search);
            }
        }else{
            if(isset($manufacturer)){
                $query = $connection->prepare('
            
                    select count(id)
                    from product
                    where manufacturer = :manufacturer
                    and category = :category
                ');
                $query->bindParam('manufacturer', $manufacturer);
                $query->bindParam('category', $category);
            }else{
                $query = $connection->prepare('
                        select count(id)
                        from product               
                        where category = :category
                    ');
                $query->bindParam('category', $category);
            }
        }  
        $query->execute();
        return $query->fetchColumn();
    }

    // Method for autocomplete functionality
    public static function searchProduct($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select name from product
            where name like :search
            union
            select name from manufacturer
            where name like :search
            order by name limit 20
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchAll();
    }

    public static function read($category, $search, $page, $manufacturer)
    {
        $ppp = App::config('ppp');
        $from = $page * $ppp - $ppp;

        $connection = DB::getInstance();
        if (!isset($category)) {
            if(isset($manufacturer)){
                $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product             
                where manufacturer = :manufacturer
                limit :from, :ppp
            ');
            $query->bindParam('manufacturer', $manufacturer);

            }else{
                $query = $connection->prepare('
        
                select a.id, a.name,c.name as manufacturer, a.description, d.name as category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product
                inner join manufacturer c on a.manufacturer=c.id   
                inner join category d on a.category=d.id        
                where concat(a.name, \' \', ifnull(a.description, \' \'),c.name, d.name) like :search
                limit :from, :ppp
            ');
            $search = '%' . $search . '%';
            $query->bindParam('search', $search);
            }
        }else{
            if(isset($manufacturer)){
                $query = $connection->prepare('
            
                    select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                    from product a
                    left join productimage b on a.id=b.product             
                    where manufacturer = :manufacturer
                    and category=:category
                    limit :from, :ppp
                ');
                $query->bindParam('manufacturer', $manufacturer);
                $query->bindParam('category', $category);
            }else{
                $query = $connection->prepare('
            
                    select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                    from product a
                    left join productimage b on a.id=b.product             
                    where category = :category
                    limit :from, :ppp
                ');
                $query->bindParam('category', $category);
            }       
        }
        $query->bindValue('from', $from, PDO::PARAM_INT);
        $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll();
    }
}

