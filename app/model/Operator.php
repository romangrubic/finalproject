<?php

class Operator
{
    public static function authorize($email, $password)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
            select * from operator where email=:email
        ');
        $query->execute(['email' => $email]);

        $operator = $query->fetch();

        if ($operator == null) {
            return null;
        }

        if (password_verify($password, $operator->user_password)){
            return null;
        }

        // Removing password from session
        unset($operator->user_password);
        return $operator;
    }
}