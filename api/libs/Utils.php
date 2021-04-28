<?php
class Utils 
{
    //Generate password
    public static function generatePassword($lenght = 10) 
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for($i = 0; $i < $lenght; $i++) {
            $string .= $chars[rand(0, strlen($chars) - 1)];
        }

        return $string;
    }
}