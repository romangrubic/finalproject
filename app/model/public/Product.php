<?php


class Product
{
    public static function totalProducts($search, $category)
    {
        $connection = DB::getInstance();
        if (!isset($category)) {
            $query = $connection->prepare('
            
                    select count(a.id)
                    from product a
                    left join productimage b on a.id=b.product                
                    where concat(a.name, \' \', ifnull(a.description, \' \')) like :search
                ');

            $search = '%' . $search . '%';
            $query->bindParam('search', $search);
            $query->execute();
        }else{
            $query = $connection->prepare('
            
                    select count(a.id)
                    from product a
                    left join productimage b on a.id=b.product                
                    where a.category = :category
                ');

            // $search = '%' . $search . '%';
            $query->bindParam('category', $category);
            $query->execute();
        }
        return $query->fetchColumn();
    }

    public static function read($category, $search, $page)
    {
        $ppp = App::config('ppp');
        $from = $page * $ppp - $ppp;

        $connection = DB::getInstance();
        if (!isset($category)) {
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product                
                where concat(a.name, \' \', ifnull(a.description, \' \')) like :search
                limit :from, :ppp
            ');

            $search = '%' . $search . '%';
            $query->bindParam('search', $search);
            $query->bindValue('from', $from, PDO::PARAM_INT);
            $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
            $query->execute();
        } else {
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, a.category, a.price, b.imageurl as imageurl
                from product a
                left join productimage b on a.id=b.product
                where a.category = :category
                limit :from, :ppp
            ');
            $query->bindParam('category', $category);
            $query->bindValue('from', $from, PDO::PARAM_INT);
            $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
            $query->execute();
        }
        return $query->fetchAll();
    }
}
