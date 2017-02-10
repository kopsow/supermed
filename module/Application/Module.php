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
use Zend\Session\SessionManager;
use Zend\Session\Container;

use Application\Model\Physician;
use Application\Model\PhysicianTable;

use Application\Model\Holidays;
use Application\Model\HolidaysTable;

use Application\Model\Users;
use Application\Model\UsersTable;

use Application\Model\Role;
use Application\Model\RoleTable;

use Application\Model\Scheduler;
use Application\Model\SchedulerTable;

use Application\Model\Days;
use Application\Model\DaysTable;

use Application\Model\Registration;
use Application\Model\RegistrationTable;

use Application\Model\Patient;
use Application\Model\PatientTable;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
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
                'Zend\Session\SessionManager' => function ($sm) {
                $config = $sm->get('config');
                if (isset($config['session'])) {
                    $session = $config['session'];

                    $sessionConfig = null;
                    if (isset($session['config'])) {
                        $class = isset($session['config']['class']) ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                        $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                        $sessionConfig = new $class();
                        $sessionConfig->setOptions($options);
                    }

                    $sessionStorage = null;
                    if (isset($session['storage'])) {
                        $class = $session['storage'];
                        $sessionStorage = new $class();
                    }

                    $sessionSaveHandler = null;

                    $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                } else {
                    $sessionManager = new SessionManager();
                }
                Container::setDefaultManager($sessionManager);
                return $sessionManager;
            }, 
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
                'Patient\Model\PatientTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('PatientTableGateway');
                    $table = new PatientTable($tableGateway);
                    return $table;
                },
                'PatientTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Patient());
                    return new TableGateway('patient', $dbAdapter, null, $resultSetPrototype);
                },
                'Registration\Model\RegistrationTable'  =>  function($sm)  {
                    $tableGateway = $sm->get('RegistrationTableGateway');
                    $table = new RegistrationTable($tableGateway);
                    return $table;
                },
                'RegistrationTableGateway' =>function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Registration());
                    return new TableGateway('registration', $dbAdapter, null, $resultSetPrototype);
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
                    $transport->setOptions(new \Zend\Mail\Transport\SmtpOptions($config['mail']['transport']));
                    return $transport;
                },
            ),
        );
    }
    
    public function bootstrapSession($e)
    {
        $session = $e->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
            $serviceManager = $e->getApplication()->getServiceManager();
            $request = $serviceManager->get('Request');

            $session->regenerateId(true);
            $container->init = 1;
            $container->remoteAddr = $request->getServer()->get('REMOTE_ADDR');
            $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');

            $config = $serviceManager->get('Config');
            if (!isset($config['session'])) {
                return;
            }

            $sessionConfig = $config['session'];
            if (isset($sessionConfig['validators'])) {
                $chain = $session->getValidatorChain();

                foreach ($sessionConfig['validators'] as $validator) {
                    switch ($validator) {
                        case 'Zend\Session\Validator\HttpUserAgent':
                            $validator = new $validator($container->httpUserAgent);
                            break;
                        case 'Zend\Session\Validator\RemoteAddr':
                            $validator = new $validator($container->remoteAddr);
                            break;
                        default:
                            $validator = new $validator();
                    }

                    $chain->attach('session.validate', array($validator, 'isValid'));
                }
            }
        }
    }
}
