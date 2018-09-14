<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 13.09.18
 * Time: 9:58
 */

namespace Xore;


class Log
{
    public static function dump($data, $filename = 'dump.log')
    {
        ob_start();
        var_dump($data);
        $string = str_replace("=&gt;", '=', strip_tags(ob_get_clean()));
        if (!is_dir(__DIR__.'/logs')) {
            mkdir(__DIR__.'/logs', 0777, true);
        }
        return file_put_contents(__DIR__."/logs/$filename", "$string\n", FILE_APPEND | LOCK_EX);
    }
}