<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Session\Container;
use Application\Form\LoginForm;

class AuthorizationController extends AbstractActionController
{
    
    public $patientTable;
    public $physicianTable;
    public $usersTable;
   
    public function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Patient\Model\PatientTable');
        }
        return $this->patientTable;
    }
    
    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
    }
    
    public function getPhysicianTable()
    {
        if (!$this->physicianTable) {
            $sm = $this->getServiceLocator();
            $this->physicianTable = $sm->get('Physician\Model\PhysicianTable');
        }
        return $this->physicianTable;
    }
   
    public function __construct() {
        $this->session = new Container('loginData');
    }
    
    
    public function logoutAction()
    {
        $this->session->getManager()->getStorage()->clear('loginData');
        $this->redirect()->toRoute('home');
        
    }
    public function adminAction()
    {
        $message = null;
        $request = $this->getRequest();
        $this->layout()->setVariable('admin_active', 'active');
        
        if ($request->isPost())
        {
            $email = $request->getPost('login');
            $pass  = $request->getPost('password');
            $result = $this->getUsersTable()->loginUsers($email,$pass);
            
           
            if ($result)
            {
                $this->session->login=true;
                $this->session->id           = $result->id;
                $this->session->name         = $result->name;
                $this->session->surname      = $result->surname;
                $this->session->email        = $result->email;
                $this->session->role         = 'admin';
                
                $this->redirect()->toRoute('patient'); 
            
            } else {
                $message = 'Błędny login lub hasło';
                
            }
        }
        
        $form = new LoginForm();
        $form->setAttribute('action', $this->url()->fromRoute('autoryzacja',array('action'=>'admin')));
        $view = new ViewModel(array(
            'form'          =>  $form,
            'patientMessage'  =>  'Logujesz się jako ADMINISTRATOR',
            'loginMessage'  =>  $message,
            'mail'         =>  $request->getPost('login')
        ));
        $view->setTemplate('application/authorization/login');
        
        return $view;
    }
    public function patientAction()
    {
        $message=NULL;
           
        $this->layout()->setVariable('patient_active', 'active');
        
         $request = $this->getRequest();
       
        if ($request->isPost())
        {
            $email = $request->getPost('login');
            $pass  = $request->getPost('password');
            $result = $this->getPatientTable()->loginPatient($email,$pass);
            
           
            if ($result && $result->verified)
            {
                $this->session->login=true;
                $this->session->id           = $result->id;
                $this->session->name         = $result->name;
                $this->session->surname      = $result->surname;
                $this->session->email        = $result->email;
                $this->session->role         = 'patient';
                
                $this->redirect()->toRoute('patient'); 
            } elseif($result && $result->verified === NULL) {
                $message = 'Konto nie zostało zweryfikowane';
                $this->session->email = $result->email;
            } else {
                $message = 'Błędny login lub hasło';
                
            }
        }
        $form = new LoginForm();
        $form->setAttribute('action', $this->url()->fromRoute('autoryzacja',array('action'=>'patient')));
        $view = new ViewModel(array(
            'form'          =>  $form,
            'patientMessage'  =>  'Logujesz się jako PACJENT',
            'loginMessage'  =>  $message,
            'mail'         =>  $request->getPost('login')
        ));
        $view->setTemplate('application/authorization/login');
        
        return $view;
    }
    
    public function registerAction()
    {
        $this->layout()->setVariable('register_active', 'active');
        $message = null ;
        $request = $this->getRequest();
        $form = new LoginForm();
        $form->setAttribute('action', $this->url()->fromRoute('autoryzacja',array('action'=>'register')));
        
        if ($request->isPost())
        {
            $email = $request->getPost('login');
            $pass  = $request->getPost('password');
           
            $result = $this->getUsersTable()->loginUsers($email,$pass);
           
           
            if ($result)
            {
                $this->session->login=true;
                $this->session->id           = $result->id;
                $this->session->name         = $result->name;
                $this->session->surname      = $result->surname;
                $this->session->email        = $result->email;
                $this->session->role         = 'register';
                
               $this->redirect()->toRoute('register'); 
                
            } else {
                $message = 'Błędny login lub hasło';
                
            }
        }
        
        $view = new ViewModel(array(
            'form'              =>  $form,
            'loginMessage'      =>  $message,
            'physicianMessage'  =>  'Logujesz się jako REJESTRATOR/KA',
            'mail'         =>  $request->getPost('login')
        ));
        $view->setTemplate('application/authorization/login');
        
        return $view;
    }
    public function physicianAction()
    {
        $this->layout()->setVariable('physician_active', 'active');
        $message = null ;
        $request = $this->getRequest();
        $form = new LoginForm();
        $form->setAttribute('action', $this->url()->fromRoute('autoryzacja',array('action'=>'physician')));
        
        if ($request->isPost())
        {
            $email = $request->getPost('login');
            $pass  = $request->getPost('password');
           
            $result = $this->getPhysicianTable()->loginPhysician($email,$pass);
           
           
            if ($result)
            {
                $this->session->login=true;
                $this->session->id           = $result->id;
                $this->session->name         = $result->name;
                $this->session->surname      = $result->surname;
                $this->session->email        = $result->email;
                $this->session->role         = 'physician';
                
               $this->redirect()->toRoute('physician'); 
                
            } else {
                $message = 'Błędny login lub hasło';
                
            }
        }
        
        
        
        $view = new ViewModel(array(
            'form'              =>  $form,
            'loginMessage'      =>  $message,
            'physicianMessage'  =>  'Logujesz się jako LEKARZ',
            'mail'         =>  $request->getPost('login')
        ));
        $view->setTemplate('application/authorization/login');
        
        return $view;
           
    }
    public function verifiedAction()
    {
       
        $id = (int) $this->params()->fromRoute('source');
        $this->getPatientTable()->verifiedPatient($id);
        $this->redirect()->toRoute('patient');
    }
    
   
}


