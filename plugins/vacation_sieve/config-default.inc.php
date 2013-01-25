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
    # 'html' is not implemented yet.
    'msg_format' => 'text',

    # Debug logging: enable for process logging
    'debug' => false,

    # Transfer parameters
    'transfer' => array(
        # Transfer mode: local, ssh, managesieve, etc...

        # Select mode
        'mode' =>  'local',
        # 'mode' =>  'managesieve',
        # 'mode'   => 'ssh',

        # Used in SSH and Managesieve Mode
        # 'host'   => 'localhost',
        # 'port'   => '22', # SSH
        # 'port'   => '4190', # Managesieve
        # Only used in SSH Mode
        # 'user'   => 'vmail',
        # Only used in Managesieve Mode
        # 'usetls' => false,

        # Only used in Managesieve Mode
        # if true, 'activate' the script via the managesieve protocol
        # if false, only write the script (eg if 'included')
        # 'ms_activate_script' => true,

        # example of a template path to save/load the local file
        # in the case of managesieve, this is the script name
        'path' => '/var/vmail/<domain>/<logon>/.sieve',
        # 'path' => 'vacation',

        # Path to sievec bin
        'sievecbin' => '/usr/bin/sievec',
    )
);

// end vacation config file
