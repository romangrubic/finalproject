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
        
        select distinct a.id, a.firstname, a.lastname, a.city, a.lastOnline, b.id as orderId
        from customer a
        inner join shoppingorder b on a.id=b.customer
        inner join cart c on c.shoppingorder=b.id
        inner join product d on d.id=c.product
        inner join manufacturer e on d.manufacturer=e.id
        inner join category f on d.category=f.id
        where concat(a.city, e.name, f.name) like :search
        and b.isFinished = 0
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchColumn();
    }

    public static function readActiveOrderCustomers($search, $page)
    {
        $ppp = App::config('ppp');
        $from = $page * $ppp - $ppp;

        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select distinct a.id, a.firstname, a.lastname, a.city, a.lastOnline, b.id as orderId
        from customer a
        inner join shoppingorder b on a.id=b.customer
        inner join cart c on c.shoppingorder=b.id
        inner join product d on d.id=c.product
        inner join manufacturer e on d.manufacturer=e.id
        inner join category f on d.category=f.id
        where concat(a.city, e.name, f.name) like :search
        and b.isFinished = 0
        limit :from, :ppp
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->bindValue('from', $from, PDO::PARAM_INT);
        $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
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

    public static function byCategory()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.name, count(c.product) as quantity
        from category a
        left join product b on b.category=a.id
        left join cart c on c.product=b.id
		group by a.name
        order by quantity  desc

        ');

        $query->execute();
        return $query->fetchAll();
    }

    public static function byManufacturer()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.name, count(c.product) as quantity
        from manufacturer a
        left join product b on b.manufacturer=a.id
        left join cart c on c.product=b.id
		group by a.name
        order by quantity  desc

        ');

        $query->execute();
        return $query->fetchAll();
    }

    public static function byCity()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.city, count(c.product) as quantity
        from customer a
        inner join shoppingorder b on a.id=b.customer
        left join cart c on c.shoppingorder=b.id
		group by a.city
        order by quantity  desc

        ');

        $query->execute();
        return $query->fetchAll();
    }

    public static function mostSold()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.name, count(b.product) as quantity
        from product a
        left join cart b on b.product=a.id
		group by a.name
        order by quantity  desc
        limit 10

        ');

        $query->execute();
        return $query->fetchAll();
    }

    public static function lessSold()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select a.name, count(b.product) as quantity
        from product a
        left join cart b on b.product=a.id
		group by a.name
        order by quantity  asc
        limit 10

        ');

        $query->execute();
        return $query->fetchAll();
    }

    public static function totalFinishedOrder()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select count(id)
        from shoppingorder
        where isFinished = true
        ');

        $query->execute();
        return $query->fetchColumn();
    }

    public static function totalFinalizedOrder($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select count(distinct b.id)
        from customer a
        left join shoppingorder b on a.id=b.customer
        left join cart c on c.shoppingorder=b.id
        left join product d on d.id=c.product
        inner join manufacturer e on d.manufacturer=e.id
        inner join category f on d.category=f.id
        where concat(b.id, a.city, e.name, f.name) like :search
        and b.isFinished = true
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchColumn();
    }

    public static function readFinalizedOrderCustomers($search, $page)
    {
        $ppp = App::config('ppp');
        $from = $page * $ppp - $ppp;

        $connection = DB::getInstance();
        $query = $connection->prepare('
        
        select distinct a.id, a.firstname, a.lastname, a.city, a.lastOnline, b.id as orderId
        from customer a
        inner join shoppingorder b on a.id=b.customer
        inner join cart c on c.shoppingorder=b.id
        inner join product d on d.id=c.product
        inner join manufacturer e on d.manufacturer=e.id
        inner join category f on d.category=f.id
        where concat(b.id, a.city, e.name, f.name) like :search
        and b.isFinished = 1
        order by orderId desc
        limit :from, :ppp
        ');

        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->bindValue('from', $from, PDO::PARAM_INT);
        $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    public static function readFinalizedOrderDetails($id)
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

    public static function searchFinalized($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select city as name from customer
            where concat(city) like :search
            union
            select id as name from shoppingorder
            where concat(id) like :search
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