#! /usr/bin/php74

<?php
    $hoboBi = null;
    require_once(dirname(__FILE__) . '/../engine/startup.php');

    $hoboBi->binanceAPI->ticker(false, function($api, $symbol, $ticker) use ($hoboBi) {

        if (!$hoboBi->redis->get($ticker['symbol'])){
            echoLine('Новая пара! ' . $ticker['symbol']);
        }

        $r = $hoboBi->redis->set($ticker['symbol'], json_encode($ticker));

    });