<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 13.09.18
 * Time: 6:24
 */
namespace Xore;

class Request
{
    //получить данные
    protected static function getData(Array $data = null,String $name = null, $default = null)
    {
        if(!empty($name)){
            return $data[$name] ?? $default;
        }else{
            return $data ?? $default;
        }
    }

    //получить get
    public static function get(String $name = null, $default = null)
    {
        return self::getData($_GET, $name, $default);
    }

    //получить post
    public static function post(String $name = null, $default = null)
    {
        return self::getData($_POST, $name, $default);
    }

    //получить put
    public static function put()
    {
        return file_get_contents('php://input');
    }
}