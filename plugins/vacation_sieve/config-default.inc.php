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

    # Date formats for the calendars
    'date_format' => 'd/m/Y',

    # Message format: text or html
    # 'html' is not implemented yet.
    'msg_format' => 'text',

    # Transfer parameters
    'transfer' => array(
        # Transfer mode: local, ssh, sieve, etc...
        # Only local and SSH supported atm

        # Select mode
        # 'mode' =>  'local',
        # 'mode' =>  'sieve',
        'mode'   => 'ssh',

        # Only used in SSH Mode
        'host'   => 'localhost',
        'user'   => 'vmail',

        # example of a template path to save/load the local file
        'path' => '/var/vmail/<domain>/<logon>/Maildir/.sieve',
    )
);

// end vacation config file
