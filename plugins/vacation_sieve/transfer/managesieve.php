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
            method_exists($this->app, get_user_name) ? $this->app->get_user_name() : $_SESSION['username'],
            method_exists($this->app, get_user_password) ? $this->app->get_user_password() : $this->app->decrypt($_SESSION['password']),
            $this->params['host'],
            $this->params['port'],
            null,
            $this->params['usetls']
        );

        if ($error = $this->managesieve->error()) {
            $this->ShowError($error);
        }

        return $error;

    }

    private function ShowError($error)
    {
        if ($error) {
            switch ($error) {
                case SIEVE_ERROR_CONNECTION:
                case SIEVE_ERROR_LOGIN:
                    $this->app->output->show_message('Managesieve: Connection error', 'error');
                    break;
                case SIEVE_ERROR_NOT_EXISTS:
                    $this->app->output->show_message('Managesieve: Script does not exist', 'error');
                    break;
                case SIEVE_ERROR_INSTALL:
                    $this->app->output->show_message('Managesieve: Script failed to install', 'error');
                    break;
                case SIEVE_ERROR_ACTIVATE:
                case SIEVE_ERROR_DEACTIVATE:
                    $this->app->output->show_message('Managesieve: Activation change failed', 'error');
                    break;
								case 255:
										$this->app->output->show_message('Managesieve: No sieve scripts found (This can be ignored on first run)', 'info');
										break;
                default:
                    $this->app->output->show_message('Managesieve: Unknown error', 'error');
                    break;
            }
        }
    }

    public function LoadScript($script_name)
    {
        $script = '';

        if (!$this->managesieve) {
            if($this->GetManagesieve()) { return 0; }
        }

        if ($script_name) {
            $script = $this->managesieve->get_script($script_name);
            if($error = $this->managesieve->error()) {
                $this->ShowError($error);
                return 0;
            }
        }

        return $script;
    }

    public function SaveScript($script_name,$script)
    {
        $success = false;
        $success_save = false;
        $success_activate = false;

        if (!$this->managesieve) {
            if($this->GetManagesieve()) { return 0; }
        }

        if ($script_name) {
            $success_save = $this->managesieve->save_script($script_name,$script);
            if($error = $this->managesieve->error()) {
                $this->ShowError($error);
                return 0;
            }
            if($this->params['enable'] && $this->params['ms_activate_script'])
            {
                $success_activate = $this->managesieve->activate($script_name);
                if($success_save && $success_activate){ $success = true; }
            }
            else
            {
                if($success_save){ $success = true; }
            }
        }

        return $success;
    }
}

