<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 14.09.18
 * Time: 14:27
 */

class Curl
{
    protected $ch;
    //url на который будет запрос
    protected $url;
    //данные
    protected $data = [];
    //последний результат
    protected $result;
    //последний статус
    protected $status;
    //ошибка
    protected $err;


    public function __construct()
    {
        $this->ch = curl_init();
    }


    /**
     * указываем url
     * @param String $url
     * @return Curl
     */
    public function setUrl(String $url) : Curl
    {
        $this->url = $url;
        return $this;
    }

    /**
     * указываем данные
     * @param array $data
     * @return Curl
     */
    public function setData(Array $data) : Curl
    {
        $this->data = $data;
        return $this;
    }

    /**
     * отправляем post
     * @return mixed
     */
    public function post()
    {
        //установим url
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        //выводить на экран результат не будем
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        // указываем, что у нас POST запрос
        curl_setopt($this->ch, CURLOPT_POST, 1);
        // добавляем данные
        if(count($this->data) > 0){
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->data);
        }
        //сохраним результат
        $this->result = curl_exec($this->ch);
        //сохраним статус
        $this->status = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        //закрыли соеденение
        curl_close($this->ch);

        //у нас ошибка?
        if ($this->result === false) {
            $this->err = curl_error($this->ch);
        }
        return $this->getResult();
    }

    /**
     * отправляем get
     * @return mixed
     */
    public function get()
    {
        // добавляем данные
        $params = '';
        if(count($this->data) > 0){
            $params = '?'.http_build_query($this->data);
        }
        //установим url
        curl_setopt($this->ch, CURLOPT_URL, $this->url.$params);
        //выводить на экран результат не будем
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        //сохраним результат
        $this->result = curl_exec($this->ch);
        //сохраним статус
        $this->status = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        //закрыли соеденение
        curl_close($this->ch);

        //у нас ошибка?
        if ($this->result === false) {
            $this->err = curl_error($this->ch);
        }
        return $this->getResult();
    }

    /**
     * получим результат
     * @return mixed
     */
    public function getResult()
    {
        $result = json_decode($this->result, true);
        return $result ?? $this->result;
    }

    /**
     * получим статус
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * получим ошибку
     * @return mixed
     */
    public function getError()
    {
        return $this->err;
    }
}