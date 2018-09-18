<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 13.09.18
 * Time: 6:24
 */
namespace Xore;

use Xore\Uploads\Files;
use Xore\Uploads\RequestFile;

class Request
{
    /**
     * получить данные
     *
     * @param array|null $data
     * @param String|null $name
     * @param null $default
     * @return array|mixed|null
     */
    protected static function getData(array $data = null, string $name = null, $default = null)
    {
        if(!empty($name)){
            return $data[$name] ?? $default;
        }else{
            return $data ?? $default;
        }
    }

    /**
     * получить get
     * @param String|null $name
     * @param null $default
     * @return array|mixed|null
     */
    public static function get(string $name = null, $default = null)
    {
        return self::getData($_GET, $name, $default);
    }

    /**
     * получить post
     * @param String|null $name
     * @param null $default
     * @return array|mixed|null
     */
    public static function post(string $name = null, $default = null)
    {
        return self::getData($_POST, $name, $default);
    }

    /**
     * получить put
     * @return bool|string
     */
    public static function put()
    {
        return file_get_contents('php://input');
    }

    /**
     * получить один или все файлы
     * @param string|null $name
     * @return RequestFile|Files|null
     */
    public static function file(string $name = null)
    {
        $files = new Files();
        return empty($name) ? $files : $files->get($name);
    }
}