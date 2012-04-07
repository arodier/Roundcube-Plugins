<?php

// Default configuration settings for identiteam.
// Copy this file in config.inc.php, and override the values you need.

// Used to create a new user identity on the first login
$rcmail_config['identiteam'] =
array(

    // Mail parameters
    'mail' => array(
        'domain' => 'example.com',                          // Id necessary, you can specify a domain to add after the login name to search
                                                            // inside the ldap database. This is useful if you want to search based on email address
                                                            // rather than the simple uid.

        'dovecot_impersonate_seperator' => '*'              // If using dovecot master users, remove the admin name before the lookup

    ),

    // LDAP parameters
    'ldap' => array(
        'server' => 'ldap.snakeoil.com',                    // Your LDAP server address
        'filter_remove_domain' => true,                     // if set to true, the domain name (eg. xxxx@example.com) is removed before the lookup
        'filter' => '(uid=%s)' ,                            // The LDAP filter to use. This is compared with the user' login. use 'mail' if the domain is
                                                            // specified in the login process.
        'domain' => 'dc=snakeoil,dc=com',                   // LDAP Domain
        'extraEmailField' => 'gosamailalternateaddress',    // This LDAP property is used to create multiple identities with different email addresses.
         
        // Read these fields to insert them inside the signatures.
        'fields' => 'postaladdress,o,ou,cn,mail,sn,telephonenumber,mobile'
    ),

    // Templates parameters
    'templates' => array(
        'emptyValues' => '...',                       // When the value is not existing inside the signature, replace by that
        'formatString' => '%s/%s/%s.%s',              // The default format string to build signature path templates.
                                                      // The four parameters are specified below
        'baseDir' => 'plugins/identiteam/templates/', // Base directory where the templates are stored.
                                                      // The path is relative to roundcube folder,  use '/' as first character to use absolute path.
        'folderName' => 'default',                    // You can use also %d for domain name, the right part of the email address
        'fileName' => 'default',                      // You can use also %u for user name left of the email address
        'extension' => 'tmpl'                         // The default filenames extensions for signature files. Do not change the value unless needed
                                                      // HTML/TEXT is automatically detected from the file content.
    )
);



