#! /usr/bin/php74
<?php

namespace hobotix;

    $hoboBi = null;
    require_once(dirname(__FILE__) . '/../engine/startup.php');

    $daemon = new \hobotix\hoboSimpleDaemon('candlestick');

    $hoboBi->binanceAPI->miniTicker(function ($api, $ticker) use ($hoboBi) {
        foreach ($ticker as $symbol) {
            $hoboBi->hoboCandleStick->addCandleStick($symbol);
        }
    });
