<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 12.09.18
 * Time: 12:50
 */
namespace Xore;

use Closure;
use Xore\db\adapters\MySQL;

require_once __DIR__.'/Route.php';
require_once __DIR__.'/Request.php';
require_once __DIR__.'/Log.php';
require_once __DIR__.'/db/Model.php';
require_once __DIR__.'/db/adapters/MySQL.php';

class App
{
    /**
     * тут будут массивы функций которы надо выполнить
     */
    protected $get = [];
    protected $put = [];
    protected $delete = [];
    protected $post = [];
    /**
     * результат работы
     */
    protected $result = null;
    /**
     * подключения к базам, доступно:
     * -MySQL
     */
    protected static $adapters = [];

    //конструктор будет определять метод запроса, и запускать соответстующий метод
    public function init()
    {
        //определяем метод
        $method = mb_strtolower($_SERVER['REQUEST_METHOD']);

        //определяем uri
        $uri = parse_url(mb_strtolower($_SERVER['REQUEST_URI']))['path'];

        //запускаем все экшен для этого метода
        if(count($this->$method) > 0){
            //ищем экшен который ответит нам именно по этому uri
            foreach ($this->$method as $endpoint_uri => $action){
                $route = new Route($endpoint_uri);
                if($route->test($uri)){
                    $this->result = $action(...$route->getParams());
                    break;
                }else{
                    $this->result = 'uri is bad';
                }
            }
        }else{
            $this->result = 'method not found';
        }

        //отключим все адаптеры
        $this->adaptersDisconnect();
    }

    //выключим соеденение с базой
    protected function adaptersDisconnect()
    {
        foreach (self::$adapters as $adapter){
            $adapter->disconnect();
        }
    }

    //получим результат для вывода на экран в формате JSON
    public function responseJSON() : String
    {
        $this->init();
        header('Content-Type: application/json');
        return json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    //получим результат для работы с ним в php
    public function getResult()
    {
        $this->init();
        return $this->result;
    }

    //добавим подключение к mysqli
    public static function setMySQL($host, $db_name, $port, $username, $password)
    {
        $mysql = new MySQL($host, $db_name, $port, $username, $password);
        self::$adapters['mysqli'] = $mysql;
    }

    //получим подключение к mysqli
    public static function getMySQL()
    {
        return empty(self::$adapters['mysqli']) ? null : self::$adapters['mysqli']->getLink();
    }

    //добавим экшены
    public function get(String $url,Closure $action)
    {
        $this->get[$url] = $action;
    }
    public function post(String $url,Closure $action)
    {
        $this->post[$url] = $action;
    }
    public function put(String $url,Closure $action)
    {
        $this->put[$url] = $action;
    }
    public function delete(String $url,Closure $action)
    {
        $this->delete[$url] = $action;
    }
}