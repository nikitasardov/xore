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

//инструменты
require_once __DIR__.'/Route.php';
require_once __DIR__.'/Request.php';
require_once __DIR__.'/Log.php';
require_once __DIR__.'/Response.php';

//работа с БД
require_once __DIR__.'/db/Model.php';
require_once __DIR__.'/db/adapters/MySQL.php';

//сервисы
require_once __DIR__.'/Services/Services.php';

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
     * если экшен не найден
     */
    protected $notFound = [
        'code' => 404,
        'response' => null
    ];
    /**
     * Ответ для xhr запросов
     */
    protected $response = null;
    /**
     * подключения к базам, доступно:
     * -MySQL
     */
    protected static $adapters = [];

    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * конструктор будет определять метод запроса, и запускать соответстующий метод
     */
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
                    //not found
                    $this->response->setCode($this->notFound['code']);
                    $this->result = $this->notFound['response'];
                }
            }
        }else{
            //not found
            $this->response->setCode($this->notFound['code']);
            $this->result = $this->notFound['response'];
        }
        //отключим все адаптеры
        $this->adaptersDisconnect();
    }


    /**
     * выключим соеденение с базой
     */
    protected function adaptersDisconnect()
    {
        foreach (self::$adapters as $adapter){
            $adapter->disconnect();
        }
    }

    /**
     * получим объект Response
     * для последующих настроек
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * выводит на экран результат экшена
     * результат выполнения экшена
     */
    public function responseJSON()
    {
        $this->init();
        $this->response
            ->setContentType('application/json')
            ->setContent(json_encode($this->result, JSON_UNESCAPED_UNICODE))
            ->end();
    }

    /**
     * получим результат для работы с ним в php
     * @return mixed
     */
    public function getResult()
    {
        $this->init();
        return $this->result;
    }

    /**
     * добавим подключение к mysqli
     * @param string $host
     * @param string $db_name
     * @param string $port
     * @param string $username
     * @param string $password
     */
    public static function setMySQL(string $host, string $db_name, string $port, string $username, string $password)
    {
        $mysql = new MySQL($host, $db_name, $port, $username, $password);
        self::$adapters['mysqli'] = $mysql;
    }

    /**
     * получим подключение к mysqli
     * @return mixed
     */
    public static function getMySQL()
    {
        return empty(self::$adapters['mysqli']) ? null : self::$adapters['mysqli']->getLink();
    }

    /**
     * данные, которые будут выданы, если
     * не один route не подошел
     * @param array $response
     * @param int $code
     */
    public function notFound(array $response, int $code = 404)
    {
        $this->notFound = [
            'response' => $response,
            'code' => $code
        ];
    }

    /**
     * добавим экшены get запроса
     * @param String $url
     * @param Closure $action
     */
    public function get(String $url, Closure $action)
    {
        $this->get[$url] = $action;
    }

    /**
     * добавим экшены post запроса
     * @param String $url
     * @param Closure $action
     */
    public function post(String $url,Closure $action)
    {
        $this->post[$url] = $action;
    }

    /**
     * добавим экшены put запроса
     * @param String $url
     * @param Closure $action
     */
    public function put(String $url,Closure $action)
    {
        $this->put[$url] = $action;
    }

    /**
     * добавим экшены delete запроса
     * @param String $url
     * @param Closure $action
     */
    public function delete(String $url,Closure $action)
    {
        $this->delete[$url] = $action;
    }
}