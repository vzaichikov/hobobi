<?php

namespace hobotix;

class hoboHistory extends hoboClass
{

    public function __construct(\hobotix\hoboBi $hoboBi)
    {
        parent::__construct($hoboBi);
    }

    private function convertTimestampToDate($timestamp)
    {

        $utime = sprintf('%.4f', $timestamp);
        return DateTime::createFromFormat('U.u', $utime)->format('Y-m-d H:i:s');
    }

    public function getLastBuyPriceBySymbol($symbol)
    {
        $completedOrders = $this->hoboBi->binanceAPI->history($symbol);
        $result = [];
        
        $lastDate = 10;
        foreach ($completedOrders as $completedOrder) {
            if ($completedOrder['isBuyer'] && $lastDate < $completedOrder['time']) {
                $result = [
                    'qty'       => $completedOrder['qty'],
                    'price'     => $completedOrder['price'],
                    'quoteQty'  => $completedOrder['quoteQty'],
                    'time'      => convertTimestampToDate($completedOrder['time'])
                ];

                $lastDate = $completedOrder['time'];
            }
        }

        return $result;
    }
}
