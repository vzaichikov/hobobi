<?php
$hoboBi = null;
require_once(dirname(__FILE__) . '/../engine/startup.php');

//Логика такая. Нам нужна RAND валюта, которая торгуется, например, с USDT и BTC одновременно
//Купить RAND за 100 USDT, купить на полученные RAND BTC и продать полученные BTC в USDT

//Отберем все
$mainAsset          = 'USDT';
$tradingSum         =  400;
$tradingMinProfit   = 1;

$symbolsTradedWithAsset    = $hoboBi->hoboSymbols->getSymbolsByAsset( $mainAsset );
echoLine('Базовая валюта: ' . $mainAsset);

//Перебираем все, первый шаг
foreach ($symbolsTradedWithAsset as $symbolTradedWithAsset){
    $priceOnFirstStep   = $hoboBi->hoboSymbols->getAssetPriceForSymbol($symbolTradedWithAsset, $mainAsset);
    $sumOnFirstStep     = $tradingSum * $priceOnFirstStep;

    //Итак, на первом шаге мы получили $sumOnFirstStep DOGE
    //Теперь получаем все пары, торгующиеся с DOGE
    $secondAsset = $hoboBi->hoboSymbols->getSecondAssetForSymbol($symbolTradedWithAsset, $mainAsset);
    $symbolsTradedWithAssetOnSecondStep = $hoboBi->hoboSymbols->getSymbolsByAsset( $secondAsset );

    //ТЕКСТ НА ПЕРВОМ ШАГЕ
    $stringStep1 = 'Шаг 1: ' . $sumOnFirstStep . ' ' . $secondAsset .' (' . $tradingSum . ' по ' . $hoboBi->hoboSymbols->getAssetPriceTypeForSymbol($symbolTradedWithAsset, $mainAsset) . ')';
    //ТЕКСТ НА ПЕРВОМ ШАГЕ END

    foreach ($symbolsTradedWithAssetOnSecondStep as $symbolTradedWithAssetOnSecondStep){
        $thirdAsset = $hoboBi->hoboSymbols->getSecondAssetForSymbol($symbolTradedWithAssetOnSecondStep, $secondAsset);

        //Предварительная проверка, торгуется ли третья валюта с первой
        if ($thirdSymbol = $hoboBi->hoboSymbols->findAssetIsTradedWithMainAsset($symbolsTradedWithAsset, $thirdAsset, $mainAsset)) {

            $stringAssets = $mainAsset . ' -> ' . $secondAsset . ' -> ' . $thirdAsset . ' -> ' . $mainAsset;
            $stringAssetsFull = $symbolTradedWithAsset['symbol'] . ' -> ' . $symbolTradedWithAssetOnSecondStep['symbol'] . ' -> ' . $thirdSymbol['symbol'];

            $priceOnSecondStep = $hoboBi->hoboSymbols->getAssetPriceForSymbol($symbolTradedWithAssetOnSecondStep, $secondAsset);
            $sumOnSecondStep = $sumOnFirstStep * $priceOnSecondStep;

            //ТЕКСТ НА ВТОРОМ ШАГЕ
            $stringStep2 = 'Шаг 2: ' . $sumOnSecondStep . ' ' . $thirdAsset . ' (' . $sumOnFirstStep . ' по ' . $hoboBi->hoboSymbols->getAssetPriceTypeForSymbol($symbolTradedWithAssetOnSecondStep, $secondAsset) . ')';
            //ТЕКСТ НА ВТОРОМ ШАГЕ END


            //ТРЕТИЙ ШАГ
            $priceOnThirdStep = $hoboBi->hoboSymbols->getAssetPriceForSymbol($thirdSymbol, $thirdAsset);
            $sumOnThirdStep = $sumOnSecondStep * $priceOnThirdStep;

            //ТЕКСТ НА ВТОРОМ ШАГЕ
            $stringStep3 = 'Шаг 3: ' . $sumOnThirdStep . ' ' . $mainAsset . ' (' . $sumOnSecondStep . ' по ' . $hoboBi->hoboSymbols->getAssetPriceTypeForSymbol($thirdSymbol, $thirdAsset) . ')';
            //ТЕКСТ НА ВТОРОМ ШАГЕ END


            //Пробуем вывести
            if ($sumOnThirdStep > $tradingSum) {
                echoLine($stringAssets);
                echoLine($stringAssetsFull);
                for ($i = 1; $i <= 3; $i++) {
                    echoLine(${'stringStep' . $i});
                }
            }

        }
    }


  //

}