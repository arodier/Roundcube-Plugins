<?php

# Use the interface
require 'sieve_transfer.php';

class LocalTransfer extends SieveTransfer
{
    public function LoadScript($path)
    {
        $content = '';

        if ( file_exists($path) )
            $content = file_get_contents($path);

        return $content;
    }

    public function SaveScript($path,$script)
    {
        $success = false;

        $folder = pathinfo($path,PATHINFO_DIRNAME);
        $try = true;

        # Create the folder if not exists
        if ( !is_dir($folder) )
            $try = @mkdir($folder,0755,true);

        if ( !$try )
        {
            $msg = sprintf('Cannot create folder "%s" to save the script.', $folder);
            throw new Exception($msg);
        }

        # Copy the script
        $bytes = @file_put_contents($path,$script);
        $success = ($bytes != false);

        if ( !$success )
        {
            $msg = sprintf('Cannot write file "%s" to save the script.', $path);
            throw new Exception($msg);
        }

        # Compile the script
        if ( $success )
            $success = (system("$this->params['sievecbin'] $path") == 0);

        return $success;
    }
}

