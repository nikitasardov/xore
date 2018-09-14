<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 13.09.18
 * Time: 10:12
 */
namespace Xore\db;

use Phalcon\Acl\Resource;
use Xore\App;

abstract class Model
{
    //подключение
    protected $link;

    //таблица
    protected $table;

    //таблица
    protected $result;

    //id последней записи
    protected $id;


    public function __construct(String $table)
    {
        $this->link = App::getMySQL();
        $this->table = $table;
    }

    /**
     * Выполняет запрос и возвращает результат
     * @param $sql
     * @return Resource
     */
    public function query($sql)
    {
        $this->result = mysqli_query($this->link, $sql);
        return $this->result;
    }

    /**
     * Экранирует спецсимволы в строке
     * @param $string
     * @return String
     */
    public function escape(String $string)
    {
        return $string;
    }

    /**
     * Создает запись и возвращает ее id
     * на вход
     * ассоциативный массив 'поле'=>'значение'
     * @param array $data
     * @return bool
     */
    public function insert(array $data)
    {
        $this->id = null;
        $keys = $values = [];
        if (!$data)
            return false;

        foreach ($data as $key => $value) {
            $values[] = $this->escape($value);
            $keys[] = $key;
        }
        $sql = "INSERT INTO `".$this->table."` (`" .
            join("`, `", $keys) .
            "`) VALUES ('" .
            join("', '", $values) . "')";
        if (false === $this->query($sql)){
            return false;
        }
        return $this->lastID();
    }

    /**
     * Возвращает значение id созданной записи
     */
    function lastID() {
        if (!$this->id) {
            $this->id = mysqli_insert_id($this->link);
        }
        return $this->id;
    }

    /**
     * Производит изменение записи и возвращает результат операции
     * на вход имя таблицы,
     * данные в виде ассоциативного массива 'поле'=>'значение',
     * условие в виде значения id или ассоциативного массива
     * @param array $data
     * @param $expr
     * @return bool|Resource
     */
    function update(array $data,array $expr) {
        $values = [];
        if (!$data){
            return false;
        }

        foreach ($data as $key => $val) {
            $val = $this->escape($val);
            $values[] = "`$key`='$val'";
        }

        $str = [];
        foreach ($expr as $key => &$val) {
            $val = $this->escape($val);
            $str[] = "`$key`='$val'";
        }
        $expr = join(' AND ', $str);

        $sql = "UPDATE `".$this->table."` SET " . join(", ", $values) . " WHERE $expr";
        return $this->query($sql);
    }

    /**
     * Производит удаление записи и возвращает результат операции
     * на вход
     * условие в виде ассоциативного массива
     * @param array $expr
     * @return int|Resource
     */
    function delete(array $expr) {
        if (empty($expr)){
            return 1;
        }

        $str = [];
        foreach ($expr as $key => &$val) {
            $val = $this->escape($val);
            $str[] = "`$key`='$val'";
        }
        $expr = join(' AND ', $str);
        return $this->query("DELETE FROM `".$this->table."` WHERE $expr");
    }

    /**
     * Выполняет запрос и возвращает запись в виде ассоциативного массива
     * если запрос выполнился неудачно или результат запроса пустой,
     * возвращается false
     * @param $sql
     * @return array|bool
     */
    protected function row(string $sql) {
        $res = $this->query($sql);
        if (false === $res)
            return false;
        return mysqli_fetch_assoc($res);
    }

    /**
     * Возвращает запись в виде ассоциативного массива
     * на вход
     * условие в виде ассоциативного массива,
     * список полей в виде ассоциативного массива
     * @param array $expr
     * @param array null $fields
     * @return array|bool
     */
    public function getRow(array $expr,array $fields = []) {

        $str = [];
        foreach ($expr as $key => $value) {
            $value = $this->escape($value);
            $str[] = "`$key`='$value'";
        }
        $expr = join(' AND ', $str);

        if (is_array($fields)){
            $fields = "`" . join("`, `", $fields) . "`";
        }elseif (!$fields){
            $fields = '*';
        }
        $sql = "SELECT $fields FROM `".$this->table."` WHERE $expr LIMIT 1";

        return $this->row($sql);
    }

    /**
     * Выполняет запрос и возвращает массив записей
     * если запрос выполнился неудачно или результат запроса пустой,
     * то возвращается false
     * @param string $sql
     * @return array|bool
     */
    public function getAll(string $sql) {
        $res = $this->query($sql);
        if (false === $res){
            return false;
        }
        $arr = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $arr[] = $row;
        }
        return $arr;
    }
}