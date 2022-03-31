<?php 

class Customer
{
    public static function readOne($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select *
                from customer
                where id=:id
        
        ');
        $query->execute(['id' => $id]);
        return  $query->fetch();
    }

    public static function getpassword($id)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select user_password
                from customer
                where id=:id
        
        ');
        $query->execute(['id' => $id]);
        return  $query->fetchColumn();
    }

    public static function updatepassword($id,$password)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                update customer
                set user_password=:password
                where id=:id
        
        ');
        $query->execute([
            'id'=>$id,
            'password'=>$password
        ]);
    }
    

    public static function read()
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select *
                from customer
        
        ');
        $query->execute();
        return  $query->fetch();
    }

    public static function insert($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                insert into customer (firstname, lastname, email, user_password, phonenumber, street, city, postalnumber, datecreated)
                values (:firstname, :lastname, :email, :user_password, :phonenumber, :street, :city, :postalnumber, now())
        
        ');
        $query->execute([
            'firstname'=>$paramaters['firstname'],
            'lastname'=>$paramaters['lastname'],
            'email'=>$paramaters['email'],
            'user_password'=>$paramaters['user_password'],
            'phonenumber'=>$paramaters['phonenumber'],
            'street'=>$paramaters['street'],
            'city'=>$paramaters['city'],
            'postalnumber'=>$paramaters['postalnumber']
        ]);
    } 
    
    public static function update($paramaters)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                
        update customer set 
        firstname=:firstname,
        lastname=:lastname,
        email=:email,
        phonenumber=:phonenumber,
        street=:street,
        city=:city,
        postalnumber=:postalnumber
        where id=:id
        ');
        
        $query->execute([
            'firstname'=>$paramaters['firstname'],
            'lastname'=>$paramaters['lastname'],
            'email'=>$paramaters['email'],
            'phonenumber'=>$paramaters['phonenumber'],
            'street'=>$paramaters['street'],
            'city'=>$paramaters['city'],
            'postalnumber'=>$paramaters['postalnumber'],
            'id'=>$paramaters['id'],
        ]);
    }  
}