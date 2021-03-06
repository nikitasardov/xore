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
    /**
     * пишет в лог файл
     * @param $data
     * @param string $filename
     * @return bool|int
     */
    public static function dump($data, $filename = 'dump.log')
    {
        ob_start();
        var_dump($data);
        $string = str_replace("=&gt;", '=', strip_tags(ob_get_clean()));
        if (!is_dir(__ROOT__.'/logs')) {
            mkdir(__ROOT__.'/logs', 0777, true);
        }
        return file_put_contents(__ROOT__."/logs/$filename", "$string\n", FILE_APPEND | LOCK_EX);
    }
}