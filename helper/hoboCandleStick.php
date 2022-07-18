<?php

namespace hobotix;

class hoboCandleStick extends hoboClass
{
    private string $table          = 'candlestick';
    private int $ttl            = 1800;
    private int $probability    = 50;
    private int $ttl_analyze    = 300;
    private bool $add_margin     = false;

    private $analyzeSymbols  = [];

    public function __construct(hoboBi $hoboBi)
    {
        parent::__construct($hoboBi);

        if ($config = Config::loadJSON('ticker')) {
            $this->ttl          = $config['ttl'];
            $this->probability  = $config['probability'];
        }

        $this->loadAnalyzeSymbols();
    }

    private function createInClause($arr)
    {
        $tmp = array();
        foreach($arr as $item)
        {
            $tmp[] = $this->db->escape($item);
        }
        return '\'' . implode( '\', \'', $tmp ) . '\'';
    }

    private function loadAnalyzeSymbols(){
        $this->analyzeSymbols = Config::loadJSON('analyze');
        return $this;
    }

    public function cleanDB()
    {
        if (probability($this->probability)) {
            $this->db->query("DELETE FROM " . $this->table . " WHERE eventTime <= '" . ((bimicrotime()/1000) - $this->ttl) * 1000 . "'");       
        }
    }


    public function addCandleStick($symbol)
    {

        $this->cleanDB();

        $symbolInfo = $this->hoboBi->hoboSymbols->getSymbol($symbol['symbol']);
        if (!$symbolInfo){
            $symbolInfo = ['baseAsset' => '', 'quoteAsset' => '', 'status' => ''];
        }

        $this->db->query("INSERT INTO " . $this->table . " SET 
			symbol 			= '" . $this->db->escape($symbol['symbol']) . "',
			status          = '" . $this->db->escape($symbolInfo['status']) . "',
			baseAsset       = '" . $this->db->escape($symbolInfo['baseAsset']) . "',
			quoteAsset      = '" . $this->db->escape($symbolInfo['quoteAsset']) . "',
			close 			= '" . (float)$symbol['close'] . "',
			open 			= '" . (float)$symbol['open'] . "',
			high 			= '" . (float)$symbol['high'] . "',
			low 			= '" . (float)$symbol['low'] . "',
			volume			= '" . (float)$symbol['volume'] . "',
			quoteVolume		= '" . (float)$symbol['quoteVolume'] . "',
			eventTime		= '" . (int)$symbol['eventTime'] . "',
			eventTimeReadable = '" . $this->db->escape(convertTimestampToDate((int)$symbol['eventTime'])) . "'");
    }

    public function getAllCandleSticks(){
        return $this->db->query("SELECT * FROM " . $this->table . " WHERE eventTime >= '" . ((bimicrotime()/1000) - $this->ttl_analyze) * 1000 . "'")->rows;
    }


    public function getAnalyzedCandleSticks(){

        if ($this->analyzeSymbols){

            $sql = "SELECT * FROM " . $this->table . " WHERE symbol2 IN (" . $this->createInClause($this->analyzeSymbols) . ") AND eventTime >= '" . ((bimicrotime()/1000) - $this->ttl_analyze) * 1000 . "'";

            return $this->db->query($sql)->rows;
        } else {
            return $this->getAllCandleSticks();
        }


    }
}
