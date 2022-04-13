<?php

class Login
{
    public static function authorize($email, $password)
    {
        $connection = DB::getInstance();

        // Maybe it's admin or operator role
        $query = $connection->prepare('
        select * from operator where email=:email
        ');
        $query->execute(['email' => $email]);

        $operator = $query->fetch();

        // Looking into customer.email
        if ($operator == null) {
            $connection->beginTransaction();
            $query = $connection->prepare('
                select * from customer where email=:email
            ');
            $query->execute(['email' => $email]);

            $operator = $query->fetch();

            $query = $connection->prepare('
                update customer set 
                lastOnline =now()
                where email=:email
            ');
            $query->execute(['email' => $email]);

            $connection->commit();
        }

        // If not customer, admin or operator, return null
        if ($operator == null){
            return null;
        }

        if (!password_verify($password, $operator->user_password)){
            return null;
        }

        // Removing password from session
        unset($operator->user_password);
        return $operator;
    }
}