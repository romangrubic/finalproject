<?php

class Dashboard
{
    // Takes user id as id
    public static function getOrders($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select *
            from shoppingorder
            where customer=:customerId and isFinished=1
            order by dateadded desc
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetchall();
    }

    public static function getOrderDetails($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select b.id, c.name, c.description,c.id as productId, c.price as productPrice, a.price, a.quantity
            from cart a
            inner join shoppingorder b on a.shoppingorder=b.id
            inner join product c on a.product=c.id
            where b.customer = :id and b.isFinished = 1
            
        ');
        $query->execute([
            'id' => $id
        ]);

        return $query->fetchall();
    }
}