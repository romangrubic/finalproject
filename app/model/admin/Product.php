<?php 

class Product
{

    public static function totalProducts($search)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
            select count(a.id)
            from product a
            left join productimage b on a.id=b.product
            inner join manufacturer c on a.manufacturer=c.id   
            inner join category d on a.category=d.id        
            where concat(a.id, a.name, \' \', ifnull(a.description, \' \'),c.name, d.name) like :search
            
        ');
        $search = '%' . $search . '%';
        $query->bindParam('search', $search);
        $query->execute();
        return $query->fetchColumn();
    }

    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, b.id as category, b.name as categoryName, d.id as manufacturer, d.name as manufacturerName, a.price, a.inventoryquantity, c.imageurl as imageurl, a.dateadded
                from product a
                inner join category b on a.category=b.id
                left join productimage c on c.product=a.id
                inner join manufacturer d on a.manufacturer=d.id
                where a.id=:id
        
        ');
        $query->execute([
            'id'=>$id
        ]);
        return $query->fetch();
    }

    public static function read($search,$page)
    {
        $ppp = App::config('ppp');
        $from = $page * $ppp - $ppp;

        $connection = DB::getInstance();
        if(!isset($search)){
            $query = $connection->prepare('
        
                select a.id, a.name, a.description, b.name as category,d.name as manufacturer, a.price, a.inventoryquantity, c.imageurl as imageurl, a.dateadded, a.lastUpdated
                from product a
                inner join category b on a.category=b.id
                left join productimage c on c.product=a.id
                inner join manufacturer d on a.manufacturer=d.id
                order by a.id
                limit :from, :ppp
        
            ');
        }else{
            $query = $connection->prepare('

                select a.id, a.name, a.description, b.name as category,d.name as manufacturer, a.price, a.inventoryquantity, c.imageurl as imageurl, a.dateadded, a.lastUpdated
                from product a
                inner join category b on a.category=b.id
                left join productimage c on c.product=a.id
                inner join manufacturer d on a.manufacturer=d.id     
                where concat(a.id, a.name, \' \', ifnull(a.description, \' \'),b.name, d.name) like :search
                limit :from, :ppp
            ');
            $search = '%' . $search . '%';
            $query->bindParam('search', $search);
        }
        $query->bindValue('from', $from, PDO::PARAM_INT);
        $query->bindValue('ppp', $ppp, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchall();
    }

    public static function create($parameters)
    {
        print_r($parameters);
        $connection = DB::getInstance();
        $connection->beginTransaction();
        $query = $connection->prepare('
        
                insert into product (name, description, category,manufacturer, price, inventoryquantity, dateadded, lastUpdated)
                values (:name, :description, :category,:manufacturer, :price, :inventoryquantity, now(), now())
        
        ');
        $query->execute([
            'name'=>$parameters['name'],
            'description'=>$parameters['description'],
            'category'=>$parameters['category'],
            'manufacturer'=>$parameters['manufacturer'],
            'price'=>$parameters['price'],
            'inventoryquantity'=>$parameters['inventoryquantity'],
        ]);

        $lastProductId=$connection->lastInsertId();

        $query = $connection->prepare('
        
                insert into productimage (product, imageurl, dateadded)
                values (:product, :imageurl, now())
        
        ');
        $query->execute([
            'product'=>$lastProductId,
            'imageurl'=>$parameters['imageurl']
            
        ]);

        $connection->commit();
    }

    public static function update($parameters)
    {
        $connection = DB::getInstance();
        $connection->beginTransaction();
        $query = $connection->prepare('
        
            update product set
            name=:name,
            description=:description,
            category=:category,
            manufacturer=:manufacturer,
            price=:price,
            inventoryquantity=:inventoryquantity,
            lastUpdated=now()
            where id=:id
                
        ');
        $query->execute([
            'name'=>$parameters['name'],
            'description'=>$parameters['description'],
            'category'=>$parameters['category'],
            'manufacturer'=>$parameters['manufacturer'],
            'price'=>$parameters['price'],
            'inventoryquantity'=>$parameters['inventoryquantity'],
            'id'=>$parameters['id'],
        ]);

        $query = $connection->prepare('
        
            update productimage set
            imageurl=:imageurl
            where product=:product
        
        ');
        $query->execute([
            'product'=>$parameters['id'],
            'imageurl'=>$parameters['imageurl']
            
        ]);

        $connection->commit();
    }

    public static function delete($id)
    {
        $connection = DB::getInstance();
        $connection->beginTransaction();
        $query = $connection->prepare('
        
            delete from productimage where product=:id
                
        ');
        $query->execute([
            'id'=>$id
        ]);

        $query = $connection->prepare('
        
            delete from product where id=:id
        
        ');
        $query->execute([
            'id'=>$id
        ]);

        $connection->commit();
    }
}