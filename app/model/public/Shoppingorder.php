<?php

class Shoppingorder
{
    // Checks if current user has a shopping order created already
    public static function getShoppingorder($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select id, customer, dateadded 
            from shoppingorder 
            where isFinished = 0 and customer=:customerId 
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetch();
    }

    // if not, Controller will make a new one, otherwise will take its id
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
    public static function addtocart($product, $shoppingorderId, $quantity)
    {
        $connection = DB::getInstance();

        $query = $connection->prepare('

            select a.quantity
            from cart as a
            inner join shoppingorder as b on a.shoppingorder = b.id
            where a.product = :product and b.id = :shoppingorderId
            
        ');
        $query->execute([
            'product' => $product,
            'shoppingorderId' => $shoppingorderId
        ]);

        // Cehcks if product already exists in cart and its quantity
        $existsInCart = $query->fetchColumn();

        if($existsInCart == 0){
            $query = $connection->prepare('

            insert into cart (shoppingorder, product, price, quantity, dateadded) values
            (:shoppingorderId, :product, (select price from product where id = :product), 1, now())
            
            ');
            return $query->execute([
                'product' => $product,
                'shoppingorderId' => $shoppingorderId
            ]);
        }else{
            $query = $connection->prepare('

            update cart a
            inner join shoppingorder as b on a.shoppingorder=b.id
            set a.quantity = a.quantity+1
            where product= :product and b.id= :shoppingorderId
            
            ');
            return $query->execute([
                'product' => $product,
                'shoppingorderId' => $shoppingorderId
            ]);
        }
        // We need to return for our check in js
    }

    // Remove product from cart
    public static function removefromcart($product, $shoppingorderId)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            delete from cart 
            where product = :product and shoppingorder = :shoppingorderId
            
        ');
        return $query->execute([
            'product' => $product,
            'shoppingorderId' => $shoppingorderId
        ]);

        // We need to return for our check in js
    }

    // Gets shopping cart with products
    public static function getShoppingorderCart($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select a.id as orderId,c.id as id, c.name, c.description, b.price, b.quantity, b.dateadded, d.imageurl as imageurl
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            inner join product c on b.product=c.id
            inner join productimage d on c.id=d.product
            where a.isFinished = 0 and a.customer = :customerId
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetchAll();
    }

    // Number of products in cart for badge
    public static function numberOfProducts($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('

            select sum(b.quantity) as number
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            where a.isFinished = 0 and a.customer = :customerId
            
        ');
        $query->execute([
            'customerId' => $id
        ]);

        return $query->fetchColumn();
    }

}