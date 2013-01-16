<?php

# Use the interface
require 'sieve_transfer.php';

class ManagesieveTransfer extends SieveTransfer
{
    private $app = null;
    private $managesieve = null;

    private function GetManagesieve()
    {
        $this->app = rcmail::get_instance();
        // Add include path for internal classes
        $include_path = $this->app->plugins->dir . 'managesieve/lib' . PATH_SEPARATOR;
        $include_path .= ini_get('include_path');
        set_include_path($include_path);

        if (empty($this->params['port'])) {
            $this->params['port'] = getservbyname('sieve', 'tcp');
            if (empty($this->params['port'])) {
                $this->params['port'] = "4190";
            }
        }

        // try to connect to managesieve server
        $this->managesieve = new rcube_sieve(
            $this->app->get_user_name(),
            $this->app->get_user_password(),
            $this->params['host'],
            $this->params['port'],
            null,
            $this->params['usetls']
        );

    }

    public function LoadScript($script_name)
    {
        $script = '';

        if (!$this->managesieve) {
            $this->GetManagesieve();
        }

        if ($script_name) {
            $script = $this->managesieve->get_script($script_name);
        }

        return $script;
    }

    public function SaveScript($script_name,$script)
    {
        $success = false;

        if (!$this->managesieve) {
            $this->GetManagesieve();
        }

        if ($script_name) {
            $success = $this->managesieve->save_script($script_name,$script);
        }

        return $success;
    }
}

