<?php


function GetTransferClass($mode, $params)
{
    $transfer = null;

    $mode = strtolower($mode);
    require "$mode.php";

    if ( $mode == 'ssh' )
    {
        $transfer = new SSHTransfer();
    }
    elseif ( $mode == 'local' )
    {
        $transfer = new LocalTransfer();
    }

    $transfer->SetParams($params);

    return $transfer;
}
