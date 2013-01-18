<?php

/*
 +-----------------------------------------------------------------------+
 | Configuration file for vacation_sieve                                 |
 |                                                                       |
 | Copyright (C) 2011 André Rodier <andre.rodier@gmail.com>              |
 | Licensed under the GNU GPL                                            |
 +-----------------------------------------------------------------------+
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
        # 'mode' =>  'local',
        'mode' =>  'managesieve',
        # 'mode'   => 'ssh',

        # Used in SSH and Managesieve Mode
        'host'   => 'localhost',
        # SSH
        # 'port'   => '22',
        # Managesieve
        'port'   => '4190',
        # Only used in SSH Mode
        'user'   => 'vmail',
        # Only used in Managesieve Mode
        'usetls' => false,

        # example of a template path to save/load the local file
        # in the case of managesieve, this is the script name
        #'path' => '/var/vmail/<domain>/<logon>/.sieve',
        #'path' => '/tmp/<logon>.sieve',
        'path' => 'vacation',

        # Path to sievec bin
        'sievecbin' => '/usr/bin/sievec',
    )
);

// end vacation config file
