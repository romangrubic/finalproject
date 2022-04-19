<?php

class AdminCustomer
{
    public static function countCustomer($search, $negation)
    {
        $connection = DB::getInstance();
        if(!isset($negation) || $negation == '0'){
            $query = $connection->prepare('
        
                select count(distinct a.id)
                from customer a
                inner join shoppingorder b on a.id=b.customer
                inner join cart c on b.id=c.shoppingorder
                inner join product d on d.id=c.product
                inner join manufacturer e on e.id=d.manufacturer
                inner join category f on f.id=d.category
                where concat(a.firstname, a.lastname, a.city,d.name ,e.name, f.name) like :search
                
        ');
        }else{
            $query = $connection->prepare('
        
                select count(distinct a.id)
                from customer a
                inner join shoppingorder b on a.id=b.customer
                inner join cart c on b.id=c.shoppingorder
                inner join product d on d.id=c.product
                inner join manufacturer e on e.id=d.manufacturer
                inner join category f on f.id=d.category
                where concat(a.firstname, a.lastname, a.city,d.name ,e.name, f.name) not like :search
                
        ');
        }
        
        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return  $query->fetchColumn();
    }

    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select *
                from customer
                where id=:id
        ');
        $query->execute([
            'id' => $id
        ]);
        return $query->fetch();
    }

    public static function read($search, $negation, $page)
    {
        $ppp = App::config('ppp');
        $from = $page * $ppp - $ppp;

        $connection = DB::getInstance();
        if (!isset($search)) {
            $query = $connection->prepare('
            
                select distinct a.id, a.firstname, a.lastname, a.email, a.phonenumber, a.street, a.city, a.postalnumber, a.datecreated, a.lastOnline
                from customer a
                inner join shoppingorder b on a.id=b.customer
                inner join cart c on b.id=c.shoppingorder
                inner join product d on d.id=c.product
                inner join manufacturer e on e.id=d.manufacturer
                inner join category f on f.id=d.category
                order by a.id
                limit :from, :ppp
            ');
        } else {
            if($negation=='0'){
                $query = $connection->prepare('
            
                select distinct a.id, a.firstname, a.lastname, a.email, a.phonenumber, a.street, a.city, a.postalnumber, a.datecreated, a.lastOnline
                from customer a
                inner join shoppingorder b on a.id=b.customer
                inner join cart c on b.id=c.shoppingorder
                inner join product d on d.id=c.product
                inner join manufacturer e on e.id=d.manufacturer
                inner join category f on f.id=d.category
                where concat(a.firstname, a.lastname, a.city,d.name ,e.name, f.name) like :search
                limit :from, :ppp

            ');
            }else {
                $query = $connection->prepare('
            
                select a.id, a.firstname, a.lastname, a.email, a.phonenumber, a.street, a.city, a.postalnumber, a.datecreated,
                 a.lastOnline, d.name, e.name, f.name 
                from customer a
                inner join shoppingorder b on a.id=b.customer
                inner join cart c on b.id=c.shoppingorder
                inner join product d on d.id=c.product
                inner join manufacturer e on e.id=d.manufacturer
                inner join category f on f.id=d.category
                where concat(a.firstname, a.lastname, a.city,d.name ,e.name, f.name) not like :search
                group by a.id
                limit :from, :ppp

            ');
            }
            $search = '%' . $search . '%';
            $query->bindParam('search', $search);
        }
        $query->bindValue('from', $from, PDO::PARAM_INT);
        $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchall();
    }

    // Method for autocomplete functionality
    public static function searchCustomer($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select city as name from customer
        where concat(city) like :search
        union
        select firstname as name from customer
        where concat(firstname) like :search
        union
        select lastname as name from customer
        where concat(lastname) like :search
        limit 15
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchAll();
    }
}
