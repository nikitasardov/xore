<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 17.09.18
 * Time: 21:24
 */

namespace Xore\Uploads;

require_once 'RequestFile.php';
require_once 'Image.php';


class Files
{

    /**
     * загруженые файлы
     */
    protected $files = [];

    /**
     * Files constructor.
     */
    public function __construct()
    {
        foreach ($_FILES as $input_name => $file){
            $this->files[$input_name] = new RequestFile($_FILES[$input_name]);
        }
    }

    /**
     * получить колличество загруженых файлов
     * @return int
     */
    public function count() : int
    {
        return count($this->files);
    }

    /**
     * получить один файл по имени поля
     * @param string $input_name
     * @return RequestFile | null
     */
    public function get(string $input_name)
    {
        return $this->files[$input_name] ?? null;
    }

    /**
     * сохранит все загруженые файлы
     * @param bool $public
     * @param string $dir
     * @return string
     */
    public function save(bool $public, string $dir) : string
    {
        $dir = RequestFile::validPath($dir);
        $path = __ROOT__.'/data/'.($public ? 'public' : 'private').'/'.$dir;
        foreach ($this->files as $file){
            if($file->uploaded()){
                $file->save($public, $dir);
            }
        }
        return $path;
    }
}