<?php 

class Registration
{

    public static function readOne($email)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
        
                select * from customer where email=:email
        
        ');
        $query->execute([
            'email'=>$email
        ]);

        $customer = $query->fetch();
        unset($customer->user_password);
        return $customer;
    }


        
}