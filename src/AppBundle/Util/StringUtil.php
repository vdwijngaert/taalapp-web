<?php
/**
 * @author Koen Van den Wijngaert <koen@koenvandenwijngaert.be>
 * @scope taalapp
 */

namespace AppBundle\Util;

class StringUtil
{

    public static function randomString( $length = 10, $symbols = false )
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($symbols) {
            $characters .= '|@#{[^{}()%$*?';
        }
        $randomString = '';
        $strLn        = strlen( $characters );
        for ($i = 0; $i < $length; $i ++) {
            $randomString .= $characters[rand( 0, $strLn - 1 )];
        }

        return $randomString;
    }
}