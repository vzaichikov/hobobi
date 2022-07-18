<?php

function echoLine($line)
{
    echo '[HBI] ' . $line . PHP_EOL;
}

function convertTimestampToDate($timestamp)
{
    return date('Y-m-d H:i:s', (int)substr((string)$timestamp, 0, strlen((string)$timestamp) - 3));
}

function bimicrotime()
{
   return floor(microtime(true) * 1000);
}

function convert($value, $rate)
{
    return $value * $rate;
}

function isMargin($token){
    return (substr($token, -2) == 'UP' || substr($token, -4) == 'DOWN');
}

function isDeposit($token)
{
    return (substr($token, 0, 2) == 'LD');
}

function depositToken($token)
{
    return substr($token, 2);
}

function shorterNum($numeric){
    $numeric = (string)$numeric;

    if (substr($numeric, -5) == '.0000'){
        return substr($numeric, 0, mb_strlen($numeric) - 5);
    }

    if (substr($numeric, -5) == '00000'){
        return substr($numeric, 0, mb_strlen($numeric) - 5);
    }

    if (substr($numeric, -4) == '0000'){
        return substr($numeric, 0, mb_strlen($numeric) - 4);
    }

    return $numeric;
}

function format($token, $value, $rate = false)
{
    if (!$rate) {
        return $value . ' ' . $token;
    }

    return shorterNum((string)($value * $rate)) . ' ' . $token;
}

function probability($probability)
{
    return (mt_rand(0, 100) >= $probability);
}
