<?php

class Admin
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

    public static function last2weeks()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select count(*)
                from customer
                where datecreated > (select DATE_SUB(curdate(), INTERVAL 2 WEEK))
        
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function activeCustomers()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select count(*)
                from customer
                where date(lastOnline) > DATE(DATE_SUB(curdate(), INTERVAL 1 day))
        
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function activeOrders()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select count(id)
            from shoppingorder
            where isFinished = false
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function closedOrders()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select count(id)
            from shoppingorder 
            where isFinished = true
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function sumTotal()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select sum(b.price*b.quantity) as number
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            where a.isFinished = 1
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function totalProducts()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select count(id)
            from product
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function activeProducts()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select count(a.id)
        from product a
        inner join cart b on a.id=b.product
        inner join shoppingorder c on b.shoppingorder=c.id
        where c.isFinished = false
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }
}