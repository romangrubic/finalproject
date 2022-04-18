<?php 

class AdminCustomer
{
    public static function countCustomer($search)
    {
        $connection = DB::getInstance();
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
            'id'=>$id
        ]);
        return $query->fetch();
    }

    public static function read($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select distinct a.id, a.firstname, a.lastname, a.email, a.phonenumber, a.street, a.city, a.postalnumber, a.datecreated, a.lastOnline
        from customer a
        inner join shoppingorder b on a.id=b.customer
        inner join cart c on b.id=c.shoppingorder
        inner join product d on d.id=c.product
        inner join manufacturer e on e.id=d.manufacturer
        inner join category f on f.id=d.category
        where concat(a.firstname, a.lastname, a.city,d.name ,e.name, f.name) like :search
        ');
        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
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
        select name as name from product
        where concat(name) like :search
        union
        select lastname as name from customer
        where concat(lastname) like :search
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
}