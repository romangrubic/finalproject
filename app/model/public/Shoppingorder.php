<?php

class Shoppingorder
{
    // Checks if current user has a shopping order created already
    public static function getShoppingorder($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select a.id,c.name, c.description, b.price, b.quantity, b.dateadded
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            inner join product c on b.product=c.id
            where a.isFinished = 0 and a.customer = :customerId
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetchAll();
    }

    // if not, Controller will make a new one, otherwise will take it's id
    public static function create($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            insert into shoppingorder (customer, dateadded, isFinished) values
            (:customerId, now(), false)
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

    }

    // Adds product into cart
    public static function addtocart($product, $shoppingorderId)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            insert into cart (shoppingorder, product, price, quantity, dateadded) values
            (:shoppingorderId, :product, (select price from product where id = :product), 1, now())
            
        ');
        $query->execute([
            'product' => $product,
            'shoppingorderId' => $shoppingorderId
        ]);

    }
}