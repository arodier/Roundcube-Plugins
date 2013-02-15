<?php

# Use the interface
require 'sieve_transfer.php';

class SSHTransfer extends SieveTransfer
{
    public function LoadScript($path)
    {
        $script = '';

        if ( file_exists($path) )
             $script = file_get_contents($path);

        return $script;
    }

    public function SaveScript($path,$script)
    {
        $tmpFile = tempnam("/tmp", "sieve");
        file_put_contents($tmpFile, $script);

        $user = $this->params['user'];
        $host = $this->params['host'];
        $sievecbin = $this->params['sievecbin'];

        if ( !$user ) $user = 'vmail';
        if ( !$host ) $host = 'localhost';

        list($logon,$domain) = explode('@', $_SESSION['username']);

        # Copy the file
        $status = 0;
        $command = sprintf("scp '%s' %s@%s:%s", $tmpFile, $user, $host, $path);
        system($command, $status);

        if ( $status == 0 )
        {
            # Compile the file. I don't think this is necessary with Dovecot
            # as it compiles the files on the fly by default.
            $command = sprintf("ssh %s@%s '%s \"%s\"'", $user, $host, $sievecbin, $path);
            system($command, $status);
        }

        # Clean up
        unlink($tmpFile);

        return ($status == 0);
    }
}
