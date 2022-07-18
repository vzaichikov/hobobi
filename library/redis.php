<?php
namespace hobotix;

final class hoboRedis {
    private $socket = '/var/run/redis/redis.sock';
    private $host   = '127.0.0.1';
    private $port   = '6379';
    private $db     = null;

    private $redis = null;

    public function __construct($db = 7){
        $this->db = $db;

        $this->redis = new \Redis();

        if ($this->redis->pconnect($this->socket)){
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            $this->redis->select($db);
        } elseif($this->redis->pconnect($this->host, $this->port)) {
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            $this->redis->select($db);
        }
    }

    public function get($key){
        return $this->redis->get($key);
    }

    public function set($key, $value){
        return $this->redis->set($key, $value);
    }

    public function hget($key, $hashKey){
        return $this->redis->hGet($key, $hashKey);
    }

    public function hmset($key, $data){
        $this->redis->hMSet($key, $data);
    }

    public function flush(){
        $this->redis->select($this->db);
        return $this->redis->flushDb();
    }





}