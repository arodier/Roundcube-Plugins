<?php
/*
 * Identiteam: Create users' identities and signature by querying an LDAP server.
 * Author: André Rodier <andre.rodier@gmail.com>
 * Licence: GPLv3. (See copying)
 */
class identiteam extends rcube_plugin
{
    public $task = 'login';

    private $config;
    private $app;

    # LDAP parameters
    private $ldap ;
    private $server ;
    private $filter ;
    private $domain ;
    private $fields ;
    private $conn ;


    function init()
    {
        # Load default config, and merge with users' settings
        $this->load_config('config-default.inc.php');
        $this->load_config('config.inc.php');

        $this->app = rcmail::get_instance();
        $this->config = $this->app->config->get('identiteam');

        # Load LDAP config at once
        $this->ldap = $this->config['ldap'];

        $this->server = $this->ldap['server'];
        $this->filter = $this->ldap['filter'];
        $this->domain = $this->ldap['domain'];

        # Get these fields
        $this->fields = explode(',',$this->ldap['fields']);
        array_push($this->fields, $this->ldap['extraEmailField']);

        $this->conn = ldap_connect($this->server);

        if ( is_resource($this->conn) )
        {
            ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);

            $bound = ldap_bind($this->conn);

            if ( $bound )
            {
                # Create signature
                $this->add_hook('user2email', array($this, 'user2email'));
            }
            else
            {
                $log = sprintf("Bind to server '%s' failed. Con: (%s), Error: (%s)",
                    $this->server,
                    $this->conn,
                    ldap_errno($this->conn));
                write_log('identiteam', $log);
            }
        }
        else
        {
            $log = sprintf("Connection to the server failed: (Error=%s)", ldap_errno($this->conn));
            write_log('identiteam', $log);
        }
    }

    function user2email($args)
    {
        # load ldap confg
        $ldap = $this->ldap;

        # Open the connection and start to search
        $login = $args['user'];

        $filter = sprintf($ldap['filter'], $login);
        $result = ldap_search($this->conn, $this->domain, $filter, $this->fields);

        if ( $result )
        {
            $info = ldap_get_entries($this->conn, $result);
            $nbr = ldap_count_entries($this->conn, $result);

            if ( $info['count'] == 1 )
            {
                $userInfo = $info[0];
                $identities = array();
                $emails = array($userInfo["mail"][0]);

                $extraEmailField = $ldap['extraEmailField'];
                $extraEmails = $userInfo[$extraEmailField];

                if ( $extraEmails['count'] > 0 )
                {
                    $count = $extraEmails['count'];

                    for ( $e=0; $e < $count ; $e++ )
                    {
                        array_push($emails,$extraEmails[$e]);
                    }
                }

                foreach ( $emails as $email )
                {
                    $dict = array();
                    $keys = array_keys($userInfo);
                    foreach ( $keys as $key )
                    {
                        $dict["[$key]"] = $userInfo[$key][0];
                    }
                    $dict['[mail]']   = $email;

                    # prepare the arrays to replace into the signature template
                    $keys = array_keys($dict);
                    $vals = array_values($dict);

                    $tmplConfig = $this->config['templates'];
                    $baseDir = $tmplConfig['baseDir'];
                    $extension = $tmplConfig['extension'];
                    $folderName = $tmplConfig['folderName'];
                    $fileName = $tmplConfig['fileName'];
                    $formatString = $tmplConfig['formatString'];

                    if ( $folderName == '%d' || $fileName == '%u' )
                    {
                        list($user,$domain) = explode('@', $email);

                        if ( $folderName == '%d' ) $folderName = $domain ;
                        if ( $fileName == '%u' ) $fileName = $user ;
                    }

                    $signPath = sprintf($formatString, $baseDir, $folderName, $fileName, $extension);

                    if ( !file_exists($signPath) )
                    {
                        $log = sprintf("Signature template not found: (path=%s). Trying default in custom folder", $signPath);
                        write_log('identiteam', $log);
                        $signPath = sprintf("%s/default/default.%s", $baseDir, $extension);
                    }
                    if ( !file_exists($signPath) )
                    {
                        $log = sprintf("Signature template not found: (path=%s). Using default one", $signPath);
                        write_log('identiteam', $log);
                        $signPath = "plugins/identiteam/templates/default/default.tmpl";
                    }

                    # Create signature
                    if ( file_exists($signPath) )
                    {
                        $sign = file_get_contents($signPath);
                        $sign = str_replace($keys,$vals,$sign);

                        # remove empty fields from the signature
                        $repl = $tmplConfig['emptyValues'];
                        $sign = preg_replace('/\[[a-zA-Z]+\]/', $repl, $sign);

                        # If the signature start with an HTNL tag,
                        # it is automatically considered as an HTML signature.
                        $isHtml = 0;
                        if ( preg_match('/^\s*<[a-zA-Z]+/', $sign) )
                            $isHtml = 1;

                        $identities[] = array(
                            'email' => $email,
                            'name' => $userInfo['cn'][0],
                            'organization' => $userInfo['o'][0],
                            'reply-to' => '',
                            'signature' => $sign,
                            'html_signature' => $isHtml
                        );
                    }
                    else
                    {
                        $log = sprintf("Warning: signature template not found: (path=%s)", $signPath);
                        write_log('identiteam', $log);
                    }
                }

                $args['email'] = $identities;
            }
            else
            {
                $log = sprintf("User '%s' not found (pass 2). Filter: %s", $login, $filter);
                write_log('identiteam', $log);
            }
        }
        else
        {
            $log = sprintf("User '%s' not found (pass 1). Filter: %s", $login, $filter);
            write_log('identiteam', $log);
        }

        # We may close the connection before,
        # unless closing connection freed ressources
        # we use...
        ldap_close($this->conn);

        return $args;
    }

}
?>
