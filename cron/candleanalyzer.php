#! /usr/bin/php74

<?php

$hoboBi = null;

require_once(dirname(__FILE__) . '/../engine/startup.php');

$candlesInfo = $hoboBi->hoboCandleStick->getAnalyzedCandleSticks();


$candlesAnalyzeArray = [];
foreach ($candlesInfo as $candleInfo){
    if (empty($candlesAnalyzeArray[$candleInfo['symbol']])){
        $candlesAnalyzeArray[$candleInfo['symbol']] = [];
    }

    $candlesAnalyzeArray[$candleInfo['symbol']][] = $candleInfo;
}

//Сортируем каждую пару по времени изменения
foreach ($candlesAnalyzeArray as $symbol => $dynamics){
    usort($dynamics, function($a, $b){

        if ($a['eventTime'] > $b['eventTime']) return true;

        return false;

    });

    $candlesAnalyzeArray[$candleInfo['symbol']] = $dynamics;
}

//Думаем
foreach ($candlesAnalyzeArray as $symbol => $dynamics){
    $results[$symbol] = [];

    //Рост объема торгов
    $cnt = count($dynamics) - 1;
    $permanent_market_up = true;
    $permanent_price_up  = true;

    for ($i=0; $i<=$cnt; $i++){
        if (!empty($dynamics[$i + 1]) && $dynamics[$i]['volume'] < $dynamics[$i + 1]['volume']){
            $permanent_market_up = false;
        }
    }

    for ($i=0; $i<=$cnt; $i++){
        if (!empty($dynamics[$i + 1]) && $dynamics[$i]['close'] < $dynamics[$i + 1]['close']){
            $permanent_price_up = false;
        }
    }

    if ($permanent_market_up){
        $results[$symbol][] = 'PERMANENT_MARKET_UP';
    }

    if ($permanent_price_up){
        $results[$symbol][] = 'PERMANENT_PRICE_UP';
    }
}


foreach ($results as $symbol => $result){

    if (!empty($result)){
        echoLine($symbol . ':' . implode(',', $result));
    }

}

