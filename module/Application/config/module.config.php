<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),           
            'patient' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/pacjent[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Patient',
                        'action'     => 'index',
                    ),
                ),
            ),
            'verified' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/autoryzacja/verified[/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Authorization',
                        'action'     => 'verified',
                    ),
                ),
            ),
            'scheduler' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/harmonogram',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Scheduler',
                        'action'     => 'index',
                    ),
                ),
            ),
            'schedulerAdd' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/harmonogram/dodaj',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Scheduler',
                        'action'     => 'add',
                    ),
                ),
            ),
            'schedulerShow' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/harmonogram/lista[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Scheduler',
                        'action'     => 'show',
                    ),
                ),
            ),
            
            'physician' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/lekarz[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Physician',
                        'action'     => 'index',
                    ),
                ),
            ),
            'registration' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/rejestracja[/:action][/:param]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Registration',
                        'action'     => 'index',
                    ),
                ),
            ),
            'registrationAdd' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/rejestracja/add',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Registration',
                        'action'     => 'add',
                    ),
                ),
            ),
            'registrationShow' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/rejestracja/lista[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Registration',
                        'action'     => 'show',
                    ),
                ),
            ),
            
            'administrator' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/administrator[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Administrator',
                        'action'     => 'index',
                    ),
                ),
            ),
            'autoryzacja' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/autoryzacja[/:action][/:source]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Authorization',
                        'action'     => 'patient',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/rejestratorka[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Register',
                        'action'     => 'index',
                    ),
                ),
            ),
            'ajax' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/ajax[/][/:action]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Ajax',
                        'action'     => 'index',                        
                    ),
                    
                ),
            ),
            'test' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/test[/][/:action]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Test',
                        'action'     => 'index',                        
                    ),
                    
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'pl_PL',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Ajax' => 'Application\Controller\AjaxController',
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Scheduler' => 'Application\Controller\SchedulerController',
            'Application\Controller\Patient' => 'Application\Controller\PatientController',
            'Application\Controller\Physician' => 'Application\Controller\PhysicianController',
            'Application\Controller\Registration' => 'Application\Controller\RegistrationController',
            'Application\Controller\Administrator' => 'Application\Controller\AdministratorController',
            'Application\Controller\Authorization' => 'Application\Controller\AuthorizationController',
            'Application\Controller\Register' => 'Application\Controller\RegisterController',
            'Application\Controller\Test' => 'Application\Controller\TestController',
        ),
    ),
    'view_manager' => array(
        'strategies'               => array('ViewJsonStrategy'),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'email/cancel'            => __DIR__ . '/../view/email/cancel.phtml',
            'email/layout'            => __DIR__ . '/../view/email/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
