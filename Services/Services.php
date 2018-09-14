<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 14.09.18
 * Time: 14:25
 */


namespace Xore;

require_once 'Curl.php';


class Services
{

    //получим cURL
    public static function getCurl()
    {
        return new \Curl();
    }
}