<?php 

class Product
{

    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, b.id as categoryId, b.name as categoryName, a.price, a.inventoryquantity, c.imageurl as imageurl, a.dateadded
                from product a
                inner join category b on a.category=b.id
                inner join productimage c on c.product=a.id
                where a.id=:id
        
        ');
        $query->execute([
            'id'=>$id
        ]);
        return $query->fetch();
    }

    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select a.id, a.name, a.description, b.name as category, a.price, a.inventoryquantity, c.imageurl as imageurl, a.dateadded
                from product a
                inner join category b on a.category=b.id
                inner join productimage c on c.product=a.id
                order by a.id
        
        ');
        $query->execute();
        return $query->fetchall();
    }

    public static function create($parameters)
    {
        $connection = DB::getInstance();
        $connection->beginTransaction();
        $query = $connection->prepare('
        
                insert into product (name, description, category, price, inventoryquantity, dateadded)
                values (:name, :description, :category, :price, :inventoryquantity, now())
        
        ');
        $query->execute([
            'name'=>$parameters['name'],
            'description'=>$parameters['description'],
            'category'=>$parameters['category'],
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
            price=:price,
            inventoryquantity=:inventoryquantity
            where id=:id
                
        ');
        $query->execute([
            'name'=>$parameters['name'],
            'description'=>$parameters['description'],
            'category'=>$parameters['category'],
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