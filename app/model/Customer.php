<?php 

class Customer
{
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
}