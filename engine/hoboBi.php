<?php

namespace hobotix;

final class HoboBi
{
    const defaultConfig = 'v';

    public $binanceAPI;
    public $db;
    public $telegram;
    public $user;
    public $tguser;

    public $config;

    public $hoboCandleStick;
    public $hoboSymbols;
    public $hoboHistory;

    public function __construct($config = '', $daemonize = false)
    {

        if (empty($config)) {
            $config = self::defaultConfig;
        }

        $this->createDirectories();

        //LIBS
        $this->library('config');
        $this->library('mysqli');
        $this->library('functions');
        $this->library('telegram');
        $this->library('redis');

        //HELPERS FOR DB
        $this->helper('hoboClass');
        $this->helper('hoboCandleStick');
        $this->helper('hoboSymbols');
        $this->helper('hoboHistory');
        $this->helper('hoboSimpleDaemon');

        $this->config       = new \hobotix\Config($config);
        $this->user         = $this->config->gs('un');
        $this->tguser       = $this->config->gs('tgu');

        if ($this->config->gs('json') && file_exists(dirname(__FILE__) . '/../configs/json/' . $this->config->gs('json'))) {
            $binanceApiObject = new \Binance\API(dirname(__FILE__) . '/../configs/json/' . $this->config->gs('json'));
        } else {
            $binanceApiObject = new \Binance\API($this->config->gs('apiKey'), $this->config->gs('apiSecret'));
        }

        $this->binanceAPI   = new \Binance\RateLimiter($binanceApiObject);
        $this->db           = new \hobotix\MySQLi($this->config->gs('dbh'), $this->config->gs('dbu'), $this->config->gs('dbp'), $this->config->gs('db'));
        $this->telegram     = new  \hobotix\Telegram($this->config->gs('tgt'), $this->config->gs('tgn'), $this->config->gs('tgg'));
        $this->redis        = new \hobotix\hoboRedis();

        $this->binanceAPI->useServerTime();
        echoLine('Сейчас на таймере: ' . date('Y-m-d H:i:s'));

        $this->hoboCandleStick  = new \hobotix\hoboCandleStick($this);
        $this->hoboSymbols      = new \hobotix\hoboSymbols($this);
        $this->hoboHistory      = new \hobotix\hoboHistory($this);

        echoLine('Привет, ' . $this->user);

        if ($daemonize){
            echoLine('Daemonize: , ' . $daemonize);
            $daemon = new hoboSimpleDaemon($daemonize);
        }
    }

    public function createDirectories(){

        if (!is_dir(dirname(__FILE__) . '/../pids/')){
            echoLine('Directory pids doesnt exist, creating');
            mkdir(dirname(__FILE__) . '/../pids/');
        }

    }

    public function library($class)
    {
        $file = dirname(__FILE__) . '/../library/' . str_replace('\\', '/', $class) . '.php';

        if (is_file($file)) {
            include_once($file);

            return $this;
        } else {
            echoLine('Could not load library ' . $class);
            die();
        }
    }

    public function helper($class)
    {
        $file =  dirname(__FILE__) . '/../helper/' . str_replace('\\', '/', $class) . '.php';

        if (is_file($file)) {
            include_once($file);

            return $this;
        } else {
            echoLine('Could not load helper ' . $class);
            die();
        }
    }
}
