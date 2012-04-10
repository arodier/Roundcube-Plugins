<?php


abstract class SieveTransfer
{
    # transfer params
    protected $params;

    public function SetParams($params)
    {
        $this->params = $params;
    }

    abstract public function LoadScript($path);
    abstract public function SaveScript($path,$script);
}

