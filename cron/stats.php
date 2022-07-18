#! /usr/bin/php74

<?php
$hoboBi = null;
require_once(dirname(__FILE__) . '/../engine/startup.php');

$openOrders = $hoboBi->binanceAPI->openOrders();

$text = '😜 <b>' . $hoboBi->tguser . ', ордера, ' . date('d.m.Y') . '</b>' . PHP_EOL . PHP_EOL;
foreach ($openOrders as $openOrder){
	$currentPrice 	= $hoboBi->binanceAPI->price($openOrder['symbol']);

	$percent = round(($currentPrice / $openOrder['price']) * 100, 2);
	$price_if_completed = $openOrder['origQty'] * $openOrder['price'];
	$price_now = $openOrder['origQty'] * $currentPrice;

	$text .= '🔥 <b>'. $openOrder['side'] .'</b> #' . $openOrder['orderId'] . PHP_EOL;

	$text .= '<b>' . convertTimestampToDate($openOrder['updateTime']) . '</b>'. PHP_EOL;

	$text .= '<b>' . $openOrder['symbol'] . '</b>'. PHP_EOL;

	if ($openOrder['side'] == 'SELL'){
		$text .= 'SELL <b>' . shorterNum($openOrder['origQty']) . '</b> * <b>' . shorterNum($openOrder['price']) . '</b>'. PHP_EOL;
	}

	if ($openOrder['side'] == 'BUY'){
		$text .= 'BUY <b>' . shorterNum($openOrder['origQty']) . '</b> * <b>' . shorterNum($openOrder['price']) . '</b>'. PHP_EOL;
	}

	$text .= 'Щас цена <b>' . shorterNum($currentPrice) . '</b>, ' . '<b>' . $percent . '%</b> от нужной' . PHP_EOL;

	if ($openOrder['side'] == 'SELL'){
		$text .= '🍏 ОК = <b>' . shorterNum($price_if_completed) . '</b>' . PHP_EOL;
		$text .= '🍎 ЩАС = <b>' . shorterNum($price_now) . '</b>' . PHP_EOL;
	}

	if ($openOrder['side'] == 'BUY'){
		$text .= '🍏 ОК = <b>' . shorterNum($price_if_completed) . '</b>' . PHP_EOL;
		$text .= '🍎 ЩАС = <b>' . shorterNum($price_now) . '</b>' . PHP_EOL;
	}

	if ($openOrder['side'] == 'SELL'){
	/*	if ($lastBought = $hoboBi->hoboHistory->getLastBuyPriceBySymbol($openOrder['symbol'])){

			$text .= PHP_EOL;
			$text .= '☝️ Напомню, последний раз покупали <b>'. $lastBought['time'] .'</b>' . PHP_EOL . '<b>' . $lastBought['qty'] . '</b> по <b>' . $lastBought['price'] . '</b> и потратили <b>' . $lastBought['quoteQty'] . '</b>' . PHP_EOL;

		}
	*/
	}

	$text .= PHP_EOL;
}

$hoboBi->telegram->sendMessage($text);


$balances = $hoboBi->binanceAPI->balances();

$text = '😜 <b>' . $hoboBi->tguser . ', твои миллионы на счету на сегодня, ' . date('d.m.Y') . '</b>' . PHP_EOL . PHP_EOL;

$totalAvailable = $totalInDeposits = $totalInOrder = 0;
$deposits = [];
foreach ($balances as $token => $balance){

	if (isDeposit($token)){
		$deposits[$token] = $balance;
		continue;
	}

	if ((float)$balance['available'] + (float)$balance['onOrder']){
		$text .= '🔥 <b>' . $token . '</b>' . PHP_EOL;	
		
		if ($token != $hoboBi->config->gs('cu')){
			try{
				$currentRate = $hoboBi->binanceAPI->price($token . $hoboBi->config->gs('cu'));	
			} catch (\Exception $e){
				$currentRate = false;
			}
		} else {
			$currentRate = 1;
		}
	}

	if ((float)$balance['available'] > 0){

		$totalAvailable += convert($balance['available'], $currentRate);

		if ($token == $hoboBi->config->gs('cu') || !$currentRate){
			$text .= '🍏 На счету ' . shorterNum($balance['available']) . PHP_EOL;
		} else {			
			$text .= '🍏 На счету ' . shorterNum($balance['available'])  . ' (' . format($hoboBi->config->gs('cu'), $balance['available'], $currentRate) . ')' . PHP_EOL;
		}	
	}

	if ((float)$balance['onOrder'] > 0){

		$totalInOrder += convert($balance['onOrder'], $currentRate);

		if ($token == $hoboBi->config->gs('cu') || !$currentRate){
			$text .= '🍎 В ордерах ' . shorterNum($balance['onOrder']) . PHP_EOL;
		} else {
			$text .= '🍎 В ордерах ' . shorterNum($balance['onOrder']) . ' (' . format($hoboBi->config->gs('cu'), $balance['onOrder'], $currentRate) . ')' . PHP_EOL;
		}
	}
}

if ($deposits){
	$text .= PHP_EOL . PHP_EOL;
}

foreach ($deposits as $token => $deposit){
	$text .= '🔥 <b>Долгосрочный депозит ' . depositToken($token) . '</b>' . PHP_EOL;	

	if (depositToken($token) != $hoboBi->config->gs('cu')){
		try{
			$currentRate = $hoboBi->binanceAPI->price(depositToken($token) . $hoboBi->config->gs('cu'));	
		} catch (\Exception $e){
			$currentRate = false;
		}
	} else {
		$currentRate = 1;
	}	

	$totalInDeposits += convert($deposit['available'], $currentRate);

	if (depositToken($token) == $hoboBi->config->gs('cu') || !$currentRate){
		$text .= '🍏 На депозите ' . shorterNum($deposit['available']) . PHP_EOL;
	} else {		
		$text .= '🍏 На депозите ' . shorterNum($deposit['available']) . ' (' . format($hoboBi->config->gs('cu'), $deposit['available'], $currentRate) . ')' . PHP_EOL;
	}
}

$text .= PHP_EOL . PHP_EOL;

$text .= '🔥 Всего на счету <b>' . $hoboBi->config->gs('cu') . ': ' . format($hoboBi->config->gs('cu'), $totalAvailable) . '</b>' . PHP_EOL;
$text .= '🔥 Всего в ордерах <b>' . $hoboBi->config->gs('cu') . ': ' . format($hoboBi->config->gs('cu'), $totalInOrder) . '</b>' . PHP_EOL;

if ($totalInDeposits){
	$text .= '🔥 В депозитах <b>' . $hoboBi->config->gs('cu') . ': ' . format($hoboBi->config->gs('cu'), $totalInDeposits) . '</b>';
}

$hoboBi->telegram->sendMessage($text);

