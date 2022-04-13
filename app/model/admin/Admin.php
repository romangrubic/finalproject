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

    public static function activeOrders()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select count(a.id)
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            where isFinished = false
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function closedOrders()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select count(a.id)
            from shoppingorder a
            inner join cart b on a.id=b.shoppingorder
            where isFinished = true
            
        ');
        $query->execute();
        return  $query->fetchColumn();
    }
}