<?php

namespace hobotix;

class hoboClass
{
    protected $db       = null;
    protected $hoboBi   = null;


    public function __construct(\hobotix\hoboBi $hoboBi)
    {
        $this->db       = $hoboBi->db;
        $this->hoboBi   = $hoboBi;
    }
}
