<?php


class Order
{
    public static function totalCustomers()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select count(*)
                from customer
        
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function searchActive($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select city as name from customer
            where concat(city) like :search
            union
            select name from manufacturer
            where concat(name) like :search
            union
            select name from category
            where concat(name) like :search
            limit 15
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchAll();
    }

    public static function totalActiveOrder($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select distinct count(a.id)
        from customer a
        inner join shoppingorder b on a.id=b.customer
        where concat(a.firstname, a.lastname, a.city) like :search
        and b.isFinished = 0
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchColumn();
    }

    public static function readActiveOrderCustomers($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.id, a.firstname, a.lastname, a.city, a.lastOnline, b.id as orderId
        from customer a
        inner join shoppingorder b on a.id=b.customer
        where concat(a.firstname, a.lastname, a.city) like :search
        and b.isFinished = 0
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchAll();
    }

    public static function readOrderDetails($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select c.name, c.description,c.id as productId, c.price as productPrice, a.price, a.quantity
        from cart a
        inner join shoppingorder b on a.shoppingorder=b.id
        inner join product c on a.product=c.id
        where b.id = :id
        ');

        $query->execute([
            'id'=>$id
        ]);
        return $query->fetchAll();
    }


}