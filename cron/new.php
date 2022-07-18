#! /usr/bin/php74

<?php
    $hoboBi = null;
	require_once(dirname(__FILE__) . '/../engine/startup.php');
	
	$newPairs = [];

    $exchangeInfo = $hoboBi->binanceAPI->exchangeInfo()['symbols'];

	foreach ($hoboBi->binanceAPI->prices() as $symbol => $price){

	//	echoLine(' Пара: ' . $symbol . ': ' . $price);

        $data = ['symbol' => $symbol, 'price' => $price, 'baseAsset' => '', 'quoteAsset' => '', 'status'=> ''];
        if (!empty($exchangeInfo[$symbol])){
            $data['baseAsset']  = $exchangeInfo[$symbol]['baseAsset'];
            $data['quoteAsset'] = $exchangeInfo[$symbol]['quoteAsset'];
            $data['status']     = $exchangeInfo[$symbol]['status'];
        }

        if (!$isOldPair = $hoboBi->hoboSymbols->checkSymbol($symbol)){
            echoLine(' Новая пара на Binance: ' . $symbol . ': ' . $price);
            $newPairs[$symbol] = $data;
        }

		$hoboBi->hoboSymbols->addSymbol($data);
	}

    foreach ($prevDayInfo = $hoboBi->binanceAPI->prevDay() as $prevDaySymbol){
   //     echoLine(' Пара: ' . $prevDaySymbol['symbol'] . ': ' . $prevDaySymbol['priceChangePercent'] . '%');
        $hoboBi->hoboSymbols->updatePrevDayInfo($prevDaySymbol);
    }

	if ($newPairs){
		$text = '😜 <b>Новые пары на Binance</b>' . PHP_EOL . PHP_EOL;

		foreach ($newPairs as $newPair){			
			$text .= '❤️ <b>' . $newPair['symbol'] . '</b>, курс ' . $newPair['price'] . '' . PHP_EOL;
		}

	//	$hoboBi->telegram->sendMessage($text);
	}

	if ($deletedPairs = $hoboBi->hoboSymbols->compareOurAndBiSymbols()){
		$text = '😂 <b>Удаленные пары на Binance</b>' . PHP_EOL . PHP_EOL;

		foreach ($deletedPairs as $deletedPair){			
			$text .= '👎 <b>' . $deletedPair . '</b>, всё, пиздец этому токену' . PHP_EOL;
		}

	//	$hoboBi->telegram->sendMessage($text);

	}

