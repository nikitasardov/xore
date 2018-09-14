<?php
/**
 * Created by IntelliJ IDEA.
 * User: oleg
 * Date: 13.09.18
 * Time: 10:20
 */

namespace Xore\db\adapters;

class MySQL
{
    protected $host;
    protected $db_name;
    protected $port;
    protected $username;
    protected $password;
    protected $link;

    //добавляем параметры для подключения
    public function __construct(String $host, String $db_name, String $port, String $username, String $password)
    {
        $this->host = $host;
        $this->db_name = $db_name;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->db_name, $this->port);
    }

    //получаем соеденение в базу
    public function getLink()
    {
        return $this->link;
    }

    public function disconnect()
    {
        mysqli_close($this->link);
    }
}