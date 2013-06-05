<?php


abstract class SieveTransfer
{
    # transfer params
    protected $params;

    # store last error
    protected $lastError;

    public function LastError()
    {
        return $this->lastError;
    }

    public function SetParams($params)
    {
        $this->params = $params;
    }

    abstract public function LoadScript($path);
    abstract public function SaveScript($path,$script);
}

