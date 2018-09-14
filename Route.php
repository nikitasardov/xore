<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 12.09.18
 * Time: 16:23
 */
namespace Xore;

class Route
{
    //введёный uri эндпойнта
    protected $custom_uri = null;
    //точки соприкосновения
    protected $const_path = [];
    //параметры из uri
    protected $params = [];
    //количество частей в uri
    protected $count_path = 0;

    public function __construct($uri)
    {
        //сохраним ведёный uri
        $this->custom_uri = $uri;
        //разделим uri на части
        $paths = explode('/', $uri);
        //посчитаем колличество частей
        $this->count_path = count($paths);
        //переберём части
        foreach ($paths as $index => $path){
            //если в части есть { то это параметр
            if(stristr($path,'{')){
                //запишем название параметра
                $this->params[$index] = null;
            }else{
                //запишем константу под её номером
                $this->const_path[$index] = $path;
            }
        }
    }

    //проверим, uri схожи или нет
    public function test($uri) : bool
    {
        //разделим введёный uri на части
        $paths = explode('/', $uri);

        //проверим, совпадают ли они на 100%
        $is_active = $uri === $this->custom_uri;

        //если они совпали, дальнейшая проверка и не нужна
        if($is_active === false){
            //переберём точки сопрекоснавения что бы проверить
            //наш ли это uri
            foreach ($this->const_path as $index => $path){
                //если один из пунктов не сходиться
                //то это не наш uri
                if($paths[$index] !== $path){
                    $is_active = false;
                    break;
                }
            }
            //если количетво частей uri одинакого
            //и все части сошлись, то это наш uri
            $is_active = $is_active ? $this->count_path === count($paths) : false;
        }

        if($is_active){
            foreach ($this->params as $index => $param){
                $this->params[$index] = $paths[$index];
            }
            return true;
        }else{
            return false;
        }
    }

    //получим параметры из uri
    public function getParams() : array
    {
        return $this->params;
    }
}