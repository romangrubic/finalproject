<?php

class Login
{
    public static function authorize($email, $password)
    {
        $connection = DB::getInstance();

        // Looking into customer.email first
        $query = $connection->prepare('
            select * from customer where email=:email
        ');
        $query->execute(['email' => $email]);

        $operator = $query->fetch();

        // Maybe it's admin or operator role
        if ($operator == null) {
            $query = $connection->prepare('
            select * from operator where email=:email
            ');
            $query->execute(['email' => $email]);

            $operator = $query->fetch();
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