<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=supermed;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'mail' => array(
    'transport' => array(
        'options' => array(
           'name'              => 'NetMed',
            'host'              => 'super-med.pl',
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'kontakt@super-med.pl',
                'password' => 'AoT7kIhf',
            ),
        ),  
    ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'module_layouts'  => array(
        'Application'  => 'layout/default',
        'Pacjent'      => 'layout/default',
        'Admin'        => 'layout/admin' 
    ),
);
