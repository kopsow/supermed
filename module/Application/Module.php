<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Application\Model\Physician;
use Application\Model\PhysicianTable;

use Application\Model\Holidays;
use Application\Model\HolidaysTable;

use Application\Model\Role;
use Application\Model\RoleTable;


use Application\Model\Scheduler;
use Application\Model\SchedulerTable;

use Application\Model\Days;
use Application\Model\DaysTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
       
        return array(
            'factories' => array(
                'Physician\Model\PhysicianTable' =>  function($sm) {
                    $tableGateway = $sm->get('PhysicianTableGateway');
                    $table = new PhysicianTable($tableGateway);
                    return $table;
                },
                'PhysicianTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Physician());
                    return new TableGateway('physician', $dbAdapter, null, $resultSetPrototype);
                },  
                'Holidays\Model\HolidaysTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('HolidaysTableGateway');
                    $table = new HolidaysTable($tableGateway);
                    return $table;
                },
                'HolidaysTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Holidays());
                    return new TableGateway('holidays', $dbAdapter, null, $resultSetPrototype);
                },
                'Users\Model\UsersTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('UsersTableGateway');
                    $table = new UsersTable($tableGateway);
                    return $table;
                },
                'UsersTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Users());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                'Role\Model\RoleTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('RoleTableGateway');
                    $table = new RoleTable($tableGateway);
                    return $table;
                },
                'RoleTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Role());
                    return new TableGateway('role', $dbAdapter, null, $resultSetPrototype);
                },
                'Days\Model\DaysTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('DaysTableGateway');
                    $table = new DaysTable($tableGateway);
                    return $table;
                },
                'DaysTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Days());
                    return new TableGateway('days', $dbAdapter, null, $resultSetPrototype);
                },
                'Scheduler\Model\SchedulerTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('SchedulerTableGateway');
                    $table = new SchedulerTable($tableGateway);
                    return $table;
                },
                'SchedulerTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Scheduler());
                    return new TableGateway('scheduler', $dbAdapter, null, $resultSetPrototype);
                },
                'mail.transport' => function ($sm) {
                    $config = $sm->get('Config');
                    $transport = new \Zend\Mail\Transport\Smtp();                
                    $transport->setOptions(new \Zend\Mail\Transport\SmtpOptions($config['mail']['transport']['options']));
                    return $transport;
                },
            ),
        );
    }
    
}
