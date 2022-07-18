#! /usr/bin/php74

<?php
$hoboBi = null;
require_once(dirname(__FILE__) . '/../engine/startup.php');

$daemon = new \hobotix\hoboSimpleDaemon('ticker24');

$hoboBi->binanceAPI->ticker(false, function($api, $symbol, $ticker) use ($hoboBi) {

    if (!$hoboBi->redis->hget($ticker['symbol'], 'symbol')){
          echoLine('Новая пара! ' . $ticker['symbol']);
 //       $message = '❤️ <b>' . $ticker['symbol'] . '</b>, курс ' . $ticker['close'] . '' . PHP_EOL;
 //       $hoboBi->telegram->sendMessage($message);
    }

    $r = $hoboBi->redis->hmset($ticker['symbol'], $ticker);
    $r = $hoboBi->hoboSymbols->updateBestBidAsk($ticker);
});