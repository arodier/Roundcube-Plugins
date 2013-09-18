<?php

# Use the interface
require 'sieve_transfer.php';

class SSHTransfer extends SieveTransfer
{
    public function LoadScript($path)
    {
        $script = '';

        $tmpFile = tempnam("/tmp", "sieve");

        $user = $this->params['user'];
        $host = $this->params['host'];
        $options = $this->params['options'];

        if ( !$user ) $user = 'vmail';
        if ( !$host ) $host = 'localhost';
        if ( !$options ) $options = '';

        # Copy the file
        $status = 0;
        $command = sprintf("/usr/bin/scp -q %s %s@%s:%s '%s'", $options, $user, $host, $path, $tmpFile);
        system($command, $status);

        $script = file_get_contents($tmpFile);
        unlink($tmpFile);

        return $script;
    }

    public function SaveScript($path,$script)
    {
        $tmpFile = tempnam("/tmp", "sieve");
        file_put_contents($tmpFile, $script);

        $user = $this->params['user'];
        $host = $this->params['host'];
        $options = $this->params['options'];

        if ( !$user ) $user = 'vmail';
        if ( !$host ) $host = 'localhost';
        if ( !$options ) $options = '';

        # Copy the file
        $status = 0;
        $command = sprintf("/usr/bin/scp -q %s '%s' %s@%s:%s", $options, $tmpFile, $user, $host, $path);
        system($command, $status);

        if ( $status == 0 && !empty($this->params['sievecbin']) )
        {
            # Compile the file. I don't think this is necessary with Dovecot
            # as it compiles the files on the fly by default.
            $sievecbin = $this->params['sievecbin'];
            $command = sprintf("/usr/bin/ssh %s %s@%s '%s \"%s\"'", $options, $user, $host, $sievecbin, $path);
            system($command, $status);
        }
        else
        {
            $this->lastError = sprintf('Error when copying the sieve script on the server (status=%d)', $status);
        }

        # Clean up
        unlink($tmpFile);

        return ($status == 0);
    }
}
