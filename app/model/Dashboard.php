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
            where customer=:customerId
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetchall();
    }
}