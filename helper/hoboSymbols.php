<?php

namespace hobotix;

class hoboSymbols extends hoboClass{	
	private $table 			    = 'symbols';
	private $ourSymbols		    = [];
    private $ourSymbolsFull		= [];

	public function __construct(\hobotix\hoboBi $hoboBi){
		parent::__construct($hoboBi);
		$this->setSymbols();
	}

	private function setSymbols(){
		$query = $this->db->query("SELECT * FROM  " . $this->table . " WHERE 1");

		foreach ($query->rows as $row){
            $this->ourSymbols[]                     = $row['symbol'];
            $this->ourSymbolsFull[$row['symbol']]   = $row;
		}

		return $this;
	}

	public function checkSymbol($symbol){
		return in_array($symbol, $this->ourSymbols);		
	}

	public function checkSymbolInDB($symbol){
		return $this->db->query("SELECT * FROM " . $this->table . " WHERE symbol = '" . $this->db->escape($symbol['symbol']) . "' LIMIT 1")->num_rows;
	}

	public function addSymbol($symbol){
		$this->db->query("INSERT IGNORE INTO " . $this->table . " SET 
			symbol 			= '" . $this->db->escape($symbol['symbol']) . "',
			baseAsset 		= '" . $this->db->escape($symbol['baseAsset']) . "',
			quoteAsset 		= '" . $this->db->escape($symbol['quoteAsset']) . "',
			status          = '" . $this->db->escape($symbol['status'])  . "',
			price 			= '" . (float)$symbol['price'] . "'
			ON DUPLICATE KEY UPDATE
			baseAsset 		= '" . $this->db->escape($symbol['baseAsset']) . "',
			quoteAsset 		= '" . $this->db->escape($symbol['quoteAsset']) . "',
			status          = '" . $this->db->escape($symbol['status'])  . "',
			price 			= '" . (float)$symbol['price'] . "'");

		$this->biSymbols[] = $symbol['symbol'];
	}

    public function updateBestBidAsk($symbol){
        $this->db->query("UPDATE " . $this->table . " SET
        bestBid 		= '" . (float)$symbol['bestBid'] . "',
        bestAsk 		= '" . (float)$symbol['bestAsk'] . "'
        WHERE symbol = '" . $this->db->escape($symbol['symbol']) . "'");
    }

    public function updatePrevDayInfo($symbol){

        $this->db->query("UPDATE " . $this->table . " SET 
        priceChange 			= '" . (float)$symbol['priceChange'] . "',
        priceChangePercent 		= '" . (float)$symbol['priceChangePercent'] . "',
        weightedAvgPrice 		= '" . (float)$symbol['weightedAvgPrice'] . "',
        prevClosePrice 			= '" . (float)$symbol['prevClosePrice'] . "',
        lastPrice 			    = '" . (float)$symbol['lastPrice'] . "',
        lastQty 			    = '" . (float)$symbol['lastQty'] . "',
        bidPrice 			    = '" . (float)$symbol['bidPrice'] . "',
        bidQty 			        = '" . (float)$symbol['bidQty'] . "',
        askPrice 			    = '" . (float)$symbol['askPrice'] . "',
        askQty 			        = '" . (float)$symbol['askQty'] . "',
        openPrice 			    = '" . (float)$symbol['openPrice'] . "',
        highPrice 			    = '" . (float)$symbol['highPrice'] . "',
        lowPrice 			    = '" . (float)$symbol['lowPrice'] . "',
        volume 			        = '" . (float)$symbol['volume'] . "',
        quoteVolume 			= '" . (float)$symbol['quoteVolume'] . "',
        openTime 			    = '" . (float)$symbol['openTime'] . "',
        closeTime 			    = '" . (float)$symbol['closeTime'] . "',
        firstId 			    = '" . (float)$symbol['firstId'] . "',
        lastId 			        = '" . (float)$symbol['lastId'] . "',
        count 			        = '" . (float)$symbol['count'] . "'
        WHERE symbol = '" . $this->db->escape($symbol['symbol']) . "'");

    }

    public function getAllSymbolsToArray() : array {
        return $this->db->query("SELECT * FROM {$this->table} WHERE 1")->rows;
    }

    public function getSymbol($symbol) : array {

        if (!empty($this->ourSymbolsFull[$symbol])){
            return $this->ourSymbolsFull[$symbol];
        }

        return array();

    }

    public function getSymbolFromDB($symbol) : array {
        return $this->db->query("SELECT * FROM " . $this->table . " WHERE symbol = '" . $this->db->escape($symbol) . "'")->row;
    }

	public function compareOurAndBiSymbols(){
		$diff = array_diff($this->ourSymbols, $this->biSymbols);
		$this->clearOurSymbolsUnexistentOnBi($diff);

		return $diff;
	}

	public function clearOurSymbolsUnexistentOnBi($diff){

		foreach ($diff as $symbol){
			$this->db->query("DELETE FROM {$this->table} WHERE symbol = '{$this->db->escape($symbol)}'");
		}

		return $this;
	}

    //Возвращает цену для asset1 - asset2
    public function getPriceForPairByBaseOrQuoteAsset($asset1, $asset2) {
        //By BASE ASSET
        $query = $this->db->query("SELECT * FROM {$this->table} WHERE 
            baseAsset = '{$this->db->escape($asset1)}'
            AND quoteAsset = '{$this->db->escape($asset2)}'
            AND status = 'TRADING'");

        if ($query->num_rows){
            return 1 / $query->row['price'];
        }

        $query = $this->db->query("SELECT * FROM {$this->table} WHERE 
            baseAsset = '{$this->db->escape($asset2)}'
            AND quoteAsset = '{$this->db->escape($asset1)}'
            AND status = 'TRADING'");

        if ($query->num_rows){
            return $query->row['price'];
        }

        return false;
    }

    public function getSecondAssetForSymbol($symbol, $mainAsset){

        if ($symbol['baseAsset'] == $mainAsset){
            return $symbol['quoteAsset'];
        }

        if ($symbol['quoteAsset'] == $mainAsset){
            return $symbol['baseAsset'];
        }

        return false;
    }

    public function findAssetIsTradedWithMainAsset($symbols, $asset, $mainAsset){

        if (!empty($symbols[$asset . $mainAsset])){
            return $symbols[$asset . $mainAsset];
        }

        if (!empty($symbols[$mainAsset . $asset])){
            return $symbols[$mainAsset . $asset];
        }

        return false;
    }


    //DOGEUSDT, это операции с DOGE, Маркет цена будет bestBid при покупке DOGE
    //USDDOGE, это операции с USDT, Маркет цена будет bestAsk при покупке DOGE
    public function getAssetPriceTypeForSymbol($symbol, $mainAsset){
        if ($symbol['baseAsset'] == $mainAsset){
            return $symbol['bestBid'];
        }

        //Я ПОКУПАЮ DOGE, т.е. ищу первого, кто хочет ПРОДАТЬ DOGE
        if ($symbol['quoteAsset'] == $mainAsset){
            return $symbol['bestAsk'];
        }

        return false;
    }

    public function getAssetPriceForSymbol($symbol, $mainAsset){
        //Я ПРОДАЮ USDT, то есть ищу первого кто хочет КУПИТЬ USDT
        if ($symbol['baseAsset'] == $mainAsset){
            return $symbol['bestBid'];
        }

        //Я ПОКУПАЮ DOGE, т.е. ищу первого, кто хочет ПРОДАТЬ DOGE
        if ($symbol['quoteAsset'] == $mainAsset){
            return (1 / $symbol['bestAsk']);
        }

        return false;
    }

    public function getSymbolsByAsset($asset){
        $result = [];

        $query = $this->db->query("SELECT * FROM {$this->table} WHERE 
            (baseAsset = '{$this->db->escape($asset)}' OR quoteAsset = '{$this->db->escape($asset)}')
            AND NOT(baseAsset LIKE ('%UP'))
            AND NOT(baseAsset LIKE ('%DOWN'))                      
            AND status = 'TRADING'");

        foreach ($query->rows as $row){
            $result[$row['symbol']] = $row;
        }

        return $result;
    }

    public function getSymbolsByBaseAsset($asset){
        return $this->db->query("SELECT * FROM {$this->table} WHERE 
            baseAsset = '{$this->db->escape($asset)}'             
            AND status = 'TRADING'")->rows;
    }

    public function getSymbolsByQuoteAsset($asset){
        return $this->db->query("SELECT * FROM {$this->table} WHERE 
            quoteAsset = '{$this->db->escape($asset)}'
            AND NOT(baseAsset LIKE ('%UP'))
            AND NOT(baseAsset LIKE ('%DOWN')) 
            AND status = 'TRADING'")->rows;
    }

}