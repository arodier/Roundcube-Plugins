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

    # Working Hours when selecting hours
    'working_hours' => array(8,18),

    # Message format: text or html
    'msg_format' => 'text',

    'logon_transform' => array('#([a-z])[a-z]+(\.|\s)([a-z])#i', '$1$3'),

    # Transfer parameters
    'transfer' => array(
        # Transfer mode: local, ssh, sieve, etc...
        # Only local supported atm

        # Select mode
        # 'mode' =>  'local',
        # 'mode' =>  'sieve',
        'mode'   => 'ssh',

        # Only used in SSH Mode
        'host'   => 'localhost',
        'user'   => 'vmail',

        # example of a template path to save/load the local file
        # 'path' => '/var/vmail/<domain>/<logon>/Maildir/.sieve',
        'path' => '/var/vmail/<domain>/<logon>/Mails/Sieve/After/01-Vacation.sieve'
    )
);

// end vacation config file
