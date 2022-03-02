<?php

class Operator
{
    public static function authorize($email, $password)
    {
        $connection = DB::getInstance();
        $query = $connection->prepare('
            select * from operater where email=:email
        ');
        $query->execute(['email' => $email]);

        $operator = $query->fetch();

        if ($operator == null) {
            return null;
        }

        if (password_verify($password, $operator->user_password)){
            return null;
        }

        // Micemo lozinku iz operatera da ju ne spremimo u SESSION
        unset($operator->user_password);
        return $operator;
    }
}