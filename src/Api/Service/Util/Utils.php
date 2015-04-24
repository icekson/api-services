<?php

/**
 * @author a.itsekson
 */


namespace Api\Service\Util;

class Utils{

    public static function generateToken(){
        return md5(microtime() . rand(0,99999999));
    }

    public static function generateSalt($n=20)
    {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
        $counter = strlen($pattern)-1;
        for($i=0; $i<$n; $i++)
        {
            $key .= $pattern{rand(0,$counter)};
        }
        return $key;
    }
    public static function normalizeName($name, $ucfirst = false)
    {
        $filtered = str_replace(array('-', '_'), ' ', (string) $name);
        $filtered = ucwords(strtolower($filtered));
        $filtered = str_replace(' ', '', $filtered);
        if($ucfirst === false){
            $first = substr($filtered, 0, 1);
            $string = substr($filtered, 1, strlen($filtered)-1);
            $first = strtolower($first);
            $filtered = $first . $string;
        }
        return $filtered;
    }
}