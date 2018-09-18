<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 17.09.18
 * Time: 20:30
 */
namespace Xore\Uploads;

class RequestFile
{
    /**
     * данные о файле
     */
    protected $data = array(
        'name' => '',
        'type' => '',
        'size' => 0,
        'tmp_name' => null,
        'error' => 'no file uploaded',
    );

    /**
     * RequestFile constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        if ($data === null) {
            $this->data = null;
            return;
        }

        $this->setData($data['name'], $data['type'], $data['size'], $data['tmp_name'], $data['error']);
    }

    /**
     * установим значения
     *
     * @param $name
     * @param $type
     * @param $size
     * @param $tmp_name
     * @param $error
     */
    protected function setData($name, $type, $size, $tmp_name, $error)
    {
        if (!is_int($error)) {
            $this->data['error'] = 'File error code must be integer.';
        }
        if ($error == UPLOAD_ERR_OK) {
            if (!is_string($name)) {
                $this->data['error'] = 'File input name must be string.';
            }
            if (!is_string($type)) {
                $this->data['error'] = 'File type must be string.';
            }
            if (!is_int($size)) {
                $this->data['error'] = 'File size must be integer.';
            }
            if (!is_string($tmp_name)) {
                $this->data['error'] = 'File tmp_name must be string.';
            }
            if (!is_uploaded_file($tmp_name)) {
                $this->data['error'] = 'Possible file upload attack: '.$tmp_name;
            } elseif (!file_exists($tmp_name)) {
                $this->data['error'] = 'No such file ($tmp_name): '.$tmp_name;
            }
        }
        $this->data = array(
            'name' => $name,
            'type' => $type,
            'size' => $size,
            'tmp_name' => $tmp_name,
            'error' => $error,
        );
    }

    /**
     * проверка успешной загрузки
     * @return bool
     */
    public function uploaded() : bool
    {
        return ($this->data !== null) && !$this->data['error'];
    }

    /**
     * сохраняем файл в
     * приватную или публичную
     * область
     * @param bool $public
     * @param string $dir
     * @param null $name
     * @return bool
     */
    public function save(bool $public, string $dir, $name = null)
    {
        $dir = self::validPath($dir);
        //генерируем путь
        $path = __ROOT__.'/data/'.($public ? 'public' : 'private').'/'.$dir;
        //создаём папку если такой нет
        if(!is_dir($path)) mkdir($path, 0777, true);
        //переносим файл
        if (is_uploaded_file($this->data['tmp_name'])) {
            return move_uploaded_file($this->data['tmp_name'], $this->concatFullPath($path, $name));
        } else {
            return rename($this->data['tmp_name'], $this->concatFullPath($path, $name));
        }
    }


    /**
     * переносит файл из tmp
     * @param $dir
     * @param $name
     * @return string
     */
    protected function concatFullPath($dir, $name)
    {
        if ($this->data === null) {
            $this->data['error'] = 'No file uploaded.';
        }
        if (!is_uploaded_file($this->data['tmp_name']) || !file_exists($this->data['tmp_name'])) {
            $this->data['error'] = 'Temporary file does not exist anymore.';
        }
        if ($name === null) {
            return $dir.'/'.$this->data['name'];
        } else {
            $lastSym = substr($dir, -1);
            if ($lastSym == '/' || $lastSym == '\\') {
                return $dir.'/'.$this->data['name'];
            } else {
                return $dir.'/'.$this->data['name'];
            }
        }
    }

    /**
     * убирает лишние слеши из строки
     * @param string $dir
     * @return string
     */
    public static function validPath(string $dir) : string
    {
        $paths = explode('/', $dir);
        $new_paths = [];
        foreach ($paths as $path){
            if(!empty($path)){
                $new_paths[] = $path;
            }
        }unset($path,$paths);
        return implode('/', $new_paths);
    }
}