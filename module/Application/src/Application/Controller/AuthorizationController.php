<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Session\Container;
use Application\Form\LoginForm;

class AuthorizationController extends AbstractActionController
{
    
    public $patientTable;
   
    public function getPatientTable()
    {
        if (!$this->patientTable) {
            $sm = $this->getServiceLocator();
            $this->patientTable = $sm->get('Patient\Model\PatientTable');
        }
        return $this->patientTable;
    }
    
   
    public function __construct() {
        $this->session = new Container('loginData');
    }
    public function loginAction()
    {       
     
    }
    
    public function logoutAction()
    {
        $this->session->getManager()->getStorage()->clear('loginData');
        $this->redirect()->toRoute('home');
        
    }
    
    public function patientAction()
    {
        
            $this->layout('layout/patient');
            $this->layout()->setVariable('patient_active', 'active');
        
         $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $email = $request->getPost('login');
            $pass  = $request->getPost('password');
            $result = $this->getPatientTable()->loginPatient($email,$pass);
           
           
            if ($result)
            {
                $this->session->login=true;
                $this->session->id           = $result->id;
                $this->session->name         = $result->name;
                $this->session->surname      = $result->surname;
                $this->session->email        = $result->email;
                $this->session->role         = 'patient';
                
                
                $this->redirect()->toRoute('patient');
                
            } else {
                echo 'błędny login / hasło';
            }
        }
        $form = new LoginForm();
        $form->setAttribute('action', $this->url()->fromRoute('autoryzacja',array('action'=>'patient')));
        $view = new ViewModel(array(
            'form'      =>  $form
        ));
        $view->setTemplate('application/authorization/login');
        
        return $view;
    }
    
   
}


