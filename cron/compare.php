<?php
    $hoboBi = null;
    require_once(dirname(__FILE__) . '/../engine/startup.php');

    //Логика такая. Нам нужна RAND валюта, которая торгуется, например, с USDT и BTC одновременно
    //Купить RAND за 100 USDT, купить на полученные RAND BTC и продать полученные BTC в USDT

    //Отберем все
    $asset  = 'USDT';
    $sum    = 400;

    $symbolsByBaseAssets    = $hoboBi->hoboSymbols->getSymbolsByBaseAsset( $asset );
    echoLine('Базовая валюта: ' . $asset);
    foreach ($symbolsByBaseAssets as $symbolByBaseAsset){
        //Это первая валюта, которую мы купим
        $quoteAsset = $symbolByBaseAsset['quoteAsset'];
        $priceOnFirstStep = $symbolByBaseAsset['price'];
        $sumOnFirstStep = $sum * $symbolByBaseAsset['price'];

    //    echoLine('Пара ' . $symbolByBaseAsset['symbol'] . ', купим ' . $sumOnFirstStep . ' ' . $quoteAsset);

        //Теперь подбираем вторую пару для $quoteAsset
        $symbolsByBaseAssets2 = $hoboBi->hoboSymbols->getSymbolsByBaseAsset( $quoteAsset );
        foreach ($symbolsByBaseAssets2 as $symbolByBaseAsset2){
            $quoteAsset2    = $symbolByBaseAsset2['quoteAsset'];
            $sumOnSecondStep = $sumOnFirstStep * $symbolByBaseAsset2['price'];
   //         echoLine('Продадим ' . $symbolByBaseAsset['symbol'] . ', купим ' . $sumOnSecondStep . ' ' . $quoteAsset2);

            //А теперь третий шаг, нам нужно продать $quoteAsset2 в USDT
            $priceForThirdStep = $hoboBi->hoboSymbols->getPriceForPairByBaseOrQuoteAsset($asset, $quoteAsset2);
            $sumOnThirdStep = $priceForThirdStep * $sumOnSecondStep;

            if ($sumOnThirdStep > $sum + $sum/100)
            echoLine($quoteAsset . '->' . $quoteAsset2 . ', купим ' . $sumOnThirdStep . ' ' . $asset);
        }

        $symbolsByQuoteAssets2 = $hoboBi->hoboSymbols->getSymbolsByQuoteAsset( $quoteAsset );
        foreach ($symbolsByQuoteAssets2 as $symbolByQuoteAsset2){
            //Второй шаг, покупаем какую-то валюту
            $baseAsset2         = $symbolByQuoteAsset2['baseAsset'];
            $sumOnSecondStep    = $sumOnFirstStep / $symbolByQuoteAsset2['price'];

    //        echoLine('Продадим ' . $quoteAsset . ', купим ' . $sumOnSecondStep . ' ' . $baseAsset2);

            //А теперь третий шаг, нам нужно продать $baseAsset2 в USDT
            $priceForThirdStep = $hoboBi->hoboSymbols->getPriceForPairByBaseOrQuoteAsset($asset, $baseAsset2);
            $sumOnThirdStep = $priceForThirdStep * $sumOnSecondStep;

            if ($sumOnThirdStep > $sum + $sum/100) {
                echoLine($quoteAsset . '->' . $baseAsset2 . ', купим ' . $sumOnThirdStep . ' ' . $asset);
                echoLine($priceOnFirstStep);
            }

        }
    }


    $symbolsByQuoteAssets   = $hoboBi->hoboSymbols->getSymbolsByQuoteAsset( $asset );
    foreach ($symbolsByQuoteAssets as $symbolByBaseAsset){
        //Это первая валюта, которую мы купим
        $baseAsset = $symbolByBaseAsset['baseAsset'];
        $priceOnFirstStep = $symbolByBaseAsset['price'];
        $sumOnFirstStep = $sum / $symbolByBaseAsset['price'];

    //    echoLine('Пара ' . $symbolByBaseAsset['symbol'] . ', купим ' . $sumOnFirstStep . ' ' . $baseAsset);

        //Теперь ищем пары, где $baseAsset - первая валюта
        //Теперь подбираем вторую пару для $baseAsset
        $symbolsByBaseAssets2 = $hoboBi->hoboSymbols->getSymbolsByBaseAsset( $baseAsset );
        foreach ($symbolsByBaseAssets2 as $symbolByBaseAsset2){
            $quoteAsset2    = $symbolByBaseAsset2['quoteAsset'];
            $sumOnSecondStep = $sumOnFirstStep * $symbolByBaseAsset2['price'];
        //    echoLine('Продадим ' . $symbolByBaseAsset['symbol'] . ', купим ' . $sumOnSecondStep . ' ' . $quoteAsset2);

            //А теперь третий шаг, нам нужно продать $quoteAsset2 в USDT
            $priceForThirdStep = $hoboBi->hoboSymbols->getPriceForPairByBaseOrQuoteAsset($asset, $quoteAsset2);
            $sumOnThirdStep = $priceForThirdStep * $sumOnSecondStep;

            if ($sumOnThirdStep > $sum + $sum/100) {
                echoLine($baseAsset . '->' . $baseAsset2 . ', купим ' . $sumOnThirdStep . ' ' . $asset);
                echoLine($sumOnFirstStep . ' по ' . $priceOnFirstStep);
                echoLine($sumOnSecondStep . ' по ' . $symbolByBaseAsset2['price']);
                echoLine($sumOnThirdStep . ' по ' . $priceForThirdStep);
                echoLine('');
            }
        }

        $symbolsByQuoteAssets2 = $hoboBi->hoboSymbols->getSymbolsByQuoteAsset( $baseAsset );
        foreach ($symbolsByQuoteAssets2 as $symbolByQuoteAsset2){
            //Второй шаг, покупаем какую-то валюту
            $baseAsset2         = $symbolByQuoteAsset2['baseAsset'];
            $sumOnSecondStep    = $sumOnFirstStep / $symbolByQuoteAsset2['price'];

            //        echoLine('Продадим ' . $quoteAsset . ', купим ' . $sumOnSecondStep . ' ' . $baseAsset2);

            //А теперь третий шаг, нам нужно продать $baseAsset2 в USDT
            $priceForThirdStep = $hoboBi->hoboSymbols->getPriceForPairByBaseOrQuoteAsset($asset, $baseAsset2);
            $sumOnThirdStep = $priceForThirdStep * $sumOnSecondStep;

            if ($sumOnThirdStep > $sum + $sum/100) {
                echoLine($baseAsset . '->' . $baseAsset2 . ', купим ' . $sumOnThirdStep . ' ' . $asset);
                echoLine($sumOnFirstStep . ' по ' . $priceOnFirstStep);
                echoLine($sumOnSecondStep . ' по ' . $symbolByBaseAsset2['price']);
                echoLine($sumOnThirdStep . ' по ' . $priceForThirdStep);
                echoLine('');
            }

        }

    }