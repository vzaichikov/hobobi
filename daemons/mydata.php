#! /usr/bin/php74

<?php
$hoboBi = null;
$config = null;
require_once(dirname(__FILE__) . '/../engine/startup.php');

$daemon = new \hobotix\hoboSimpleDaemon('mydata' . $config);

$balanceUpdateFunction = function ($api, $balances) use ($hoboBi) : string {

    $text = '👋 <b>' . $hoboBi->tguser . ', обновление баланса!</b>' . PHP_EOL;

    foreach ($balances as $asset => $data){
        $text .= '🔥 <b>' . $asset . '</b>' . PHP_EOL;
        $text .= '🍎 В ордере ' . shorterNum($data['onOrder']) . PHP_EOL;
        $text .= '🍏 Доступно еще ' . shorterNum($data['available']) . PHP_EOL;
    }

  //  $hoboBi->telegram->sendMessage($text);

    return '';

};

$orderUpdateFunction = function ($api, $report) use ($hoboBi) : string {

    $text = '👋 <b>' . $hoboBi->tguser . ', ордер!</b>' . PHP_EOL;

    $text .= '🔥 <b>' . $report['side'] . ' ' . $report['orderType'] .  ' ' . $report['symbol'] . '</b>' . PHP_EOL;
    $text .= shorterNum($report['quantity']) . ' по цене ' . shorterNum($report['price']) . PHP_EOL;
    $text .= 'Статус ' . '<b>' . $report['orderStatus'] . '</b>';

    $hoboBi->telegram->sendMessage($text);

    return '';
};


$hoboBi->binanceAPI->userData($balanceUpdateFunction, $orderUpdateFunction);