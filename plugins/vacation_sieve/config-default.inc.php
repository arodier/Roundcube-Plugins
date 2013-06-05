<?php

/*
 +-----------------------------------------------------------------------+
 | Configuration file for vacation_sieve                                 |
 |                                                                       |
 | Copyright (C) 2011 André Rodier <andre.rodier@gmail.com>              |
 | Licensed under the GNU GPL                                            |
 +-----------------------------------------------------------------------+
 Note: Don't edit this file. Copy to config.php and override settins there
*/

$rcmail_config['vacation_sieve'] = array(

    # Message format: text or html
    'msg_format' => 'text',

    # Working Hours when selecting hours
    'working_hours' => array(8,18),

    # Debug logging: enable for process logging
    'debug' => false,

    # Transfer parameters: how to transfer your
    # sieve script on the server.
    'transfer' => array(
        # Transfer mode: local, ssh or managesieve.
        # local:
        #   simply copy the file in a local dir, but need permissions.
        # ssh:
        #   use ssh to transfer the file on a remote server,
        #   this is useful with dovecot sieve_before and sieve_after options
        # managesieve
        #   use the managesieve protocol to communicate with the mail server.
        #   need an appropriate mail server, like dovecot/cyrus or afterlogic

        # Select mode
        'mode' =>  'local',
        # 'mode' =>  'managesieve',
        # 'mode'   => 'ssh',

        # SSH Mode example
        # 'user'   => 'vmail',
        # 'host'   => 'localhost',
        # 'port'   => '22

        # Managesieve Mode example
        # 'ms_activate_script' => true,
        # if true, 'activate' the script via the managesieve protocol
        # if false, only write the script (eg if 'included')
        # 'port'   => '4190',
        # 'usetls' => false,

        # example of anonymous function used to transform the logon name
        # php 5.3+ needed to use this feature.
        #'logon_transform' => function($logon)
        #{
        #    return strtolower(preg_replace('#([a-z])[a-z]+\.([a-z])#', '\1\2', $logon));
        #},

        # example of a template path to save/load the local file
        # in the case of managesieve, this is the script name
        'path' => '/var/vmail/<domain>/<logon>/.sieve',

        # Path to sievec bin if necessary
        # 'sievecbin' => '/usr/bin/sievec',
        'sievecbin' => '',
    )
);

// end vacation config file
