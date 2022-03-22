<?php

class Dashboard
{
    // Takes user id as id
    public static function getOrders($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select a.id, a.dateadded, c.id, c.name, b.price, b.quantity
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            inner join product c on b.product=c.id
            where customer=:customerId
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetchall();
    }
}